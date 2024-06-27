<?php

use App\Models\Inventory\Store;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('product_dispatches', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->morphs('destination');
            $table->foreignIdFor(Store::class, 'from_store_id')->index()->constrained('stores');
            $table->foreignIdFor(Store::class, 'to_store_id')->index()->constrained('stores');
            $table->foreignIdFor(User::class, 'dispatched_by')->index()->constrained('users');
            $table->timestamp('dispatched_at')->nullable();
            $table->foreignIdFor(User::class, 'received_by')->nullable()->index()->constrained('users');
            $table->timestamp('received_at')->nullable();
            $table->boolean('requires_review')->default(true);
            $table->status();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_dispatches');
    }
};
