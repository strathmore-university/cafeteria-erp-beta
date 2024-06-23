<?php

namespace App\Filament\Clusters\Inventory\Pages\Inventory\Resources;

use App\Filament\Clusters\Inventory;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\ArticleResource\Pages\CreateArticle;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\ArticleResource\Pages\EditArticle;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\ArticleResource\Pages\ListArticles;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\ArticleResource\Pages\ViewArticle;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\ArticleResource\RelationManagers\DescendantsRelationManager;
use App\Models\Core\Team;
use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Inventory\Store;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ArticleResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Inventory::class;
    protected static ?string $model = Article::class;
    protected static ?string $slug = 'articles';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->required(),
            TextInput::make('description')->required(),
            Select::make('store_id')->label('Default Store')
                ->options(Store::whereOwnerType(Team::class)->pluck('name', 'id')->toArray())
                ->searchable()
                ->preload(),
            Select::make('unit_id')->label('Tracking Unit')
                ->options(Unit::isDescendant()->pluck('name', 'id')->toArray())
//                    ->disabled(fn(string $state) => filled($state)) todo: prevent changing units for reference articles
                ->searchable()
                ->preload(),
            TextInput::make('lifespan_days')->nullable(),
            TextInput::make('reorder_level')->nullable(),

            Section::make('article_cost_type')->schema([
                Toggle::make('is_supportive')->default(false)->reactive()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('is_profit_contributing', false);
                        $set('is_expense', false);
                    }),
                Toggle::make('is_profit_contributing')->default(true)->reactive()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('is_supportive', false);
                        $set('is_expense', false);
                    }),
                Toggle::make('is_expense')->default(false)->reactive()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('is_supportive', false);
                        $set('is_profit_contributing', false);
                    }),
            ])->columns(3),
            Section::make('article_type')->schema([
                Toggle::make('is_ingredient')->default(true)->reactive()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('is_sellable', false);
                        $set('is_consumable', false);
                        $set('is_product', false);
                    }),
                Toggle::make('is_consumable')->default(null)->reactive()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('is_sellable', true);
                        $set('is_ingredient', false);
                        $set('is_product', false);
                    }),
                Toggle::make('is_product')->default(null)->reactive()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('is_sellable', true);
                        $set('is_ingredient', false);
                        $set('is_consumable', false);
                    }),
            ])->columns(3),

            Hidden::make('is_sellable')->default(false),
            Hidden::make('is_reference')->default(true),

            // todo: categories for articles
            //                Select::make('category_id')->label('Type')
            //                    ->options(store_types()->pluck('name', 'id')->toArray())
            //                    ->preload()
            //                    ->searchable(),

            common_fields(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('type'),
            TextColumn::make('unit.name')->searchable()->sortable(),
            IconColumn::make('is_active')->boolean(),
            IconColumn::make('is_reference')->boolean(),
        ])
            ->filters([])
            ->actions([EditAction::make()])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArticles::route('/'),
            'create' => CreateArticle::route('/create'),
            'edit' => EditArticle::route('/{record}/edit'),
            'view' => ViewArticle::route('/{record}/view'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return Article::with('descendants'); // todo: review if i want to display the reference articles here
        //        isReference()->orWhere('is_product', '=', true);
    }

    public static function getRelations(): array
    {
        return [DescendantsRelationManager::class];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
