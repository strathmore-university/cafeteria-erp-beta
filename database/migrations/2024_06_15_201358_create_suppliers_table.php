<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->unsignedInteger('kfs_vendor_id')->nullable()->index();
            $table->name();
            $table->description();
            $table->string('email', 100)->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->string('address');
            $table->integer('kfs_vendor_number')->nullable()->unique();
            $table->string('kfs_preformat_code')->nullable();
            $table->string('kfs_preformat_description')->nullable();
            $table->string('supplier_number')->nullable();
            $table->decimal('percentage_vat');
            $table->active();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
