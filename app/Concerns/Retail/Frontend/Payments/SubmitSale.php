<?php

namespace App\Concerns\Retail\Frontend\Payments;

use App\Actions\Retail\SubmitNewSale;
use Throwable;

trait SubmitSale
{
    public function canSubmit(): bool
    {
        $one = filled($this->recordedPayments);
        $two = filled($this->saleItems);
        $three = $this->balance === 0;

        return and_check(and_check($one, $two), $three);
    }

    /**
     * @throws Throwable
     */
    public function submitSale(): void
    {
        if ( ! $this->canSubmit()) {
            return;
        }

        $payments = $this->recordedPayments;
        (new SubmitNewSale())->execute($this->saleItems, $payments, $this->user);

        //        todo: notification

        $this->cancel();
    }
}
