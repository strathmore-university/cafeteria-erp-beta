<?php

namespace App\Filament\Clusters\Production\Resources\MenuResource\RelationManagers;

use App\Models\Inventory\Article;
use App\Models\Production\Station;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('code')->required()->maxLength(255),
            Forms\Components\TextInput::make('selling_price')->required()->numeric(),
            Forms\Components\TextInput::make('portions')->required()->numeric(),
            Forms\Components\Select::make('article_id')->label('Article')
                ->options($this->articles())
                ->preload()->searchable()->required(),
            Forms\Components\Select::make('station_id')->label('Station')
                ->options(Station::pluck('name', 'id')->toArray())
                ->preload()->searchable()->required(),
        ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('selling_price')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('article.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('code')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('station.name')->searchable()
                    ->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])->headerActions([
                Tables\Actions\CreateAction::make()->slideOver(),
            ])->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('View')
                        ->url(fn ($record) => get_record_url($record))
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ]);
    }

    private function articles(): array
    {
        return Article::isDescendant()
            ->where(function (Builder $query): void {
                $query
                    ->where('is_consumable', '=', true)
                    ->orWhere('is_product', '=', true);
            })
            ->pluck('name', 'id')
            ->toArray();
    }
}
