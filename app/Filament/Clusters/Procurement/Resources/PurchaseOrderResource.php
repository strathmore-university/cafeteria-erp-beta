<?php

namespace App\Filament\Clusters\Procurement\Resources;

use App\Filament\Clusters\Procurement;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages\CreatePurchaseOrder;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages\EditPurchaseOrder;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages\ListPurchaseOrders;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages\ViewPurchaseOrder;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\RelationManagers\CreditNotesRelationManager;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\RelationManagers\GoodsReceivedNotesRelationManager;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\RelationManagers\ItemsRelationManager;
use App\Models\Procurement\PurchaseOrder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Str;
use Throwable;

class PurchaseOrderResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $cluster = Procurement::class;

    protected static ?string $slug = 'purchase-orders';

    protected static ?int $navigationSort = 3;

    /**
     * @throws Throwable
     */
    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form->columns($cols)->schema([
            Select::make('supplier_id')->label('Supplier')
                ->options(active_suppliers())->searchable()->preload(),
            Select::make('store_id')->label('Store')
                ->options(procurement_stores())->searchable()->preload(),
            TextInput::make('created_by')
                ->formatStateUsing(fn($record) => Str::title($record?->creator->name))
                ->label('Requested by')->readOnly(),
            TextInput::make('kfs_account_number')
                ->default(fn() => auth_team()->kfs_account_number)
                ->required()->string()->numeric(),
            DatePicker::make('expected_delivery_date')
                ->visible(fn($record) => $record?->hasBeenApproved())
                ->rules('required|after:yesterday|date'),
            DatePicker::make('expires_at')->readOnly()
                ->visible(fn($record) => $record?->hasBeenApproved()),
            TextInput::make('total_value')->readOnly(),
            Hidden::make('status')->default('draft'),
            Section::make()->columns(3)->schema([
                Placeholder::make('status')
                    ->visible(fn($record) => filled($record?->exists()))
                    ->content(fn(PurchaseOrder $record): string => Str::title($record->getAttribute('status'))),
                placeholder('created_at', 'Created at'),
                placeholder('updated_at', 'Late updated'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->label('PO Number')
                ->searchable()->sortable(),
            TextColumn::make('supplier.name')->searchable()->sortable(),
            TextColumn::make('creator.name')->label('Requested By')
                ->formatStateUsing(fn($state) => Str::title($state))
                ->searchable()->sortable(),
            TextColumn::make('total_value')->numeric()->sortable()
                ->prefix('Ksh. '),
            TextColumn::make('expected_delivery_date')->dateTime()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('status')->badge()
                ->formatStateUsing(fn($state) => Str::title($state))
                ->color(fn(string $state): string => match ($state) {
                    'pending review', 'returned', 'pending fulfilment' => 'warning',
                    'delivered', 'fulfilled' => 'success',
                    'rejected', 'expired' => 'danger',
                    default => 'gray'
                }),
        ])->actions([
            ActionGroup::make([
                ActionGroup::make([
                    Action::make('receive')->requiresConfirmation()
                        ->action(function (PurchaseOrder $record): void {
                            redirect(get_record_url($record->fetchGrn()));
                        })
                        ->visible(fn(PurchaseOrder $record) => $record->canBeReceived())
                        ->icon('heroicon-o-truck'),
                    Action::make('generate-credit-note')->requiresConfirmation()
                        ->action(function (PurchaseOrder $record): void {
                            redirect(get_record_url($record->generateCrn()));
                        })
                        ->visible(fn(PurchaseOrder $record) => $record->canGeneratedCrn())
                        ->icon('heroicon-o-receipt-percent'),
                    Action::make('download')
                        ->url(fn(PurchaseOrder $record) => $record->downloadLink())
                        ->visible(fn(PurchaseOrder $record) => $record->canBeDownloaded())
                        ->icon('heroicon-o-arrow-down-tray'),
                ])
                    ->dropdown(false),
                ViewAction::make(),
            ])->dropdownPlacement('top-end'),
        ]);
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
            GoodsReceivedNotesRelationManager::class,
            CreditNotesRelationManager::class
        ];
    }
}
