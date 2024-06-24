<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\RelationManagers;

use App\Concerns\HasBackRoute;
use App\Models\Production\FoodOrderByProducts;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ByProductsRelationManager extends RelationManager
{
    use HasBackRoute;

    protected static ?string $title = 'Produced By Products';

    protected static string $relationship = 'byProducts';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        $id = $ownerRecord->getKey();

        return FoodOrderByProducts::whereFoodOrderId($id)->exists();
    }

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name')
                    ->label('By Product')
                    ->searchable()->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()->searchable()->sortable(),
                Tables\Columns\TextColumn::make('unit.name'),
            ]);
    }
}
