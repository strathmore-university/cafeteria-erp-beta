<?php

use App\Models\Inventory\Store;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\Supplier;
use App\Models\User;
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
            $table->foreignIdFor(Store::class)->index()->constrained();
            $table->creator();
            $table->string('delivery_note_number')->nullable();
            $table->string('invoice_number')->nullable();
            $table->timestamp('invoiced_at')->nullable();
            $table->json('attachments')->nullable();
            $table->decimal('total_value')->default(0);
            $table->status();
            $table->boolean('is_posted_to_kfs')->default(false);
            $table->foreignIdFor(User::class, 'received_by')->nullable()->index()->constrained('users');
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_notes');
    }
};
