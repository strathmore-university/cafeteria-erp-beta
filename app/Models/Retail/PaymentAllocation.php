<?php

namespace App\Models\Retail;

use App\Concerns\BelongsToTeam;
use App\Models\Accounting\PaymentMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentAllocation extends Model
{
    use BelongsToTeam, SoftDeletes;

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function mode(): BelongsTo
    {
        return $this->belongsTo(PaymentMode::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PaymentTransaction::class);
    }
}
