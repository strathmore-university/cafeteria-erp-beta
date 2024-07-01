<?php

namespace App\Models\Core;

use App\Concerns\HasOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use SoftDeletes, HasOwner;

    public function mutations(): HasMany
    {
        return $this->hasMany(WalletMutation::class);
    }
}
