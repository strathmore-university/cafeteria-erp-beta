<?php

use App\Models\Core\Team;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('team_user', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Team::class)->index()->constrained();
            $table->foreignIdFor(User::class)->index()->constrained();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_user');
    }
};
