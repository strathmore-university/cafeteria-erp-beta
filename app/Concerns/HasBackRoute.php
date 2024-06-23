<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;

trait HasBackRoute
{
    protected function back(Model $record): void
    {
        $this->redirect(get_record_url($record), true);
    }
}
