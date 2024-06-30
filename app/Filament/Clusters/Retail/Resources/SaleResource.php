<?php

namespace App\Filament\Clusters\Retail\Resources;

use App\Filament\Clusters\Retail;
use App\Filament\Clusters\Retail\Resources\SaleResource\Pages\ListSales;
use App\Filament\Clusters\Retail\Resources\SaleResource\Pages\ViewSale;
use App\Models\Retail\Sale;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SaleResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $cluster = Retail::class;

    protected static ?string $model = Sale::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'sales';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('retail_session_id')->label('Session')
                ->formatStateUsing(fn (Sale $sale) => $sale->session->getAttribute('code')),
            TextInput::make('cashier_id')->label('Cashier')
                ->formatStateUsing(fn (Sale $sale) => $sale->cashier->name),
            text_input('sale_value'),
            text_input('tendered_amount'),
            text_input('narration'),
            Toggle::make('is_printed'),
        ]);
    }

    public static function table(Table $table): Table
    {
        //        hash('md5', '0700616911');
        //        hash('sha256', '254700616911');
        return $table->columns([
            TextColumn::make('cashier.name')->searchable()->sortable(),
            TextColumn::make('sale_value')->numeric(),
            TextColumn::make('tendered_amount')->numeric(),
        ])->actions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSales::route('/'),
            'view' => ViewSale::route('/{record}/view'),
        ];
    }
}
