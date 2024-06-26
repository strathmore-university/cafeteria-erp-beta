<?php

use App\Models\Inventory\Article;
use App\Models\Production\Station;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('product_conversions', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->creator();
            $table->foreignIdFor(Station::class)->index()->constrained();
            $table->foreignIdFor(Article::class, 'from_id')->index()->constrained('articles');
            $table->foreignIdFor(Article::class, 'to_id')->index()->constrained('articles');
            $table->decimal('quantity', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_conversions');
    }
};
