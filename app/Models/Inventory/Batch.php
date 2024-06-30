<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToArticle;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Kalnoy\Nestedset\NodeTrait;

class Batch extends Model
{
    use BelongsToArticle, NodeTrait;
    use BelongsToTeam, HasOwner;

    protected $guarded = [];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Batch $batch): void {
            $value = $batch->initial_units * $batch->weighted_cost;
            $batch->current_units = $batch->initial_units;
            $batch->initial_value = $value;
            $batch->current_value = $value;
            $batch->previous_units = 0;

            if (blank($batch->getAttribute('batch_number'))) {
                $code = generate_code('BATCH-', get_next_id($batch));
                $batch->setAttribute('batch_number', $code);
            }
        });

        static::updating(function (Batch $batch): void {
            if ((int) $batch->current_units === 0) {
                $batch->depleted_at = now();
            }

            if ($batch->previous_units !== $batch->current_units) {
                $value = $batch->current_units * $batch->weighted_cost;
                $batch->current_value = $value;
            }
        });
    }
}
