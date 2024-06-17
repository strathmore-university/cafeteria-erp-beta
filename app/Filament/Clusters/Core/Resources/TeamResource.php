<?php

namespace App\Filament\Clusters\Core\Resources;

use App\Filament\Clusters\Core;
use App\Filament\Clusters\Core\Resources\TeamResource\Pages\CreateTeam;
use App\Filament\Clusters\Core\Resources\TeamResource\Pages\EditTeam;
use App\Filament\Clusters\Core\Resources\TeamResource\Pages\ListTeams;
use App\Models\Core\Team;
use Exception;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TeamResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Core::class;

    protected static ?string $model = Team::class;

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'teams';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('description')->nullable(),
                placeholder('created_at', 'Created Date'),
                placeholder('updated_at', 'Last Modified Date'),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description'),
            ])
            ->filters([])
            ->actions([EditAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeams::route('/'),
            'create' => CreateTeam::route('/create'),
            'edit' => EditTeam::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
