<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('payment_modes', function (Blueprint $table): void {
            $table->id();
            $table->name();
            $table->description();
            $table->string('kfs_account_number');
            $table->string('object_code');

            $table->string('internal_account_number')->nullable(); // todo: remove
            $table->string('internal_object_code')->nullable(); // todo: remove

            $table->string('revenue_account_number');
            $table->string('revenue_object_code');

            $table->boolean('requires_approval'); // todo: whats the difference
            $table->boolean('requires_verification');

            $table->active();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_modes');
    }
};
