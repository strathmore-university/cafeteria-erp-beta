<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table): void {
            $table->id();
            $table->name();
            $table->description();
            $table->active();
            $table->foreignIdFor(User::class, 'head_user_id')->index()->constrained('users');
            $table->string('kfs_chart_code')->default('SC');
            $table->string('kfs_account_number');
            $table->default();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
