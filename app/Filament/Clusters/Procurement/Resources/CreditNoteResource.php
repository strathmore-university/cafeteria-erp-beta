<?php

namespace App\Filament\Clusters\Procurement\Resources;

use App\Filament\Clusters\Procurement;
use App\Filament\Clusters\Procurement\Resources\CreditNoteResource\Pages\ListCreditNotes;
use App\Filament\Clusters\Procurement\Resources\CreditNoteResource\Pages\ViewCreditNote;
use App\Filament\Clusters\Procurement\Resources\CreditNoteResource\RelationManagers\ItemsRelationManager;
use App\Models\Procurement\CreditNote;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CreditNoteResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?string $slug = 'credit-notes';

    protected static ?string $cluster = Procurement::class;

    protected static ?string $model = CreditNote::class;

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            Section::make()->schema([
                TextInput::make('code')->label('Credit Not Number'),
                TextInput::make('purchase_order_id')->label('LPO Number')
                    ->formatStateUsing(fn ($record) => $record->purchaseOrder->code),
                TextInput::make('supplier_id')->label('Supplier')
                    ->formatStateUsing(
                        fn ($record) => Str::title($record->supplier->getAttribute('name'))
                    ),
                TextInput::make('created_by')->label('Creator')
                    ->formatStateUsing(
                        fn (CreditNote $record) => Str::title($record->creator->name)
                    ),
                TextInput::make('total_value')
                    ->formatStateUsing(fn ($state) => 'Ksh. ' . number_format($state)),
            ])->columns($cols),
            SpatieMediaLibraryFileUpload::make('attachments')
                ->columnSpan(2)
                ->visible(fn (CreditNote $record) => $record->hasMedia())
                ->deletable(false)->visibility('private')
                ->downloadable()
                ->multiple(),
            Section::make()->schema([
                TextInput::make('status')
                    ->formatStateUsing(fn ($state) => Str::title($state)),
                placeholder('created_at', 'Created at'),
                placeholder('updated_at', 'Last updated'),
                placeholder('issued_at', 'Issued at'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Credit Note Number')
                    ->searchable()->sortable(),
                TextColumn::make('purchaseOrder.code')
                    ->searchable()->sortable(),
                TextColumn::make('supplier.name')
                    ->searchable()->sortable(),
                TextColumn::make('total_value')->numeric()
                    ->sortable()->prefix('Ksh. '),
                TextColumn::make('created_by')->searchable()
                    ->label('Created By')->sortable()->formatStateUsing(
                        fn ($record) => Str::title($record->creator->name)
                    )
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('issued_at')->searchable()->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)->sortable(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (string $state) => Str::title($state))
                    ->color(fn (string $state): string => match ($state) {
                        'issued' => 'success',
                        default => 'warning'
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('download')
                        ->visible(fn (CreditNote $record) => $record->preventEdit())
                        ->url(fn (CreditNote $record) => $record->downloadLink())
                        ->icon('heroicon-o-arrow-down-tray'),
                    ViewAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCreditNotes::route('/'),
            'view' => ViewCreditNote::route('/{record}/view'),
        ];
    }
}
