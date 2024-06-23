<?php

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Inventory\Store;
use App\Models\Production\FoodOrder;
use App\Models\Production\RequestedIngredient;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('dispatched_ingredients', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(User::class, 'dispatched_by')->index()->constrained('users');
            $table->foreignIdFor(FoodOrder::class)->index()->constrained();
            $table->foreignIdFor(RequestedIngredient::class)->index()->constrained();
            $table->foreignIdFor(Article::class)->index()->constrained();
            $table->foreignIdFor(Store::class)->index()->constrained();
            $table->foreignIdFor(Unit::class)->index()->constrained();
            $table->decimal('initial_units', 10);
            $table->decimal('current_units', 10)->default(0);
            $table->decimal('used_units', 10)->default(0);
            $table->decimal('cost_of_production', 10)->default(0);
            $table->status();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatched_ingredients');
    }
};
