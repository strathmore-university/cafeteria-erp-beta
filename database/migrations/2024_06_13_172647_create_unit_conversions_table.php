<?php

use App\Models\Core\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('unit_conversions', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Unit::class, 'from_unit_id')->index()->constrained('units');
            $table->foreignIdFor(Unit::class, 'to_unit_id')->index()->constrained('units');
            $table->float('factor', 10);
            $table->active();
            $table->timestamps();
            $table->unique(['from_unit_id', 'to_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_conversions');
    }
};
