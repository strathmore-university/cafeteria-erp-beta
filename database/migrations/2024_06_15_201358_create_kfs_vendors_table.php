<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('kfs_vendors', function (Blueprint $table): void {
            $table->id();
            $table->string('vendor_number')->unique();
            $table->string('vendor_name');
            $table->string('pre_format_code')->unique();
            $table->string('pre_format_description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kfs_vendors');
    }
};
