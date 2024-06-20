<?php

namespace Database\Seeders\Procurement;

use App\Models\Inventory\Article;
use App\Models\Procurement\KfsVendor;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Throwable;

class ProcurementDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @throws Throwable
     */
    public function run(): void
    {
        $vendors = KfsVendor::limit(10)->get();
        $vendors->each(function (KfsVendor $vendor): void {
            Supplier::create([
                'kfs_preformat_description' => $vendor->pre_format_description,
                'kfs_preformat_code' => $vendor->pre_format_code,
                'kfs_vendor_number' => $vendor->vendor_number,
                'phone_number' => fake()->phoneNumber,
                'description' => fake()->sentence,
                'name' => $vendor->vendor_name,
                'kfs_vendor_id' => $vendor->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'address' => fake()->address,
                'email' => fake()->email,
                'percentage_vat' => 16,
            ]);
        });

        $articles = Article::canBeOrdered()->get();

        Supplier::all()->each(function (Supplier $supplier) use ($articles): void {
            $po = $supplier->purchaseOrders()->create([
                'team_id' => system_team()->id,
                'store_id' => system_team()->stores()->first()->id,
                'expected_delivery_date' => now()->addDays(),
            ]);

            $articles->each(function (Article $article) use ($po): void {
                $po->items()->create([
                    'article_id' => $article->id,
                    'ordered_units' => random_int(50, 100),
                    'price' => random_int(150, 1000),
                ]);
            });
        });

        $purchaseOrder = PurchaseOrder::first();
        $purchaseOrder->requestReview();
        $purchaseOrder->submitReview(['comment' => 'good', 'status' => 'approved']);

        $grn = $purchaseOrder->fetchOrCreateGrn();
        $grn->receive();
    }
}
