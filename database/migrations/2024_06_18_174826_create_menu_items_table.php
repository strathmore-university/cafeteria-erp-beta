<?php

use App\Models\Production\Menu;
use App\Models\Production\Station;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('menu_items', function (Blueprint $table): void {
            $table->id();
            $table->team();
            $table->article();
            $table->code();
            $table->name();
            $table->foreignIdFor(Station::class)->nullable()->index()->constrained();
            $table->foreignIdFor(Menu::class)->index()->constrained();
            $table->integer('selling_price');
            $table->integer('portions_to_prepare');
            //            $table->decimal('production')->nullable(); // todo: to keep or not? or have a separate table like price quotes?
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
