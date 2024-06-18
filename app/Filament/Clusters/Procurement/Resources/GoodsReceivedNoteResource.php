<?php

namespace App\Filament\Clusters\Procurement\Resources;

use App\Filament\Clusters\Procurement;
use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource\Pages\ListGoodsReceivedNotes;
use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource\Pages\ViewGoodsReceivedNote;
use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource\RelationManagers\ItemsRelationManager;
use App\Models\Procurement\GoodsReceivedNote;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class GoodsReceivedNoteResource extends Resource
{
    protected static ?string $slug = 'procurement/goods-received-notes';

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $model = GoodsReceivedNote::class;

    protected static ?string $cluster = Procurement::class;

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form
            ->schema([
                Section::make()->schema([
                    Placeholder::make('code')
                        ->visible(fn ($record) => filled($record?->exists()))
                        ->content(
                            fn (GoodsReceivedNote $record): string => $record->getAttribute('code')
                        ),
                    Placeholder::make('Purchase_order')
                        ->visible(fn ($record) => filled($record?->exists()))
                        ->content(
                            fn (GoodsReceivedNote $record): string => $record->purchaseOrder->getAttribute('code')
                        ),
                    Placeholder::make('supplier')
                        ->visible(fn ($record) => filled($record?->exists()))
                        ->content(
                            fn (GoodsReceivedNote $record): string => $record->supplier->getAttribute('name')
                        ),
                    Placeholder::make('grn_generated_by')
                        ->visible(fn ($record) => filled($record?->exists()))
                        ->content(
                            fn (GoodsReceivedNote $record): string => $record->creator->name
                        ),
                ])->columns($cols),
                Section::make()->schema([
                    TextInput::make('delivery_note_number')->string(),
                    TextInput::make('invoice_number')
                        ->string()
                        ->afterStateUpdated(fn (GoodsReceivedNote $record) => $record->invoiced_at = now()),
                ]),
                Section::make()->schema([
                    Placeholder::make('status')
                        ->visible(fn ($record) => filled($record?->exists()))
                        ->content(
                            fn (GoodsReceivedNote $record): string => Str::title($record->getAttribute('status'))
                        ),
                    placeholder('created_at', 'Created at'),
                    placeholder('updated_at', 'Late updated'),
                ])->columns(3),
            ])
            ->disabled(fn ($record) => ! $record->canBeReceived());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code'),
                TextColumn::make('purchaseOrder.code'),
                TextColumn::make('supplier.name'),
                TextColumn::make('creator.name')->label('Created By'),
                TextColumn::make('status')
                    ->formatStateUsing(fn (string $state) => Str::title($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'received' => 'success',
                        default => 'warning'
                    }),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('download-lpo')->label('Download LPO')
                    ->color('success')->button()
                    ->url(fn ($record) => route('download.grn', ['grn' => $record->id]))
                    ->visible(fn ($record) => $record->canBeDownload()),
            ])
            ->filters([])
            ->bulkActions([]);
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

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
