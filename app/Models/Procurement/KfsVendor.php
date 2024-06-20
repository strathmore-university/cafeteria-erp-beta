<?php

namespace App\Models\Procurement;

use App\Facades\ApiClient;
use App\Filament\Clusters\Procurement\Resources\KfsVendorResource;
use Database\Seeders\Procurement\KfsVendorSeeder;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class KfsVendor extends Model
{
    protected $guarded = [];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public static function refreshEntries(): void
    {
        try {
            $param = ['--class' => KfsVendorSeeder::class];
            Artisan::call('db:seed', $param);

            success();
        } catch (Throwable $exception) {
            error_notification($exception->getMessage());
        }
    }

    public static function retrieve(string $search): void
    {
        try {
            $exists = self::checkDB($search);
            throw_if($exists, new Exception('Vendor already exists!'));

            $search = str_replace('-0', '', $search);
            $response = ApiClient::fetchVendor($search);

            $message = 'Vendor is inactive!';
            $check = $response['status'] !== 'Y';
            throw_if($check, new Exception($message));

            self::createRecord($response, $search);

            success();
            redirect(KfsVendorResource::getUrl());
        } catch (Throwable $exception) {
            $message = match ($exception->getCode()) {
                404 => 'Vendor not found!',
                default => $exception,
            };

            error_notification($message);
        }
    }

    private static function createRecord(
        Collection $data,
        string $search
    ): void {
        self::create([
            'pre_format_description' => $data['preFormartDescription'],
            'pre_format_code' => $data['preFormatCode'],
            'vendor_name' => $data['vendorName'],
            'vendor_number' => $search,
        ]);
    }

    private static function checkDB(string $search): bool
    {
        return self::where('vendor_number', $search)->exists();
    }
}
