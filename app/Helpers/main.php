<?php

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
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
            ->content(function (Model $record) use ($column): string {
                $value = $record->getAttribute($column);
                $string = $value->toDateTimeString();

                return $string . ' (' . $value->diffForHumans() . ')';
            });
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
        $cols = 2;

        return Section::make()->columns($cols)->schema([
            placeholder('created_at', 'Created at'),
            placeholder('updated_at', 'Late updated'),
        ])->visible(fn ($record) => $record?->exists());
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

if ( ! function_exists('start_watch')) {
    function start_watch(): float
    {
        $start = microtime(true);

        DB::enableQueryLog();

        return $start;
    }
}

if ( ! function_exists('end_watch')) {
    function end_watch(float $start): void
    {
        $queries = DB::getQueryLog();
        $totalTime = microtime(true) - $start;

        collect($queries)->each(function ($query): void {
            Log::info('********************');
            Log::info('Query: ' . $query['query']);
            Log::info('Bindings: ' . implode(', ', $query['bindings']));
            Log::info('Time: ' . $query['time'] . 'ms');
            Log::info('********************');
        });

        Log::info('--------------');
        Log::info('Total queries: ' . count($queries));
        Log::info('Total execution time: ' . $totalTime . 's');
    }
}
