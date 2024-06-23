<?php

namespace App\Filament\Clusters\Production\Resources;

use App\Filament\Clusters\Production;
use App\Filament\Clusters\Production\Resources\RecipeResource\Pages\CreateRecipe;
use App\Filament\Clusters\Production\Resources\RecipeResource\Pages\EditRecipe;
use App\Filament\Clusters\Production\Resources\RecipeResource\Pages\ListRecipes;
use App\Filament\Clusters\Production\Resources\RecipeResource\Pages\ViewRecipe;
use App\Filament\Clusters\Production\Resources\RecipeResource\RelationManagers\ByProductsRelationManager;
use App\Filament\Clusters\Production\Resources\RecipeResource\RelationManagers\IngredientsRelationManager;
use App\Models\Production\Recipe;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Throwable;

class RecipeResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $cluster = Production::class;

    protected static ?string $slug = 'production/recipes';

    protected static ?string $model = Recipe::class;

    protected static ?int $navigationSort = 3;

    /**
     * @throws Throwable
     */
    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('description')->required(),
            Select::make('article_id')->label('Product')
                ->options(product_articles())->preload()->searchable()
                ->required(),
            TextInput::make('yield')->required()->numeric(),
            TextInput::make('surplus_tolerance')->required()
                ->label('Surplus Tolerance (%)')->suffix('%')
                ->numeric()->maxValue(100)->minValue(0),
            TextInput::make('wastage_tolerance')->required()
                ->label('Wastage Tolerance (%)')->suffix('%')
                ->numeric()->maxValue(100)->minValue(0),
            Select::make('category_id')->label('Recipe Category')
                ->options(recipe_groups()->pluck('name', 'id')->toArray())
                ->preload()->searchable()->required(),
            Toggle::make('is_active')->default(true),
            Section::make([
                placeholder('created_at', 'Created Date'),
                placeholder('updated_at', 'Last Modified Date'),
            ])->visible(fn ($record) => $record?->exists())->columns($cols),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('product.name')->searchable()->sortable(),
            TextColumn::make('yield')->sortable(),
            TextColumn::make('surplus_tolerance')->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('wastage_tolerance')->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('category.name')->searchable()->sortable(),
            IconColumn::make('is_active')->boolean(),
        ])->actions([ViewAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
            IngredientsRelationManager::class,
            ByProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecipes::route('/'),
            'create' => CreateRecipe::route('/create'),
            'edit' => EditRecipe::route('/{record}/edit'),
            'view' => ViewRecipe::route('/{record}/view'),
        ];
    }
}
