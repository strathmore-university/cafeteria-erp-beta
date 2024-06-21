<?php

namespace App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\RelationManagers;

use App\Models\Procurement\CreditNote;
use App\Models\Procurement\PurchaseOrder;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreditNotesRelationManager extends RelationManager
{
    protected static string $relationship = 'creditNotes';

    public static function canViewForRecord(
        Model|PurchaseOrder $ownerRecord,
        string $pageClass
    ): bool {
        return CreditNote::wherePurchaseOrderId($ownerRecord->id)
            ->exists();
    }

    public function table(Table $table): Table
    {
        return $table->recordTitleAttribute('code')
            ->columns([
                TextColumn::make('code')->label('Credit Note Number')
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
            ])->actions([
                Tables\Actions\Action::make('view-record')
                    ->url(fn ($record) => get_record_url($record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}
