<?php

namespace App\Models\Production;

use App\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CookingShift extends Model
{
    use BelongsToTeam;

    protected $guarded = [];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(FoodOrder::class);
    }

    protected static function booted(): void
    {
        static::creating(function (CookingShift $shift): void {
            $code = generate_code('SHIFT-', get_next_id($shift));
            $shift->setAttribute('code', $code);
        });
    }

    protected function casts(): array
    {
        return [
            'is_flagged' => 'boolean',
        ];
    }
}
