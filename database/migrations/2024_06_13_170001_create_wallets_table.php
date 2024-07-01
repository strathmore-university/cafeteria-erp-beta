<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->code();
            $table->name();
            $table->morphs('owner');
            $table->decimal('allowance')->default(0);
            $table->decimal('allowance_balance')->default(0);
            $table->decimal('wallet_balance')->default(0);
            $table->boolean('is_wallet_active')->default(true);
            $table->boolean('is_allowance_active')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
