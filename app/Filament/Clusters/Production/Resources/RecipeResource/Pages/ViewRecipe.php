<?php

namespace App\Filament\Clusters\Production\Resources\RecipeResource\Pages;

use App\Filament\Clusters\Core\Resources\ReviewResource\Pages\ViewReview;
use App\Filament\Clusters\Production\Resources\RecipeResource;
use App\Models\Inventory\Article;
use App\Models\Production\Recipe;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

class ViewRecipe extends ViewReview
{
    protected static string $resource = RecipeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //todo: download button
            // todo: button to go to the product
            Action::make('View-product')->button()
                ->url(function (Recipe $record) {
                    $article = Article::find($record->article_id);

                    return get_record_url($article);
                }),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
