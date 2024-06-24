<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages;

use App\Concerns\HasBackRoute;
use App\Filament\Clusters\Production\Resources\FoodOrderResource;
use App\Models\Inventory\Article;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class RecordByProducts extends ManageRelatedRecords
{
    use HasBackRoute;

    protected static string $resource = FoodOrderResource::class;

    protected static string $relationship = 'byProducts';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'By Products';
    }

    public function getSubheading(): string|Htmlable|null
    {
        $record = $this->getOwnerRecord();
        $code = $record->getAttribute('code');

        return 'For Food Order: ' . $code;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('article_id')->label('By Product')
                ->options(Article::whereIsProduct(true)->isDescendant()->pluck('name', 'id')->toArray())
                ->searchable()->preload()->required()->reactive()
                ->afterStateUpdated(function (Set $set, $state): void {
                    $article = Article::select(['id', 'unit_id'])->find($state);
                    $set('unit_id', $article->getAttribute('unit_id'));
                }),
            TextInput::make('quantity'),
            Hidden::make('unit_id'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('article.name')
            ->columns([
                Tables\Columns\TextColumn::make('article.name')
                    ->label('By Product Name')->searchable()->sortable(),
                Tables\Columns\TextInputColumn::make('quantity')
                    ->rules(['numeric', 'required']),
                Tables\Columns\TextColumn::make('unit.name'),
            ])->headerActions([
                Tables\Actions\CreateAction::make()->label('Add by product')->link(),
            ])->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Complete')->color('success')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->action(function (): void {
                    $record = $this->getOwnerRecord();
                    $record->setAttribute('has_recorded_by_products', true);
                    $record->update();

                    $this->back($record);
                }),
            ActionGroup::make([
                Action::make('view-food-order')
                    ->url(get_record_url($this->getOwnerRecord()))
                    ->color('gray')
                    ->icon('heroicon-o-ticket'),
            ]),
        ];
    }
}
