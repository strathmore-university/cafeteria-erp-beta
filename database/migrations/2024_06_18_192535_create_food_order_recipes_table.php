<?php

use App\Models\Production\FoodOrder;
use App\Models\Production\Recipe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('food_order_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(FoodOrder::class)->index()->constrained();
            $table->foreignIdFor(Recipe::class)->index()->constrained();
            $table->integer('expected_portions');
            $table->integer('produced_portions')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->timestamp('dispatched_at')->nullable();

            $table->timestamp('ingredients_dispatched_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_order_recipes');
    }
};
