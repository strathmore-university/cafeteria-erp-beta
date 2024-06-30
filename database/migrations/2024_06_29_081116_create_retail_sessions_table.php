<?php

use App\Models\Production\Restaurant;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('retail_sessions', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->code();
            $table->foreignIdFor(User::class, 'cashier_id')->index()->constrained('users');
            $table->foreignIdFor(Restaurant::class)->index()->constrained();
            $table->decimal('initial_cash_float')->default(0);
            $table->decimal('ending_cash_float')->default(0);
            $table->boolean('is_open')->default(true);
            $table->foreignIdFor(User::class, 'closed_by')->nullable()->index()->constrained('users');
            $table->boolean('is_closed')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('retail_sessions');
    }
};
