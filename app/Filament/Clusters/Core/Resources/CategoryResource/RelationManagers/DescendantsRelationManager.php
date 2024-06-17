<?php

namespace App\Filament\Clusters\Core\Resources\CategoryResource\RelationManagers;

use App\Models\Core\Category;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
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
                Toggle::make('is_active'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('Create new item')
                    ->form([
                        TextInput::make('name')->required(),
                        Toggle::make('is_active'),
                    ])->action(fn ($data) => $this->createDescendant($data)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private function createDescendant(array $data): void
    {
        $this->ownerRecord->appendNode(Category::create($data));
    }
}
