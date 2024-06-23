<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('selling_portions', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->article();
            $table->foreignIdFor(App\Models\Production\MenuItem::class)->index()->constrained();
            $table->unit();
            $table->string('code')->nullable();
            $table->integer('selling_price')->nullable();
            $table->timestamps();

            //            $table->unique(['team_id', 'code']); // todo: revisit
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('selling_portions');
    }
};
