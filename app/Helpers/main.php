<?php

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

if ( ! function_exists('or_check')) {
    function or_check(bool $first, bool $second): bool
    {
        return $first || $second;
    }
}

if ( ! function_exists('and_check')) {
    function and_check(bool $first, bool $second): bool
    {
        return $first && $second;
    }
}

if ( ! function_exists('tannery')) {
    function tannery(bool $check, mixed $one, mixed $two): mixed
    {
        return $check ? $one : $two;
    }
}

if ( ! function_exists('placeholder')) {
    function placeholder(string $column, string $label)
    {
        return Placeholder::make($column)
            ->visible(fn ($record) => filled($record?->getAttribute($column)))
            ->label($label)
            ->content(
                fn (Model $record): string => $record->getAttribute($column)
                    ->diffForHumans()
            );
    }
}

if ( ! function_exists('build_string')) {
    function build_string(array $parts): string
    {
        return implode(' ', $parts);
    }
}

if ( ! function_exists('common_fields')) {
    function common_fields()
    {
        return Section::make()->schema([
            placeholder('created_at', 'Created at'),
            placeholder('updated_at', 'Late updated'),
        ])->visible(fn ($record) => $record?->exists())->columns(2);
    }
}

if ( ! function_exists('get_next_id')) {
    function get_next_id(Model $model): int
    {
        $table = $model->getTable();
        $query = "SHOW TABLE STATUS LIKE '" . $table . "'";

        return collect(DB::select($query))->first()?->Auto_increment;
    }
}

if ( ! function_exists('generate_code')) {
    function generate_code(string $prefix, int $id): string
    {
        $id = (string) $id;
        $pad = str_pad($id, 3, '0', STR_PAD_LEFT);

        return $prefix . $pad;
    }
}

if ( ! function_exists('auth_id')) {
    function auth_id(): int
    {
        return auth()->id() ?? system_user()->id;
    }
}

if ( ! function_exists('team_id')) {
    function team_id(): int
    {
        return auth()->user()->team_id ?? system_team()->id;
    }
}

if ( ! function_exists('error_notification')) {
    function error_notification(string|Throwable $exception): void
    {
        $message = match ($exception instanceof Throwable) {
            true => $exception->getMessage(),
            false => $exception
        };

        Log::error($exception);

        Notification::make()->title($message)
            ->persistent()
            ->danger()
            ->send();
    }
}

if ( ! function_exists('success')) {
    function success(string $message = ''): void
    {
        $message = match (filled($message)) {
            false => 'Operation completed successfully!',
            true => $message
        };

        Notification::make()->title($message)
            ->success()
            ->send();
    }
}
