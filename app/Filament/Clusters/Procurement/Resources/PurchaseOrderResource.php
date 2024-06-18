<?php

namespace App\Filament\Clusters\Procurement\Resources;

use App\Filament\Clusters\Procurement;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages\CreatePurchaseOrder;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages\EditPurchaseOrder;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages\ViewPurchaseOrder;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\RelationManagers\ItemsRelationManager;
use App\Models\Inventory\Store;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Str;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $cluster = Procurement::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'purchase-orders';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supplier_id')->options(Supplier::all()
                    ->pluck('name', 'id'))
                    ->disabled(fn ($record) => filled($record?->exists()))
                    ->searchable()
                    ->preload(),
                Select::make('store_id')
                    ->options(Store::all()->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
                DatePicker::make('expected_delivery_date'),
                DatePicker::make('expires_at')
                    ->visible(fn ($record) => $record->hasBeenApproved())
                    ->disabled(),
                TextInput::make('kfs_account_number'),
                Hidden::make('status')->default('draft'),
                Section::make()->schema([
                    Placeholder::make('status')
                        ->visible(fn ($record) => filled($record?->exists()))
                        ->content(
                            fn (PurchaseOrder $record): string => Str::title($record->getAttribute('status'))
                        ),
                    placeholder('created_at', 'Created at'),
                    placeholder('updated_at', 'Late updated'),
                ])->columns(3),
            ])->disabled(fn ($record) => $record?->preventEdit());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code'),
                TextColumn::make('supplier.name'),
                TextColumn::make('creator.name')->label('Requested By'),
                TextColumn::make('total_value')->numeric(),
                TextColumn::make('expected_delivery_date')->dateTime(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn ($state) => Str::title($state))
                    ->color(fn (string $state): string => match ($state) {
                        'pending review', 'returned', 'pending fulfilment' => 'warning',
                        'delivered', 'fulfilled' => 'success',
                        'rejected' => 'danger',
                        default => 'gray'
                    }),
            ])
            ->filters([])
            ->actions([
                Action::make('receive')->requiresConfirmation()
                    ->button()
                    ->visible(fn ($record) => $record->canBeDownload())
                    ->action(function ($record): void {
                        redirect(get_record_url($record->fetchOrCreateGrn()));
                    })
//                    ->authorize('receive')
                    ->color('success')
                    ->icon('heroicon-o-shopping-cart'),
                Action::make('download-lpo')->label('Download LPO')
                    ->color('success')->button()
                    ->url(fn ($record) => route('download.purchase-order', ['purchaseOrder' => $record]))
                    ->visible(fn ($record) => $record->canBeDownload()),
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
            'view' => ViewPurchaseOrder::route('/{record}/view'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            // todo: add grn relation
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [];
    }
}
