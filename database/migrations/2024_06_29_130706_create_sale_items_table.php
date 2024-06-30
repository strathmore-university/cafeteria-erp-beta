<?php

use App\Models\Core\Unit;
use App\Models\Production\SellingPortion;
use App\Models\Retail\Sale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->foreignIdFor(Sale::class)->index()->constrained();
            $table->foreignIdFor(App\Models\Production\MenuItem::class)->index()->constrained();
            $table->foreignIdFor(SellingPortion::class)->index()->constrained();
            $table->foreignIdFor(Unit::class)->index()->constrained();
            $table->string('narration');
            $table->decimal('units');
            //            $table->decimal('unit_cost');
            $table->decimal('sale_price');
            $table->decimal('total_amount');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};
