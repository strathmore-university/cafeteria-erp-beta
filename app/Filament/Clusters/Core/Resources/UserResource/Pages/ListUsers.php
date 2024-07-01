<?php

namespace App\Filament\Clusters\Core\Resources\UserResource\Pages;

use App\Filament\Clusters\Core\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('print-card')->color('danger')
//                ->authorize('print', $user)
                ->label('PRINT MEAL CARD')
                ->infolist([
                    TextEntry::make('preview')
//                        ->view('core::pdfs.meal-card')
//                        ->viewData(['user' => $user]),
                ])
                ->modalWidth('sm')
                ->modalSubmitActionLabel('DOWNLOAD')
//                ->icon('fas-barcode')
//                ->action(fn ($data) => response()->streamDownload(
//                    callback: fn () => print (core()->mealCardPDF($user)->output()),
//                    name: "{$user->code}_MEAL_CARD.pdf"
//                ));
        ];
    }
}
