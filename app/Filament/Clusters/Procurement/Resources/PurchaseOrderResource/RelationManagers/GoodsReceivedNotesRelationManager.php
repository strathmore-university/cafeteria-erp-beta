<?php

namespace App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\RelationManagers;

use App\Models\Procurement\PurchaseOrder;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GoodsReceivedNotesRelationManager extends RelationManager
{
    protected static string $relationship = 'goodsReceivedNotes';

    public static function canViewForRecord(
        Model|PurchaseOrder $ownerRecord,
        string $pageClass
    ): bool {
        return $ownerRecord->hasBeenApproved();
    }

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('code')
            ->columns([
                TextColumn::make('code')->label('GRN Number')
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
                Tables\Actions\Action::make('view-record')
                    ->url(fn ($record) => get_record_url($record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
