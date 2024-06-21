<?php

namespace App\Models\Procurement;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasIsActiveColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use BelongsToTeam, HasFactory, SoftDeletes;
    use HasIsActiveColumn;

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function kfsVendor(): BelongsTo
    {
        return $this->belongsTo(KfsVendor::class);
    }
}
