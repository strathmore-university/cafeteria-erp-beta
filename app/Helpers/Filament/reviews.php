<?php

use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

if ( ! function_exists('request_review')) {
    function request_review()
    {
        return Action::make('request-Review')
            ->visible(function (Model $record) {
                $method = 'canBeSubmittedForReview';

                return $record->$method();
            })
            ->action(function (Model $record): void {
                $method = 'requestReview';
                $record->$method();

                redirect(get_record_url($record));
            })
            ->icon('heroicon-o-paper-airplane')
            ->label('Request Review')
            ->requiresConfirmation()
            ->color('gray');
    }
}

if ( ! function_exists('review_form')) {
    function review_form()
    {
        return Action::make('review')->form([
            TextInput::make('comment')->label('comments')
                ->required()->string()->maxLength(255),
            Radio::make('status')->default('approve')
                ->required()->columns(3)->options([
                    'approved' => 'Approve',
                    'rejected' => 'Reject',
                    'returned' => 'Return',
                ]),
        ])->action(function (Model $record, $data): void {
            $method = 'submitReview';
            $record->$method($data);

            success();
            redirect(get_record_url($record));
        })->visible(function (Model $record) {
            $method = 'canBeReviewed';

            return $record->$method();
        })
            ->icon('heroicon-o-pencil-square')
            ->requiresConfirmation()
            ->color('success');
    }
}
