<?php

use App\Models\Procurement\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('price_quotes', function (Blueprint $table): void {
            $table->id();
            $table->article();
            $table->foreignIdFor(Supplier::class)->index()->constrained();
            $table->decimal('price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_quotes');
    }
};
