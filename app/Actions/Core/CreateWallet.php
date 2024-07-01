<?php

namespace App\Actions\Core;

use App\Models\Core\Department;
use App\Models\Core\Wallet;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateWallet
{
    public function execute(Model $owner, bool $isStaff = false): void
    {
        $one = $owner instanceof Department;
        $allowanceIsActive = or_check($one, $isStaff);

        $code = match(class_basename($owner)) {
            default => Str::of($owner->getAttribute('code'))
                ->lower()->trim()->snake(),
            'User' => $owner->getAttribute('user_number'),
        };

        Wallet::create([
            'allowance' => tannery($isStaff, 4000, 0),
            'is_wallet_active' => $owner instanceof User,
            'is_allowance_active' => $allowanceIsActive,
            'name' => $owner->getAttribute('name'),
            'owner_type' => $owner->getMorphClass(),
            'owner_id' => $owner->getKey(),
            'allowance_balance' => 0,
            'wallet_balance' => 0,
            'code' => $code,
        ]);
    }
}