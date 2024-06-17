<?php

namespace App\Models\Procurement;

use App\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use BelongsToTeam, HasFactory, SoftDeletes;

    // todo: implement dataservice vendor endpoint

    public function purchaseOrders(): HasMany
    {
        // todo: create relation manager
        return $this->hasMany(PurchaseOrder::class);
    }
}
