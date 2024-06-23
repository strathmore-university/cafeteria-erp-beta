<?php

use App\Models\Core\Unit;
use App\Models\Production\FoodOrder;
use App\Models\Production\Recipe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_order_by_products', function (Blueprint $table) {
            $table->id();
            $table->article();
            $table->foreignIdFor(FoodOrder::class)->index()->constrained();
            $table->foreignIdFor(Unit::class)->index()->constrained();
            $table->decimal('quantity', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_order_by_products');
    }
};
