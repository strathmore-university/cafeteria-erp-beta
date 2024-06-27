<?php

namespace App\Livewire;

use App\Models\Production\SellingPortion;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

class PosInterface extends Component
{
    public $user_number = '';

    public $searchPortions = '';

    public $attemptAddItem = '';

    public ?string $selectedMode = null;

    public ?string $reference = null;

    public ?User $user = null;

    public Collection $allSellingPortions;

    public Collection $sellingPortions;

    public Collection $saleItems;

    public Collection $recordedPayments;

    public array $paymentModes;

    public float $totalDue = 0;

    public float $totalPaid = 0;

    public float $balance = 0;

    public ?float $recordedAmount = null;

    public string $balanceLabel;

    public function mount(): void
    {
        $this->allSellingPortions = collect();
        $this->recordedPayments = collect();
        $this->saleItems = collect();
    }

    #[Layout('pos.pos')]
    public function render(): View
    {
        $key = 'sellingPortions';
        //        Cache::forget($key);
        $this->allSellingPortions = Cache::remember($key, now()->addHour(), function () {
            return SellingPortion::with('menuItem')
                ->whereHas('menuItem', function (Builder $builder): void {
                    $builder->where('menu_id', '=', 1);
                })->get();
        });

        $this->sellingPortions = $this->allSellingPortions;

        if (filled($this->searchPortions)) {
            $search = $this->searchPortions;

            $this->sellingPortions = $this->sellingPortions->filter(function ($portion) use ($search) {
                $name = $portion->menuItem->getAttribute('name');

                return
                    Str::contains($portion->code, $search) ||
                    Str::contains(mb_strtolower($name), mb_strtolower($search));
            });
        }

        $this->paymentModes = $this->loadPaymentModes();

        $this->balance = $this->totalDue - $this->totalPaid;
        $this->balanceLabel = $this->balance > 0 ? 'Due' : 'Change';

        //        $this->saleItems->push([
        //            'name' => 'Chicken Curry',
        //            'code' => '123',
        //            'quantity' => 1,
        //            'price' => '800',
        //            'units' => 1,
        //        ]);
        //
        //        $this->saleItems->push([
        //            'name' => 'Chicken Curry',
        //            'code' => '123',
        //            'quantity' => 1,
        //            'price' => '800',
        //            'units' => 1,
        //        ]);
        //
        //        $this->saleItems->push([
        //            'name' => 'Chicken Curry',
        //            'code' => '123',
        //            'quantity' => 1,
        //            'price' => '800',
        //            'units' => 1,
        //        ]);

        return view('livewire.pos-interface');
    }

    public function updatedUserNumber(): void
    {
        if (filled($this->user_number)) {
            $this->searchUser();

            return;
        }

        $this->resetCustomer();
    }

    public function searchUser(): void
    {
        $user = User::where('name', $this->user_number)->first();

        if (blank($user)) {
            $this->resetCustomer();

            return;
        }

        $this->user = $user;
    }

    public function attempt(): void
    {
        $portion = $this->allSellingPortions
            ->firstWhere('code', '=', $this->attemptAddItem);

        if (blank($portion)) {
            $this->searchPortions = $this->attemptAddItem;

            $this->dispatch('open-items-modal');

            return;
        }

        $this->addSaleItem($portion);

        $this->attemptAddItem = '';
    }

    public function addFromSelect(SellingPortion $portion): void
    {
        $this->addSaleItem($portion);

        $this->dispatch('close-items-modal');

        $this->attemptAddItem = '';
    }

    public function addSaleItem(SellingPortion $portion): void
    {
        $a = $this->allSellingPortions->firstWhere('code', '=', $portion->code);

        // Check if the item already exists in the saleItems collection
        $existingItem = $this->saleItems->firstWhere('code', $portion->code);

        if ($existingItem) {
            // If the item exists, increment the quantity and units
            $this->saleItems = $this->saleItems->map(function ($item) use ($portion) {
                if ($item['code'] === $portion->code) {
                    $item['quantity']++;
                    $item['units']++;
                    $this->totalDue += $item['price'];
                }

                return $item;
            });
        } else {
            // If the item does not exist, add it to the collection
            $this->saleItems->push([
                'name' => $a->menuItem->name,
                'code' => $portion->code,
                'quantity' => 1,
                'price' => $portion->selling_price,
                'units' => 1,
            ]);

            $this->totalDue += $portion->selling_price;
        }
    }

