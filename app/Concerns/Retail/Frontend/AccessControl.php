<?php

namespace App\Concerns\Retail\Frontend;

use App\Models\User;

trait AccessControl
{
    public string $user_number = '';

    public ?User $user = null;

    public function updatedUserNumber(): void
    {
        match (filled($this->user_number)) {
            false => $this->resetCustomer(),
            true => $this->searchUser(),
        };
    }

    public function searchUser(): void
    {
        $user = User::where('name', $this->user_number)->first();

        match (blank($user)) {
            true => $this->resetCustomer(),
            false => $this->user = $user,
        };

        $this->loadPaymentModes();
    }

    public function resetCustomer(): void
    {
        $this->user_number = '';
        $this->user = null;
    }

    public function cancel(): void
    {
        $this->recordedPayments = collect();
        $this->saleItems = collect();
        $this->selectedMode = null;
        $this->searchPortions = '';
        $this->mpesaReceipt = null;
        $this->tenderedAmount = 0;
        $this->user_number = '';
        $this->itemCode = '';
        $this->totalPaid = 0;
        $this->saleTotal = 0;
        $this->user = null;
        $this->balance = 0;
        $this->change = 0;
    }
}
