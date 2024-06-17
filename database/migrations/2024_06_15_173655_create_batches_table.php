<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->article();
            $table->store();
            $table->string('batch_number')->unique();
            $table->nullableMorphs('owner');
            $table->decimal('current_units', 10)->default(0);
            $table->decimal('previous_units', 10)->default(0);
            $table->decimal('initial_units', 10)->default(0);
            $table->text('narration')->nullable();
            $table->decimal('current_value');
            $table->decimal('initial_value');
            $table->decimal('weighted_cost');
            $table->decimal('production_cost')->nullable();
            $table->timestamp('locked_at')->nullable(); // for food preparation
            $table->timestamp('utilised_at')->nullable(); // for food preparation
            $table->boolean('is_sold_batch')->default(false);
            $table->timestamp('depleted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->nest();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
