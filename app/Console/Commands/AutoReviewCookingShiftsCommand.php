<?php

namespace App\Console\Commands;

use App\Models\Production\CookingShift;
use Illuminate\Console\Command;

class AutoReviewCookingShiftsCommand extends Command
{
    protected $signature = 'auto:review-cooking-shifts';

    protected $description = 'Command description';

    public function handle(): void
    {
        $shifts = CookingShift::withoutGlobalScopes()
            ->whereDate('created_at', today()->subDay())
            ->first();

        // todo: review cooking shifts
    }
}
