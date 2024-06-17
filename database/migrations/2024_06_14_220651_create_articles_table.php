<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->store();
            $table->category();
            $table->unit();
            $table->name();
            $table->description();

            $table->integer('unit_capacity')->nullable();
            $table->decimal('weighted_cost')->nullable();
            $table->integer('lifespan_days')->nullable();
            $table->integer('reorder_level')->nullable();

            $table->boolean('is_profit_contributing')->default(false);
            $table->boolean('is_supportive')->default(false);
            $table->boolean('is_expense')->default(false);

            $table->boolean('is_ingredient')->default(false);
            $table->boolean('is_sellable')->default(false);
            $table->boolean('is_consumable')->default(false);
            $table->boolean('is_product')->default(false);

            $table->active();
            $table->nest();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
