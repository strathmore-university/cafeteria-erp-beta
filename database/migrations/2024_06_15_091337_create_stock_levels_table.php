<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('stock_levels', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->article();
            $table->store();
            $table->decimal('previous_units', 10)->default(0);
            $table->decimal('current_units', 10)->default(0);
            $table->boolean('is_sold_stock')->default(false);
            $table->timestamps();

            $table->unique(['team_id', 'article_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_levels');
    }
};
