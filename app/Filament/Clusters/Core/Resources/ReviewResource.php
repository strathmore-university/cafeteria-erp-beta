<?php

namespace App\Filament\Clusters\Core\Resources;

use App\Filament\Clusters\Core;
use App\Filament\Clusters\Core\Resources\ReviewResource\Pages\ListReviews;
use App\Filament\Clusters\Core\Resources\ReviewResource\Pages\ViewReview;
use App\Models\Core\Review;
use Exception;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ReviewResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $model = Review::class;

    protected static ?string $cluster = Core::class;

    protected static ?string $slug = 'reviews';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        $cols = 2;

        return $form
            ->schema([
                Placeholder::make('reviewer.name')
                    ->label("Reviewer's name")
                    ->content(
                        fn ($record): string => Str::title($record->reviewer->name)
                    ),
                TextInput::make('reviewable_type'),
                TextInput::make('comment'),
                TextInput::make('status')
                    ->formatStateUsing(
                        fn (string $state): string => class_basename($state)
                    ),
                Section::make([
                    placeholder('created_at', 'Created Date'),
                    placeholder('updated_at', 'Last Modified Date'),
                ])->columns($cols),
            ])->disabled();
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reviewable_type')
                    ->formatStateUsing(
                        fn (string $state): string => class_basename($state)
                    )
                    ->sortable()
                    ->sortable(),
                TextColumn::make('reviewer.name')->searchable()->sortable()
                    ->formatStateUsing(fn (string $state): string => Str::title($state)),
                TextColumn::make('comment')
                    ->formatStateUsing(fn (string $state): string => Str::title($state)),
                TextColumn::make('reviewed_at')->dateTime(),
                TextColumn::make('status')->badge()
                    ->formatStateUsing(fn (string $state): string => Str::title($state))
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        default => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('reviewable_type')
                    ->options(reviewable_types())
                    ->searchable(),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('reviewed-record')
                    ->url(fn ($record) => get_record_url($record)),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'view' => ViewReview::route('/{record}/view'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return Review::with('reviewer:id,name');
    }
}
