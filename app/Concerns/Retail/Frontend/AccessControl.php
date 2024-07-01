<?php

namespace App\Concerns\Retail\Frontend;

use App\Models\Core\Wallet;

trait AccessControl
{
    public string $walletCode = '';

    public ?Wallet $wallet = null;

    public function updatedWalletCode(): void
    {
        match (filled($this->walletCode)) {
            false => $this->resetCustomer(),
            true => $this->searchWallet(),
        };
    }

    public function searchWallet(): void
    {
        $wallet = Wallet::where('code', $this->walletCode)->first();

//        dd($wallet);
        match (blank($wallet)) {
            false => $this->wallet = $wallet,
            true => $this->resetCustomer(),
        };

        $this->loadPaymentModes();
    }

    public function resetCustomer(): void
    {
        $this->walletCode = '';
        $this->wallet = null;
    }

    public function cancel(): void
    {
        $this->recordedPayments = collect();
        $this->saleItems = collect();
        $this->selectedMode = null;
        $this->searchPortions = '';
        $this->mpesaReceipt = null;
        $this->tenderedAmount = 0;
        $this->walletCode = '';
        $this->itemCode = '';
        $this->totalPaid = 0;
        $this->saleTotal = 0;
        $this->wallet = null;
        $this->balance = 0;
        $this->change = 0;
    }
}
