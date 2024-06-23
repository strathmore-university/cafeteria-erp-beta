<?php

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Inventory\Store;
use App\Models\Production\FoodOrder;
use App\Models\Production\Ingredient;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('requested_ingredients', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(FoodOrder::class)->index()->constrained();
            $table->foreignIdFor(Ingredient::class)->index()->constrained();
            $table->foreignIdFor(Store::class)->index()->constrained();
            $table->foreignIdFor(Article::class)->index()->constrained();
            $table->foreignIdFor(Unit::class)->index()->constrained();
            $table->decimal('required_quantity', 10);
            $table->decimal('dispatched_quantity', 10)->default(0);
            $table->decimal('remaining_quantity', 10);
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requested_ingredients');
    }
};
