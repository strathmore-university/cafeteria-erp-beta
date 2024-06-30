<?php

namespace Database\Seeders\Accounting;

use App\Models\Accounting\PaymentMode;
use App\Models\Production\Restaurant;
use App\Models\Retail\RetailSession;
use App\Models\User;
use Illuminate\Database\Seeder;
use Random\RandomException;

class AccountingSeeder extends Seeder
{
    /**
     * @throws RandomException
     */
    public function run(): void
    {
        //        PaymentMode::truncate();

        PaymentMode::create([
            'name' => 'Cash',
            'description' => '',
            'kfs_account_number' => '0115048',
            'object_code' => '8902',
            'revenue_account_number' => '__team_revenue',
            'revenue_object_code' => '__team_revenue',
            'requires_approval' => false,
            'requires_verification' => true,
        ]);

        PaymentMode::create([
            'name' => 'Wallet',
            'description' => '',
            'kfs_account_number' => '__team_deferred_income',
            'object_code' => '__team_deferred_income',
            'revenue_account_number' => '__team_revenue',
            'revenue_object_code' => '__team_revenue',
            'requires_approval' => false,
            'requires_verification' => false,
        ]);

        PaymentMode::create([
            'name' => 'Allowance',
            'description' => '',
            'kfs_account_number' => '__team_deferred_income',
            'object_code' => '2336',
            'revenue_account_number' => '__team_revenue',
            'revenue_object_code' => '2336',
            'requires_approval' => false,
            'requires_verification' => false,
        ]);

        PaymentMode::create([
            'name' => 'Mpesa-Offline',
            'description' => '',
            'kfs_account_number' => '__team_deferred_income',
            'object_code' => '__team_deferred_income',
            'revenue_account_number' => '__team_revenue',
            'revenue_object_code' => '__team_revenue',
            'requires_approval' => true,
            'requires_verification' => true,
        ]);

        PaymentMode::create([
            'name' => 'Mpesa',
            'description' => '',
            'kfs_account_number' => '__team_deferred_income',
            'object_code' => '__team_deferred_income',
            'revenue_account_number' => '__team_revenue',
            'revenue_object_code' => '__team_revenue',
            'requires_approval' => false,
            'requires_verification' => false,
        ]);

        PaymentMode::create([
            'name' => 'Bank',
            'description' => '',
            'kfs_account_number' => '0115048',
            'object_code' => '8902',
            'revenue_account_number' => '__team_revenue',
            'revenue_object_code' => '__team_revenue',
            'requires_approval' => false,
            'requires_verification' => true,
        ]);

        $session = RetailSession::create([
            'cashier_id' => auth_id(),
            'restaurant_id' => Restaurant::first()->id,
            'initial_cash_float' => random_int(1, 1000),
            'ending_cash_float' => random_int(1, 1000),
        ]);

        $user = User::first();

        foreach (range(1, 10) as $item) {
            $session->sales()->create([
                'sale_value' => 1000,
                'tendered_amount' => 500,
                'narration' => 'gnsdkghdksg',
                'cashier_id' => $user->id,
                'customer_id' => $user->id,
                'customer_type' => User::class,
            ]);
        }
    }
}
