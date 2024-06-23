<?php

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
        Schema::create('credit_notes', function (Blueprint $table): void {
            $table->id();
            $table->code();
            $table->team();
            $table->foreignIdFor(PurchaseOrder::class)->index()->constrained();
            $table->foreignIdFor(Supplier::class)->index()->constrained();
            $table->creator();
            $table->foreignIdFor(User::class, 'issued_by')->nullable()->index()->constrained('users');
            $table->decimal('total_value')->default(0);
            $table->status();
            $table->timestamp('issued_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};
