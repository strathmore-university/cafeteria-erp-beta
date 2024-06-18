<?php

namespace App\Filament\Clusters\Core\Resources\UnitResource\RelationManagers;

use App\Models\Core\Unit;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DescendantsRelationManager extends RelationManager
{
    protected static string $relationship = 'descendants';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('code')->required(),
            Toggle::make('is_active'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('code'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->filters([])
            ->headerActions([
                Action::make('create')->label('Create new item')
                    ->form([
                        TextInput::make('name')->required(),
                        TextInput::make('code')->required(),
                    ])->action(fn ($data) => $this->createDescendant($data)),
            ])
            ->actions([EditAction::make()])
            ->bulkActions([]);
    }

    private function createDescendant(array $data): void
    {
        if ($this->ownerRecord instanceof Unit) {
            $this->ownerRecord->appendNode(Unit::create($data));

            success();
        }
    }
}
