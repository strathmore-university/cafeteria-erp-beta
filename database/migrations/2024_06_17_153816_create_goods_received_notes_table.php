<?php

use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\Supplier;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('goods_received_notes', function (Blueprint $table): void {
            $table->id();
            $table->code();
            $table->team();
            $table->foreignIdFor(PurchaseOrder::class)->index()->constrained();
            $table->foreignIdFor(Supplier::class)->index()->constrained();
            $table->creator();

            $table->string('delivery_note_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->timestamp('invoiced_at')->nullable();

            $table->status();
            $table->boolean('is_posted_to_kfs')->default(false);
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_notes');
    }
};
