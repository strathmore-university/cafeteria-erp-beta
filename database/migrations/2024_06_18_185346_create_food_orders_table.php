<?php

use App\Models\Production\Restaurant;
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
            $table->code();
            $table->team();
            $table->description();
            $table->foreignIdFor(Restaurant::class)->index()->constrained();
            $table->foreignIdFor(User::class, 'prepared_by')
                ->nullable()->index()->constrained('users');
            $table->foreignIdFor(Station::class)->index()->constrained();
            $table->boolean('requires_approval')->default(false);

            $table->boolean('has_dispatched_ingredients')->default(false);
            $table->boolean('preparation_initiated')->default(false);

            $table->timestamp('ingredients_dispatched_at')->nullable();
            $table->status();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_orders');
    }
};
