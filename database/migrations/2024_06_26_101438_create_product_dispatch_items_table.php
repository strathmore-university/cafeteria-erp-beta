<?php

use App\Models\Production\ProductDispatch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('product_dispatch_items', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->article();
            $table->foreignIdFor(ProductDispatch::class)->index()->constrained();
            $table->decimal('dispatched_quantity');
            $table->decimal('received_quantity')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_dispatch_items');
    }
};
