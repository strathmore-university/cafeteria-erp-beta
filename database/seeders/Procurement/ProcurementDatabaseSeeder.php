<?php

namespace Database\Seeders\Procurement;

use App\Models\Inventory\Article;
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
        foreach (range(1, 10) as $item) {
            Supplier::create([
                'name' => fake()->name,
                'description' => fake()->sentence,
                'email' => fake()->email,
                'phone_number' => fake()->phoneNumber,
                'address' => fake()->address,
                'kfs_preformat_description' => fake()->sentence(2),
                'kfs_preformat_code' => fake()->slug,
                'percentage_vat' => 16,
                'kfs_vendor_number' => fake()->numberBetween(10000, 20000),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

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
