<?php

namespace App\Models\Retail;

use App\Concerns\BelongsToTeam;
use App\Models\Accounting\PaymentMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends Model
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

    public function customer(): MorphTo
    {
        return $this->morphTo('customer');
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    protected function casts(): array
    {
        return [
            'consumed_at' => 'datetime',
        ];
    }
}
