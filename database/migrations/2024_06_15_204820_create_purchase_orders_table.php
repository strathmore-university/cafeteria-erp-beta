<?php

use App\Models\Inventory\Store;
use App\Models\Procurement\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table): void {
            $table->id();
            $table->code();
            $table->team();
            $table->creator();
            $table->foreignIdFor(Supplier::class)->index()->constrained();
            $table->foreignIdFor(Store::class)->index()->constrained();
            $table->decimal('total_value', 15)->default(0);
            $table->timestamp('expected_delivery_date');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_fulfilled')->default(false);
            $table->boolean('is_lpo')->default(false);
            $table->timestamp('lpo_generated_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->string('kfs_account_number')->nullable();
            $table->status();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
