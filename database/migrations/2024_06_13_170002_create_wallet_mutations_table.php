<?php

use App\Models\Core\Wallet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallet_mutations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Wallet::class)->index()->constrained();
            $table->morphs('owner');
            $table->decimal('amount');
            $table->decimal('previous_balance');
            $table->decimal('current_balance');
            $table->string('narration');
            $table->string('wallet_type');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_mutations');
    }
};
