<?php

use App\Models\Production\Station;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('cooking_shifts', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->code();
            $table->foreignIdFor(Station::class)->index()->constrained();
            $table->decimal('performance_rating')->default(0);
            $table->boolean('is_flagged')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cooking_shifts');
    }
};
