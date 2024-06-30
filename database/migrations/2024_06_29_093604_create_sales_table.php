<?php

use App\Models\Retail\RetailSession;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->foreignIdFor(RetailSession::class)->index()->constrained();
            $table->foreignIdFor(User::class, 'cashier_id')->index()->constrained('users');
            $table->nullableMorphs('customer');
            $table->decimal('sale_value');
            $table->decimal('tendered_amount');
            $table->text('narration');
            $table->boolean('is_printed')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
