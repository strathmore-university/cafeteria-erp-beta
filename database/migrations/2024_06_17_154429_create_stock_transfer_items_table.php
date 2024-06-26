<?php

use App\Models\Inventory\StockTransfer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfer_items', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->article();
            $table->foreignIdFor(StockTransfer::class)->index()->constrained();
            $table->integer('units');
            $table->integer('dispatched_units')->default(0);
            $table->integer('received_units')->default(0);
            $table->status();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
    }
};
