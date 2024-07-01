<?php

namespace App\Models\Core;

use App\Concerns\HasOwner;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletMutation extends Model
{
    use SoftDeletes, HasOwner;

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
