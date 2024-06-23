<?php

use App\Models\Core\Unit;
use App\Models\Production\Recipe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('recipe_by_products', function (Blueprint $table): void {
            $table->id();
            $table->article();
            $table->foreignIdFor(Recipe::class)->index()->constrained();
            $table->foreignIdFor(Unit::class)->index()->constrained();
            $table->decimal('quantity', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipe_by_products');
    }
};
