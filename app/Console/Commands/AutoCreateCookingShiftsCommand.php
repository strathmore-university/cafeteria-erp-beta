<?php

namespace App\Console\Commands;

use App\Models\Production\Station;
use Illuminate\Console\Command;

class AutoCreateCookingShiftsCommand extends Command
{
    protected $signature = 'auto:create-cooking-shifts';

    protected $description = 'Create cooking shifts for stations without shifts';

    public function handle(): void
    {
        $stations = Station::withoutGlobalScopes()
            ->whereDoesntHave('shifts', function ($query): void {
                $query->whereDate('created_at', now()->toDateString());
            })->get();

        $this->log('Found ' . $stations->count() . ' stations without shifts');

        $stations->each(fn (Station $station) => $station->fetchShift());

        $this->log('Cooking shifts created successfully');
    }

    private function log(string $message): void
    {
        $this->info($message);
        info($message);
    }
}
