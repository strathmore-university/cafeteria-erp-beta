<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->name();
            $table->code();
            $table->foreignIdFor(User::class, 'head_user_id')->index()->constrained('users');
            $table->unsignedInteger('parent_department_id')->index()->nullable();
            $table->integer('sync_id')->nullable();
            $table->string('chart_code')->nullable();
            $table->string('account_number')->nullable();
            $table->string('object_code')->nullable();
            $table->string('revenue_account_number')->nullable();
            $table->string('revenue_object_code')->nullable();
            $table->active();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
