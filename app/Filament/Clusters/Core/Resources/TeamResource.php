<?php

namespace App\Filament\Clusters\Core\Resources;

use App\Filament\Clusters\Core;
use App\Filament\Clusters\Core\Resources\TeamResource\Pages\CreateTeam;
use App\Filament\Clusters\Core\Resources\TeamResource\Pages\EditTeam;
use App\Filament\Clusters\Core\Resources\TeamResource\Pages\ListTeams;
use App\Models\Core\Team;
use App\Models\User;
use Exception;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TeamResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $cluster = Core::class;

    protected static ?string $model = Team::class;

    protected static ?int $navigationSort = 0;

    protected static ?string $slug = 'teams';

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('description')->nullable(),
            Select::make('head_user_id')->label('Team lead')->options(
                User::
//                    where('type', '=', 'staff') // todo:
                all()
                    ->pluck('name', 'id')
            )->preload()->searchable(),
            Section::make([
                placeholder('created_at', 'Created Date'),
                placeholder('updated_at', 'Last Modified Date'),
            ])->columns($cols),
        ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()
                    ->formatStateUsing(fn ($state) => Str::title($state)),
                TextColumn::make('head.name')->label('Team lead')
                    ->formatStateUsing(fn ($state) => Str::title($state))
                    ->searchable()->sortable(),
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
        return Team::with('head:id,name');
    }
}
