<?php

namespace App\Concerns\Retail\Frontend\Payments;

use Livewire\Attributes\Validate;

trait RecordPayment
{
    #[Validate('required|numeric|min:1')]
    public $tenderedAmount;

    #[Validate('required|numeric|min_digits:10|starts_with:0|max_digits:10')]
    public string $phoneNumber;

    #[Validate('required|string|max:20')]
    public ?string $mpesaReceipt;

    public function recordPayment(): void
    {
        $one = blank($this->selectedMode);

        if (or_check($one, (int) $this->balance === 0)) {
            return;
        }

        $this->validate($this->fetchRules());

        $paid = min($this->balance, $this->tenderedAmount);
        $this->totalPaid += $this->tenderedAmount;
        $this->recalculate();

        $this->recordedPayments->push([
            'id' => count($this->recordedPayments) + 1,
            'tendered_amount' => $this->tenderedAmount,
            'reference' => $this->getReference(),
            'mode' => $this->selectedMode,
            'paid_amount' => $paid,
        ]);

        $this->tenderedAmount = $this->selectedMode = null;
        $this->dispatch('close-payment-mode-modal');
    }

    private function fetchRules(): array
    {
        $rule = 'required|numeric|min_digits:10|starts_with:0|max_digits:10';
        $extra = match ($this->selectedMode) {
            'Mpesa-Offline' => ['mpesaReceipt' => 'required|string|max:20'],
            'Mpesa' => ['phoneNumber' => $rule],
            default => []
        };

        $rules = ['tenderedAmount' => 'required|numeric|min:1'];

        return array_merge($rules, $extra);
    }

    private function getReference(): ?string
    {
        $ref = $this->selectedMode . $this->user?->user_number;

        return match ($this->selectedMode) {
            'Wallet', 'Allowance' => $ref,
            'Mpesa-Offline' => $this->mpesaReceipt,
            default => $this->selectedMode
        };
    }
}
