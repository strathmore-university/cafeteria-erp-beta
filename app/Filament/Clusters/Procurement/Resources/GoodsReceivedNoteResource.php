<?php

namespace App\Filament\Clusters\Procurement\Resources;

use App\Filament\Clusters\Procurement;
use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource\Pages\ListGoodsReceivedNotes;
use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource\Pages\ViewGoodsReceivedNote;
use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource\RelationManagers\ItemsRelationManager;
use App\Models\Procurement\GoodsReceivedNote;
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

class GoodsReceivedNoteResource extends Resource
{
    protected static ?string $slug = 'goods-received-notes';

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $model = GoodsReceivedNote::class;

    protected static ?string $cluster = Procurement::class;

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->schema([
            Section::make()->schema([
                TextInput::make('code')->label('GRN Number'),
                TextInput::make('purchase_order_id')->label('LPO Number')
                    ->formatStateUsing(fn ($record) => $record->purchaseOrder->code),
                TextInput::make('supplier_id')->label('Supplier')
                    ->formatStateUsing(
                        fn ($record) => Str::title($record->supplier->getAttribute('name'))
                    ),
                TextInput::make('received_by')->label('Receiver')
                    ->formatStateUsing(
                        fn (GoodsReceivedNote $record) => Str::title($record->receiver?->name)
                    ),
            ])->columns($cols),
            Section::make()->schema([
                TextInput::make('total_value')
                    ->formatStateUsing(fn ($state) => 'Ksh. ' . number_format($state)),
                TextInput::make('delivery_note_number'),
                TextInput::make('invoice_number'),
                placeholder('invoiced_at', 'Invoiced at'),
            ])->columns($cols),
            //            FileUpload::make('attachments')->multiple()->nullable()->columnSpan(2)
            //                ->directory('App/Attachments/Procurement')
            //                ->deletable(false)
            //                ->downloadable()
            //                ->visibility('private')
            SpatieMediaLibraryFileUpload::make('attachments')
                ->deletable(false)->visibility('private')
                ->visible(fn (GoodsReceivedNote $record) => $record->hasMedia())
                ->downloadable()
                ->multiple(),

            Section::make()->schema([
                TextInput::make('status')
                    ->formatStateUsing(fn ($state) => Str::title($state)),
                placeholder('created_at', 'Created at'),
                placeholder('updated_at', 'Last updated'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->label('GRN Number')
                ->searchable()->sortable(),
            TextColumn::make('purchaseOrder.code')
                ->searchable()->sortable(),
            TextColumn::make('supplier.name')
                ->searchable()->sortable(),
            TextColumn::make('total_value')->numeric()
                ->sortable()->prefix('Ksh. '),
            TextColumn::make('received_by')->searchable()
                ->label('Received By')->sortable()->formatStateUsing(
                    fn ($record) => Str::title($record->receiver->name)
                )
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('received_at')->searchable()->dateTime()
                ->toggleable(isToggledHiddenByDefault: true)->sortable(),
            TextColumn::make('status')->badge()
                ->formatStateUsing(fn (string $state) => Str::title($state))
                ->color(fn (string $state): string => match ($state) {
                    'received' => 'success',
                    default => 'warning'
                }),
        ])->actions([
            ActionGroup::make([
                Action::make('download')
                    ->visible(fn (GoodsReceivedNote $record) => $record->canBeDownload())
                    ->url(fn (GoodsReceivedNote $record) => $record->downloadLink())
                    ->icon('heroicon-o-arrow-down-tray'),
                ViewAction::make(),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGoodsReceivedNotes::route('/'),
            'view' => ViewGoodsReceivedNote::route('/{record}/view'),
        ];
    }

    public static function getRelations(): array
    {
        return [ItemsRelationManager::class];
    }
}
