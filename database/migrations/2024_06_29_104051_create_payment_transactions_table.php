<?php

use App\Models\Accounting\PaymentMode;
use App\Models\Retail\Sale;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->code();
            $table->foreignIdFor(PaymentMode::class)->index()->constrained();
            $table->foreignIdFor(Sale::class)->index()->constrained();
            $table->nullableMorphs('customer');
            $table->decimal('tendered_amount');
            $table->decimal('paid_amount');
            $table->decimal('balance')->default(0);
            $table->text('narration')->nullable();
            $table->string('name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('checkout_request_id')->nullable();
            $table->string('merchant_request_id')->nullable();
            $table->boolean('is_consumed')->default(false);
            $table->boolean('is_valid');
            $table->timestamp('consumed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
