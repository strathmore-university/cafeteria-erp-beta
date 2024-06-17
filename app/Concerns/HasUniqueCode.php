<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUniqueCode
{
    protected static function booted(): void
    {
        static::creating(function (Model $model): void {
            $column = $this->fetchCodeColumn();
            $code = $this->generateCode();

            dump($column);
            dump($code);
            dump($model->getAttribute($column));
            dd(filled($model->getAttribute($column)));
            if (filled($model->getAttribute($column))) {
                return;
            }

            $model->setAttribute($column, $code);

            //            match(filled($model->getAttribute($column))) {
            //                default => $model->setAttribute($column, $code),
            //                true => null,
            //            };
        });
    }

    abstract protected function fetchCodeColumn(): string;

    abstract protected function fetchPrefix(): string;

    protected function generateCode(): string
    {
        $string = Str::upper(Str::random(6));
        $time = now()->format('Ymd');
        $prefix = $this->fetchPrefix();

        return $prefix . ':' . $time . '-' . $string;
    }
}
