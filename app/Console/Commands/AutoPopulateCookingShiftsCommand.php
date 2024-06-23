<?php

namespace App\Console\Commands;

use App\Models\Production\MenuItem;
use App\Models\Production\Recipe;
use App\Models\Production\Restaurant;
use Illuminate\Console\Command;

class AutoPopulateCookingShiftsCommand extends Command
{
    protected $signature = 'auto:populate-cooking-shifts';

    protected $description = 'Command description';

    public function handle(): void
    {
        $restaurants = Restaurant::withoutGlobalScopes()->get();
        $restaurants->each(function (Restaurant $restaurant): void {
            $with = ['station:id', 'menu:id,owner_type,owner_id', 'article:id'];
            $select = ['id', 'station_id', 'menu_id', 'article_id'];
            $select = array_merge($select, ['portions_to_prepare']);
            $menu = $restaurant->daysMenu();

            $menuItems = MenuItem::with($with)->withoutGlobalScopes()
                ->whereMenuId($menu->id)
                ->select($select)
                ->get();

            $menuItems->each(function (MenuItem $item): void {
                $id = $item->getAttribute('article_id');
                $recipe = Recipe::where('article_id', '=', $id)
                    ->select('id')
                    ->first();

                match (blank($recipe)) {
                    false => $this->createOrder($item, $recipe->id),
                    true => null,
                };
            });
        });
    }

    private function createOrder(MenuItem $item, int $recipeId): void
    {
        $station = $item->station;
        $menu = $item->menu;

        $station->fetchShift()->orders()->create([
            'expected_portions' => $item->portions_to_prepare,
            'owner_type' => $menu->owner_type,
            'status' => 'pending preparation',
            'owner_id' => $menu->owner_id,
            'station_id' => $station->id,
            'recipe_id' => $recipeId,
        ]);
    }
}
