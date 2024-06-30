<?php

namespace App\Models\Retail;

use App\Concerns\BelongsToTeam;
use App\Models\Production\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RetailSession extends Model
{
    use BelongsToTeam;

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function accountant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    protected static function booted(): void
    {
        parent::creating(function (RetailSession $session): void {
            $code = generate_code('POS-', get_next_id($session));

            $session->setAttribute('code', $code);
        });
    }
}
