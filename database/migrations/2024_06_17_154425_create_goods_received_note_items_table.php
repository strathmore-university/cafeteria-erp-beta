<?php

use App\Models\Inventory\Article;
use App\Models\Inventory\Batch;
use App\Models\Procurement\GoodsReceivedNote;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseOrderItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('goods_received_note_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(PurchaseOrder::class)->index()->constrained();
            $table->foreignIdFor(PurchaseOrderItem::class)->index()->constrained();
            $table->foreignIdFor(GoodsReceivedNote::class)->index()->constrained();
            $table->foreignIdFor(Article::class)->index()->constrained();
            $table->foreignIdFor(Batch::class)->nullable()->index()->constrained();
            $table->string('batch_number')->nullable();
            $table->date('expires_at')->nullable();
            $table->integer('units');
            $table->integer('price');
            $table->decimal('total_value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_received_note_items');
    }
};
