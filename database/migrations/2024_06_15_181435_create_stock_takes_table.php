<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('stock_takes', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->store();
            $table->creator();
            $table->description();
            $table->timestamp('started_at');
            $table->timestamp('concluded_at')->nullable();
            $table->status();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_takes');
    }
};
