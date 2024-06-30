<?php

use App\Models\Accounting\PaymentMode;
use App\Models\Retail\PaymentTransaction;
use App\Models\Retail\Sale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('payment_allocations', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->foreignIdFor(Sale::class)->index()->constrained();
            $table->foreignIdFor(PaymentTransaction::class)->index()->constrained();
            $table->foreignIdFor(PaymentMode::class)->index()->constrained();
            $table->decimal('amount');
            $table->text('narration');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_allocations');
    }
};
