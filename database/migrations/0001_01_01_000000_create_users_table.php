<?php

use App\Models\Core\Department;
use App\Models\Core\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Team::class)->index()->nullable();
            $table->foreignIdFor(Department::class)->index()->nullable();
            $table->string('name');
            $table->string('first_name');
            $table->string('other_names')->nullable();
            $table->string('last_name');
            $table->string('username')->unique();
            $table->string('user_number')->unique();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('phone_number')->unique()->nullable();

            $table->string('uac')->nullable()->default('512');
            $table->string('domain')->nullable();
            $table->string('guid')->nullable();

            $table->boolean('receive_transaction_alerts')->default(true);
            $table->boolean('receive_top_up_alerts')->default(true);
            $table->boolean('receive_allowance_zeroing_alerts')->default(true);
            $table->boolean('receive_donation_alerts')->default(true);
            $table->boolean('receive_other_alerts')->default(true);
            $table->boolean('is_staff');
            $table->boolean('is_system_user')->default(false);
            $table->active();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
