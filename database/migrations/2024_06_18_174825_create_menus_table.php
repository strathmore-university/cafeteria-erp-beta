<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->name();
            $table->morphs('owner');
            $table->string('active_day')->nullable();
            $table->timestamp('active_date')->nullable();
            $table->active();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['team_id', 'active_day', 'owner_id']);
            $table->unique(['team_id', 'active_date', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
