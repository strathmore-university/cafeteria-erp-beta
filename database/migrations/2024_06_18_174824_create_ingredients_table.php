<?php

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Production\Recipe;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Recipe::class)->index()->constrained();
            $table->foreignIdFor(Article::class)->index()->constrained();
            $table->foreignIdFor(Unit::class)->index()->constrained();
            $table->decimal('quantity', 10);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
