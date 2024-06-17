<?php

use App\Models\Inventory\Batch;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->store();
            $table->article();
            $table->foreignIdFor(Batch::class)->index()->constrained();
            $table->decimal('weighted_cost');
            $table->decimal('stock_value');
            $table->decimal('units', 10);
            $table->text('narration');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
