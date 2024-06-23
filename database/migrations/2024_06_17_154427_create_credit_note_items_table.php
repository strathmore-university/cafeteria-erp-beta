<?php

use App\Models\Inventory\Article;
use App\Models\Procurement\CreditNote;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseOrderItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('credit_note_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(PurchaseOrder::class)->index()->constrained();
            $table->foreignIdFor(PurchaseOrderItem::class)->index()->constrained();
            $table->foreignIdFor(CreditNote::class)->index()->constrained();
            $table->foreignIdFor(Article::class)->index()->constrained();
            $table->integer('units');
            $table->integer('price');
            $table->decimal('total_value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_note_items');
    }
};
