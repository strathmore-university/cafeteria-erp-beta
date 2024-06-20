<?php

namespace App\Console\Commands;

use App\Facades\ApiClient;
use App\Models\Procurement\KfsVendor;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class FetchKfsVendorsCommand extends Command
{
    protected $signature = 'fetch:kfs-vendors 
        {--size=1000 : Number of records to fetch}
        {--limit=null : Optional page limit for the query}';

    protected $description = 'Retrieve KFS vendors from the Data-service API';

    private Collection $items;

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        //        DB::transaction(function () {
        $size = $this->option('size') ?? 1000;
        $pages = $this->getPages();

        $pages->each(function ($page) use ($size): void {
            $query = ['size' => $size, 'page' => $page];
            $response = ApiClient::fetchVendors($query);

            $response = collect($response['_embedded']['vendors']);
            $response = $response->filter(fn ($vendor) => $vendor['status'] === 'Y');

            $response->each(function (array $entry): void {
                $no = str_replace('-0', '', $entry['vendorId']);

                $this->items->push([
                    'pre_format_description' => $entry['preFormartDescription'],
                    'pre_format_code' => $entry['preFormatCode'],
                    'vendor_name' => $entry['vendorName'],
                    'vendor_number' => $no,
                ]);
            });
        });

        KfsVendor::truncate();
        KfsVendor::insert($this->items->toArray());
        //        });
    }

    private function getPages(): Collection
    {
        $this->items = collect();

        if (filled($this->option('limit'))) {
            $end = (int) $this->option('limit') - 1;

            return collect(range(0, $end));
        }

        $response = ApiClient::fetchVendors(['size' => 1]);
        $total = $response['page']['totalElements'];
        $pages = (int) ceil($total / 1000);

        return collect(range(0, $pages - 1));
    }
}
