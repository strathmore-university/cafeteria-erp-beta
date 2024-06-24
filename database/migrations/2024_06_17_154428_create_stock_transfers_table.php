<?php

use App\Models\Inventory\Store;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->team();
            $table->creator();
            $table->foreignIdFor(Store::class, 'from_store_id')->index()->constrained('stores');
            $table->foreignIdFor(Store::class, 'to_store_id')->index()->constrained('stores');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('actioned_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('narration')->nullable();
            $table->status();
            $table->timestamps();

            $table->unique(['from_store_id', 'to_store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};
