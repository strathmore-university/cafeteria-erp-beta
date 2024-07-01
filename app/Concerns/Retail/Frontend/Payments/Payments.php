<?php

namespace App\Concerns\Retail\Frontend\Payments;

use Illuminate\Support\Collection;

trait Payments
{
    use RecordPayment, SubmitSale;

    public ?string $selectedMode = null;

    public Collection $recordedPayments;

    public float $totalPaid = 0;

    public array $paymentModes;

    public float $saleTotal = 0;

    public float $balance = 0;

    public float $change = 0;

    public function selectPaymentMode(string $mode): void
    {
        $this->loadPaymentModes();

        $check = in_array($mode, $this->paymentModes);
        $this->selectedMode = match ($check) {
            true => $mode,
            false => null,
        };

        // todo: if wallet the load to max of the available balance
        $this->tenderedAmount = $this->balance;
    }

    public function removePayment(int $id): void
    {
        $items = $this->recordedPayments;
        $this->recordedPayments = $items->filter(function ($item) use ($id) {
            if ($item['id'] === $id) {
                $this->totalPaid -= $item['tendered_amount'];
                $this->recalculate();
            }

            return $item['id'] !== $id;
        });
    }

    public function recalculate(): void
    {
        $balance = $this->saleTotal - $this->totalPaid;
        $this->balance = max($balance, 0);

        $change = $this->totalPaid - $this->saleTotal;
        $this->change = max($change, 0);
    }

    private function loadPaymentModes(): void
    {
        $modes = payment_modes()->pluck('name', 'id')->toArray();

        $this->paymentModes = match (filled($this->wallet)) {
            false => array_diff($modes, ['Wallet', 'Allowance']),
            true => $modes,
        };
    }
}
