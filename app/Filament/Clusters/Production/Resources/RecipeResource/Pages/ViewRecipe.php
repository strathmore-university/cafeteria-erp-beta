<?php

namespace App\Filament\Clusters\Production\Resources\RecipeResource\Pages;

use App\Filament\Clusters\Core\Resources\ReviewResource\Pages\ViewReview;
use App\Filament\Clusters\Production\Resources\RecipeResource;
use App\Models\Inventory\Article;
use App\Models\Production\Recipe;
use Filament\Actions\Action;
use Filament\Actions\EditAction;

class ViewRecipe extends ViewReview
{
    protected static string $resource = RecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //todo: download button
            Action::make('View-product')->icon('heroicon-o-eye')
                ->button()->url(function (Recipe $record) {
                    $id = $record->getAttribute('article_id');
                    $article = Article::select('id')->find($id);

                    return get_record_url($article);
                }),
            EditAction::make(),
            //            DeleteAction::make(),
        ];
    }
}
