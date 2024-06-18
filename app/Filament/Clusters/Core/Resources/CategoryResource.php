<?php

namespace App\Filament\Clusters\Core\Resources;

use App\Filament\Clusters\Core;
use App\Filament\Clusters\Core\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Clusters\Core\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Clusters\Core\Resources\CategoryResource\Pages\ListCategories;
use App\Filament\Clusters\Core\Resources\CategoryResource\RelationManagers\DescendantsRelationManager;
use App\Models\Core\Category;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $model = Category::class;

    protected static ?string $cluster = Core::class;

    protected static ?string $slug = 'categories';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form
            ->schema([
                Section::make([
                    TextInput::make('name')->required(),
                    Select::make('parent_id')->label('Parent Category')
                        ->options(
                            Category::isReference()->pluck('name', 'id')
                                ->toArray()
                        )
                        ->afterStateUpdated(fn (Set $set, Get $get) => $set(
                            'is_reference',
                            blank($get('parent_id'))
                        ))
                        ->reactive()
                        ->nullable(),
                ]),
                Section::make([
                    Hidden::make('is_reference')->default(true),
                    placeholder('created_at', 'Created Date'),
                    placeholder('updated_at', 'Last Modified Date'),
                ])->columns($cols),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->actions([EditAction::make()])
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                IconColumn::make('is_active')->boolean(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [DescendantsRelationManager::class];
    }

    public static function getEloquentQuery(): Builder
    {
        return Category::isReference();
    }
}
