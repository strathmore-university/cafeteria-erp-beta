<?php

namespace App\Livewire;

use App\Concerns\Retail\Frontend\AccessControl;
use App\Concerns\Retail\Frontend\Items\SaleItems;
use App\Concerns\Retail\Frontend\Payments\Payments;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

class PosInterface extends Component
{
    use AccessControl, Payments, SaleItems;

    public function mount(): void
    {
        //        Cache::clear();
        $this->allSellingPortions = collect();
        $this->recordedPayments = collect();
        $this->saleItems = collect();
        $this->loadPaymentModes();
        $this->loadPortions();
    }
    //    public function getListeners(): array
    //    {
    //        return [
    //            "echo:App.Models.User.1,Anthony" => 'test',
    //        ];
    //    }

    #[On('echo:test,Anthony')]
    //    #[On('echo:orders.{order.id},OrderShipped')]
    public function test(): void
    {
        $this->selectedMode = 'Cash';
        $this->tenderedAmount = 100;
        $this->recordPayment();
        //        $this->user = User::first();
    }

    public function handleTestEvent($event): void
    {
        $this->selectedMode = 'Cash';
        $this->tenderedAmount = 100;
        $this->recordPayment();
    }

    #[Layout('components.pos.Layout.pos')]
    public function render(): View
    {
//        $this->addFromSelect($this->allSellingPortions->first());
//        $this->addFromSelect($this->allSellingPortions->first());
//        $this->addFromSelect($this->allSellingPortions->last());
//        $this->itemCode = '963';
//        $this->addItemByCode();
//
//        $this->itemCode = '963';
//        $this->addItemByCode();
//
//        $this->user = User::first();
//                $this->selectedMode = 'Cash';
//                $this->tenderedAmount = 100;
//                $this->recordPayment();
//
//                $this->selectedMode = 'Wallet';
//                $this->tenderedAmount = 1300;
//                $this->recordPayment();
//        //
//                $this->selectedMode = 'Allowance';
//                $this->tenderedAmount = 100;
//                $this->recordPayment();

        //        $this->selectedMode = 'Mpesa-Offline';
        //        $this->tenderedAmount = 100;
        //        $this->mpesaReceipt = 'gffgd';
        //        $this->recordPayment();

        //        $this->selectedMode = 'Mpesa';
        //        $this->tenderedAmount = 100;
        //        $this->mpesaReceipt = 'sdgsgs';
        //        $this->phoneNumber = '0700616911';
        //        $this->recordPayment();
        //
        //$this->submitSale();

        return view('livewire.pos-interface');
    }
}
