<?php

namespace App\Actions\Retail;

use App\Models\Core\Wallet;
use App\Models\Retail\Sale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubmitNewSale
{
    private Collection $payments;

    private ?Wallet $wallet = null;

    private Collection $items;

    private Sale $sale;

    private function setup(
        Collection $items,
        Collection $payments,
        ?Wallet $wallet = null,
    ): void {
        $this->payments = $payments;
        $this->items = $items;
        $this->wallet = $wallet;
    }

    /**
     * @throws Throwable
     */
    public function execute(
        Collection $items,
        Collection $payments,
        ?Wallet $wallet = null,
    ): void {
        $this->setup($items, $payments, $wallet);
        //dd($payments);
        try {
            DB::transaction(function (): void {
                $this->createSale();
                $this->createItems();
                $this->createPaymentTransaction();
                $this->reduceStock();

                //            dd(PaymentAllocation::all());
            });
        } catch (Throwable $exception) {
            Log::error($exception);
            throw $exception;
        }
    }

    /**
     * @throws Throwable
     */
    public function reduceStock(): void
    {
        (new ReduceStock())->execute($this->sale);
    }

    private function createSale(): void
    {
        $payments = $this->payments;
        $items = $this->items;

        $class = new CreateSale();
        $this->sale = $class->execute($items, $payments, $this->wallet);
    }

    private function createItems(): void
    {
        (new CreateSaleItems())->execute($this->sale, $this->items);
    }

    private function createPaymentTransaction(): void
    {
        $class = new CreatePaymentTransaction();
        $class->execute($this->sale, $this->payments, $this->wallet);
    }
}
