<?php

use App\Models\Procurement\PurchaseOrder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(PurchaseOrder::class)->index()->constrained();
            $table->article();
            $table->integer('ordered_units');
            $table->integer('remaining_units');
            $table->decimal('price');
            $table->decimal('total_value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