    public function increase(string $code): void
    {
        $this->saleItems = $this->saleItems->map(function ($saleItem) use ($code) {
            if ($saleItem['code'] === $code) {
                $saleItem['units'] = ++$saleItem['units'];
                $this->totalDue += $saleItem['price'];
            }

            return $saleItem;
        });
    }

    public function decrease(string $code): void
    {
        $this->saleItems = $this->saleItems->map(function ($saleItem) use ($code) {
            if ($saleItem['code'] === $code) {
                if ($saleItem['units'] !== 1) {
                    $saleItem['units'] = --$saleItem['units'];
                    $this->totalDue -= $saleItem['price'];
                }
            }

            return $saleItem;
        });
    }

    public function remove(string $code): void
    {
        $this->saleItems = $this->saleItems->filter(function ($item) use ($code) {
            if ($item['code'] === $code) {
                $total = $item['units'] * $item['price'];

                $this->totalDue -= $total;
            }

            return $item['code'] !== $code;
        });
    }

    public function selectPaymentMode(string $mode): void
    {
        if ( ! in_array($mode, $this->paymentModes)) {
            $this->selectedMode = null;

            return;
        }

        $this->selectedMode = $mode;
    }

    public function showPhoneNumber(): bool
    {
        return $this->selectedMode === 'mpesa';
    }

    public function showAmount(): bool
    {
        return filled($this->selectedMode);
    }

    public function canSubmitPayment(): bool
    {
        $one = filled($this->recordedAmount) && $this->recordedAmount > 0;

        return filled($this->selectedMode) && $this->selectedMode !== 'mpesa' && $one;
    }

    public function mpesaOfflineModeSelected(): bool
    {
        return $this->selectedMode === 'mpesa-offline';
    }

    public function recordPayment(): void
    {
        $this->validate([
            'recordedAmount' => 'required|numeric|min:1',
            'reference' => 'sometimes|string|max:20|nullable',
            'selectedMode' => 'sometimes|string',
        ]);

        $reference = match ($this->selectedMode) {
            'mpesa-offline' => $this->reference,
            default => $this->selectedMode
        };

        $this->recordedPayments->push([
            'amount' => $this->recordedAmount,
            'mode' => $this->selectedMode,
            'reference' => $reference,
        ]);

        $this->totalPaid += $this->recordedAmount;

        $this->dispatch('close-payment-mode-modal');

        $this->recordedAmount = null;

        $this->selectedMode = null;
    }

    public function updatingRecordedAmount(): void
    {
        if ( ! is_numeric($this->recordedAmount)) {
            $this->recordedAmount = null;
            // todo:
        }
    }

    public function resetCustomer(): void
    {
        $this->user_number = null;
        $this->user = null;
    }

    public function cancel(): void
    {
        $this->recordedPayments = collect();
        $this->saleItems = collect();
        $this->recordedAmount = null;
        $this->selectedMode = null;
        $this->searchPortions = '';
        $this->attemptAddItem = '';
        $this->user_number = null;
        $this->reference = null;
        $this->totalPaid = 0;
        $this->totalDue = 0;
        $this->user = null;
        $this->balance = 0;
    }

    private function loadPaymentModes(): array
    {
        // todo: fetch from table the enabled modes

        $modes = [
            'cash', 'bank', 'mpesa', 'mpesa-offline',
            'e-wallet', 'meal allowance',
        ];

        if (blank($this->user)) {
            $remove = ['e-wallet', 'meal allowance'];

            $modes = array_diff($modes, $remove);
        }

        return $modes;
    }
}
