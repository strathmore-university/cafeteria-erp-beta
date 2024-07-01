<?php

namespace Database\Seeders\Base;

use App\Models\Core\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = collect();
        // retail
        $settings->push([
            'group' => 'retail',
            'key' => 'receipt_title',
            'value' => 'STRATHMORE UNIVERSITY',
        ]);

        $settings->push([
            'group' => 'retail',
            'key' => 'receipt_subtitle',
            'value' => ':team_name',
        ]);

        $settings->push([
            'group' => 'retail',
            'key' => 'receipt_email',
            'value' => 'cafeteria@strathmore.edu',
        ]);

        $settings->push([
            'group' => 'retail',
            'key' => 'receipt_phone',
            'value' => '0703034249',
        ]);

        $settings->push([
            'group' => 'retail',
            'key' => 'receipt_footer_text',
            'value' => 'Thanks for dining with us.',
        ]);

        $settings->push([
            'group' => 'retail',
            'key' => 'receipt_copyright_text',
            'value' => 'Strathmore University',
        ]);

        $settings->push([
            'group' => 'retail',
            'key' => 'charge_vat',
            'value' => 'false',
        ]);

        $settings->push([
            'group' => 'retail',
            'key' => 'receipt_height',
            'value' => '297',
        ]);

        $settings->push([
            'group' => 'retail',
            'key' => 'receipt_width',
            'value' => '72',
        ]);

        // mpesa
        $settings->push([
            'group' => 'mpesa',
            'key' => 'business_short_code',
            'encrypted_value' => '5047995',
        ]);

        $settings->push([
            'group' => 'mpesa',
            'key' => 'party_a',
            'encrypted_value' => '600989',
        ]);

        $settings->push([
            'group' => 'mpesa',
            'key' => 'party_b',
            'encrypted_value' => '5047997',
        ]);

        $settings->push([
            'group' => 'mpesa',
            'key' => 'consumer_key',
            'encrypted_value' => 'JDn9Ab9VGARyaG6qfGTUsGvV9nsZo6wn',
        ]);

        $settings->push([
            'group' => 'mpesa',
            'key' => 'consumer_secret',
            'encrypted_value' => 'HALqUfWnow7YRNEh',
        ]);

        $settings->push([
            'group' => 'mpesa',
            'key' => 'pass_key',
            'encrypted_value' => 'a4622970cf163ddc241d2986cd49d7cc6ebb41a0ea310a8a9d6da5a795d8907d',
        ]);

        $settings->push([
            'group' => 'mpesa',
            'key' => 'initiator_name',
            'encrypted_value' => 'George Munyi',
        ]);

//        $settings->push([
//            'group' => 'mpesa',
//            'key' => 'initiator_password',
//            'encrypted_value' => '', // todo:
//        ]);

        $settings->push([
            'group' => 'mpesa',
            'key' => 'integration_type',
            'encrypted_value' => 'BUY_GOODS',
        ]);

        $settings->push([
            'group' => 'mpesa',
            'key' => 'c2b_callback_endpoint',
            'encrypted_value' => '/api/daraja/c2b/confirm',
        ]);

        $settings->push([
            'group' => 'mpesa',
            'key' => 'app_base_url',
            'encrypted_value' => 'https://pos.strathmore.edu',
        ]);

        $settings->push([
            'group' => 'mpesa',
            'key' => 'bank_account_number',
            'encrypted_value' => '0115036',
        ]);

        $settings->push([
            'group' => 'mpesa',
            'key' => 'bank_object_code',
            'encrypted_value' => '8902',
        ]);

        // kfs
        $settings->push([
            'group' => 'kfs',
            'key' => 'base_url',
            'value' => 'https://kfs5.strathmore.edu/kfs-prd',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'test_url',
            'value' => 'https://juba.strathmore.edu/kfs-prd',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'encryption_secret_key',
            'encrypted_value' => 'phahre4to7Aijude',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'encryption_cipher',
            'encrypted_value' => 'aes-128-cbc',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'encryption_iv',
            'encrypted_value' => 'SREFAIQUOQU8PU5A',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'post_journal_vouchers',
            'value' => 'true',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'post_payment_vouchers',
            'value' => 'true',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'post_stock',
            'value' => 'true',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'post_pv_endpoint',
            'value' => '/ws/secure/externalPaymentRequest/{iv}',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'post_jv_endpoint',
            'value' => '/ws/jv.do',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'update_stock_endpoint',
            'value' => '/ws/updatestock',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'get_all_items_endpoint',
            'value' => '/finance/getAllItemsInStore/{storeCode}',
        ]);

        $settings->push([
            'group' => 'kfs',
            'key' => 'get_stock_endpoint',
            'value' => '/ws/stockitem',
        ]);

        // sync
        $settings->push([
            'group' => 'sync',
            'key' => 'daily_department_sync_time',
            'value' => '23:20',
        ]);

        $settings->push([
            'group' => 'sync',
            'key' => 'daily_staff_sync_time',
            'value' => '23:25',
        ]);

        $settings->push([
            'group' => 'sync',
            'key' => 'daily_student_sync_time',
            'value' => '23:35',
        ]);

        $settings->push([
            'group' => 'sync',
            'key' => 'enable_monthly_top_ups',
            'value' => 'true',
        ]);

        $settings->push([
            'group' => 'sync',
            'key' => 'monthly_top_up_date',
            'value' => '24',
        ]);

        $settings->push([
            'group' => 'sync',
            'key' => 'monthly_top_up_time',
            'value' => '23:40',
        ]);

        $settings->push([
            'group' => 'sync',
            'key' => 'annual_zeroing_date',
            'value' => '20',
        ]);

        $settings->push([
            'group' => 'sync',
            'key' => 'annual_zeroing_month',
            'value' => '12',
        ]);

        $settings->push([
            'group' => 'sync',
            'key' => 'annual_zeroing_time',
            'value' => '17:00',
        ]);

        $settings->push([
            'group' => 'sync',
            'key' => 'annual_first_top_up_date',
            'value' => '1',
        ]);

        $settings->push([
            'group' => 'sync',
            'key' => 'annual_first_top_up_month',
            'value' => '1',
        ]);

        $settings->push([
            'group' => 'sync',
            'key' => 'annual_first_top_up_time',
            'value' => '23:30',
        ]);

        $settings->each(function ($setting) {
            $a = Setting::create([
                'encrypted_value' => $setting['encrypted_value'] ?? null,
                'value' => $setting['value'] ?? null,
                'group' => $setting['group'],
                'key' => $setting['key'],
            ]);

            dump($a->value());
        });
    }
}
