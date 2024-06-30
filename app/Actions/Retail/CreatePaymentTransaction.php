<?php

namespace App\Actions\Retail;

use App\Models\Retail\PaymentAllocation;
use App\Models\Retail\PaymentTransaction;
use App\Models\Retail\Sale;
use App\Models\User;
use Illuminate\Support\Collection;
use Throwable;

class CreatePaymentTransaction
{
    public function execute(
        Sale $sale,
        Collection $payments,
        ?User $user = null
    ): void {
        $items = collect();

        $payments->each(
            /**
             * @throws Throwable
             */
            fn ($item) => $this->process($item, $sale, $user)
        );

        PaymentTransaction::insert($items->toArray());
    }

    /**
     * @throws Throwable
     */
    private function process(array $item, Sale $sale, ?User $user = null): void
    {
        $mode = payment_mode($item['mode']);

        //        if ($item['mode'] === 'Mpesa') {
        //            $entry = PaymentTransaction::where('code', '=', $item['reference'])
        //                ->first();
        //
        //            throw_if(blank($entry), new Exception('Not found')); // todo:
        //        }

        $transaction = PaymentTransaction::create([
            'balance' => $item['tendered_amount'] - $item['paid_amount'],
            'narration' => $this->fetchNarration($item, $sale),
            'team_id' => $sale->getAttribute('team_id'),
            'tendered_amount' => $item['tendered_amount'],
            'customer_type' => $user?->getMorphClass(),
            'paid_amount' => $item['paid_amount'],
            'customer_id' => $user?->getKey(),
            'payment_mode_id' => $mode->id,
            'code' => $item['reference'],
            'sale_id' => $sale->id,
            'is_valid' => true,
        ]);

        $this->createPaymentAllocation($transaction, $item);
    }

    private function fetchNarration(array $item, Sale $sale): string
    {
        return build_string([
            'Payment of Ksh.' . number_format($item['paid_amount']) .
            ' against Sale ID:' . $sale->id,
        ]);
    }

    private function createPaymentAllocation(
        PaymentTransaction $transaction,
        array $item
    ): void {
        PaymentAllocation::create([
            'team_id' => $transaction->getAttribute('team_id'),
            'payment_mode_id' => payment_mode($item['mode'])->id,
            'payment_transaction_id' => $transaction->id,
            'narration' => $transaction->narration,
            'sale_id' => $transaction->sale_id,
            'amount' => $item['paid_amount'],
        ]);
    }
}
