<?php

use App\Models\Production\CookingShift;
use App\Models\Production\Recipe;
use App\Models\Production\Station;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('food_orders', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->code();
            $table->status();
            $table->foreignIdFor(CookingShift::class)->index()->constrained();
            $table->foreignIdFor(Recipe::class)->index()->constrained();
            $table->foreignIdFor(Station::class)->index()->constrained();
            $table->morphs('owner');
            $table->integer('expected_portions');
            $table->integer('expected_portions_upper_limit')->default(0);
            $table->integer('expected_portions_lower_limit')->default(0);
            $table->integer('produced_portions')->default(0);
            $table->integer('performance_rating')->default(0);
            $table->decimal('production_cost')->default(0);
            $table->decimal('unit_cost')->default(0);

            $table->timestamp('ingredients_dispatched_at')->nullable();
            $table->foreignIdFor(User::class, 'ingredients_dispatched_by')
                ->nullable()->index()->constrained('users');

            $table->timestamp('prepared_at')->nullable();
            $table->foreignIdFor(User::class, 'prepared_by')
                ->nullable()->index()->constrained('users');

            $table->boolean('requires_approval')->default(false);
            $table->boolean('is_flagged')->default(false);
            $table->boolean('has_recorded_remaining_stock')->default(false);
            $table->boolean('has_recorded_by_products')->default(false);
            $table->timestamp('initiated_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_orders');
    }
};
