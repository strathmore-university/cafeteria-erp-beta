<?php

namespace App\Filament\Clusters\Inventory\Resources\ArticleResource\RelationManagers;

use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DescendantsRelationManager extends RelationManager
{
    protected static string $relationship = 'descendants';

    protected static ?string $title = 'Items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('lifespan_days')->required(),
                TextInput::make('reorder_level')
                    ->visible(fn () => ! $this->ownerRecord->getAttribute('is_product'))
                    ->nullable(),
                Toggle::make('is_active'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('unit_capacity'),
                TextColumn::make('unit.name'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Create new item')
                    ->form([
                        TextInput::make('name')->required(),
                        TextInput::make('description')->required(),
                        Select::make('unit_id')->label('Default Unit')
                            ->options(Unit::where('parent_id', '=', $this->ownerRecord->unit->parent_id)->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->preload(),
                        TextInput::make('lifespan_days')->required(),
                        TextInput::make('reorder_level')
                            ->visible(fn () => ! $this->ownerRecord->getAttribute('is_product'))
                            ->nullable(),
                        TextInput::make('unit_capacity')
                            ->visible(fn () => ! $this->ownerRecord->getAttribute('is_product'))
                            ->required(),
                    ])->action(fn ($data) => $this->createDescendant($data)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                //                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    private function createDescendant(array $data): void
    {
        $fields = [
            'is_ingredient', 'is_sellable', 'is_consumable', 'is_product', 'store_id',
            'is_profit_contributing', 'is_supportive', 'is_expense',
        ];
        $node = new Article();
        $node = $node->fill($data);
        $node->fill($this->ownerRecord->only($fields));

        if ($this->ownerRecord->getAttribute('is_product')) {
            $node->unit_capacity = 1;
        }

        $this->ownerRecord->appendNode($node);
    }
}
