<?php

namespace App\Filament\Clusters\Production\Resources\Production\ProductConversionResource\Pages;

use App\Actions\Production\ConvertProduct;
use App\Filament\Clusters\Production\Resources\ProductConversionResource;
use App\Models\Inventory\Article;
use App\Models\Inventory\StockLevel;
use App\Models\Inventory\Store;
use App\Models\Production\Station;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\ListRecords;
use Throwable;

class ListProductConversions extends ListRecords
{
    protected static string $resource = ProductConversionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Convert Product')
                ->requiresConfirmation()->slideOver()
                ->form([
                    Select::make('station_id')->label('Station')
                        ->relationship('station', 'name')
                        ->searchable()->preload()->reactive()->required()
                        ->afterStateUpdated(function ($state, Set $set) {
                            if (filled($state)) {
                                return;
                            }

                            $set('quantity', null);
                            $set('from_id', null);
                            $set('to_id', null);
                        }),
                    Select::make('from_id')->label('Product to convert')
                        ->options(fn(Get $get) => $this->fromArticles($get))
                        ->searchable()->preload()->reactive()->required()
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                            if (filled($state)) {
                                $max = $this->maxQuantity($get);
                                $set('quantity', $max);

                                return;
                            }

                            $set('quantity', null);
                            $set('to_id', null);
                        }),
                    Select::make('to_id')->label('Target Product')
                        ->options(fn(Get $get) => $this->toArticles($get))
                        ->required()->searchable()->preload()->reactive(),
                    TextInput::make('quantity')->label('Quantity')
                        ->maxValue(fn(Get $get) => $this->maxQuantity($get))
                        ->required()->numeric()->minValue(1),
                ])->action(function ($data) {
                    (new ConvertProduct())->execute($data);
                })
        ];
    }

    private function fromArticles(Get $get): array
    {
        $id = $get('station_id');
        if (blank($id)) {
            return [];
        }

        $station = Station::select('id')->find($id);
        $store = Store::whereOwnerId($station->id)
            ->whereOwnerType(Station::class)
            ->first();

        $ids = StockLevel::where('store_id', '=', $store->id)
            ->where('current_units', '>', 0)
            ->pluck('article_id')
            ->toArray();

        return Article::whereIn('id', $ids)
            ->whereIsProduct(true)
            ->isDescendant()
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * @throws Throwable
     */
    private function maxQuantity(Get $get): int
    {
        $fromArticle = $get('from_id');
        if (blank($fromArticle)) {
            return 0;
        }

        $id = $get('station_id');
        $station = Station::select('id')->find($id);
        $store = Store::whereOwnerId($station->id)
            ->whereOwnerType(Station::class)
            ->first();

        $article = Article::find($fromArticle);

        return article_units($article, $store);
    }

    private function toArticles(Get $get): array
    {
        $fromArticle = $get('from_id');

        return Article::whereNotIn('id', [$fromArticle])
            ->whereIsProduct(true)
            ->isDescendant()
            ->pluck('name', 'id')
            ->toArray();
    }
}
