<?php

namespace App\Actions\Retail;

use App\Models\Retail\Sale;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SubmitNewSale
{
    private Collection $payments;

    private ?User $user = null;

    private Collection $items;

    private Sale $sale;

    private function setup(
        Collection $items,
        Collection $payments,
        ?User $user = null,
    ): void {
        $this->payments = $payments;
        $this->items = $items;
        $this->user = $user;
    }

    /**
     * @throws Throwable
     */
    public function execute(
        Collection $items,
        Collection $payments,
        ?User $user = null,
    ): void {
        $this->setup($items, $payments, $user);
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

    public function reduceStock(): void
    {
        (new ReduceStock())->execute($this->sale, $this->items);
    }

    private function createSale(): void
    {
        $payments = $this->payments;
        $items = $this->items;

        $class = new CreateSale();
        $this->sale = $class->execute($items, $payments, $this->user);
    }

    private function createItems(): void
    {
        (new CreateSaleItems())->execute($this->sale, $this->items);
    }

    private function createPaymentTransaction(): void
    {
        $class = new CreatePaymentTransaction();
        $class->execute($this->sale, $this->payments, $this->user);
    }
}
