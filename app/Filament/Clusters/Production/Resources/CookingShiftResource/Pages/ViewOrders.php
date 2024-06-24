<?php

namespace App\Filament\Clusters\Production\Resources\CookingShiftResource\Pages;

use App\Filament\Clusters\Production\Resources\CookingShiftResource;
use App\Models\Production\FoodOrder;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ViewOrders extends ManageRelatedRecords
{
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string $resource = CookingShiftResource::class;

    protected static string $relationship = 'orders';

    protected static ?string $title = 'Orders';

    public static function getNavigationLabel(): string
    {
        return 'Orders';
    }

    //    public function form(Form $form): Form
    //    {
    //        return $form
    //            ->schema([
    //                Forms\Components\TextInput::make('recipe.name')
    //                    ->required()
    //                    ->maxLength(255),
    //            ]);
    //    }

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('recipe.name')
            ->contentGrid(['md' => 2, 'xl' => 3])->columns([
                Tables\Columns\Layout\Stack::make([
                    TextColumn::make('recipe.name')->searchable(),
                    TextColumn::make('owner.name')->searchable()
                        ->formatStateUsing(fn (FoodOrder $record) => 'For: ' . $record->ownerName()),
//                    Tables\Columns\Layout\Split::make([
                    Tables\Columns\Layout\Split::make([
                        TextColumn::make('expected_portions')
                            ->formatStateUsing(fn ($state) => $state . ' portions required')
                            ->badge()->color('gray'),
                        TextColumn::make('produced_portions')
                            ->formatStateUsing(fn ($state) => $state . ' portions produced')
                            ->visible(fn ($state) => $state > 0)
                            ->badge()->color('gray'),
                    ]),
                    Tables\Columns\Layout\Split::make([
                        TextColumn::make('performance_rating')
                            ->formatStateUsing(fn ($state) => $state . '% rating score')
                            ->visible(fn ($state) => $state > 0)
                            ->badge()->color('gray'),
                        TextColumn::make('status')->badge()
                            ->formatStateUsing(fn ($state) => Str::title($state))
                            ->color(fn (string $state): string => match ($state) {
                                'flagged', 'wastage detected' => 'danger',
                                'prepared' => 'success',
                                default => 'warning'
                            }),
                    ]),
//                    ]),
                ])->space(2),
            ])->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn ($record) => get_record_url($record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
