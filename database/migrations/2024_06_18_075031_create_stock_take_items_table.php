<?php

use App\Models\Inventory\StockTake;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('stock_take_items', function (Blueprint $table): void {
            $table->id();
            $table->store();
            $table->foreignIdFor(StockTake::class)->index()->constrained();
            $table->article();
            $table->decimal('current_units', 10);
            $table->decimal('actual_units', 10);
            $table->decimal('variance', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_take_items');
    }
};
