<?php

namespace App\Services\Inventory;

use App\Models\Inventory\StockMovement;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class CreateMovements
{
    private Collection $movements;

    public function __construct()
    {
        $this->movements = collect();
    }

    public function add(array $movement): self
    {
        $this->movements->push($movement);

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function execute(): void
    {
        $message = 'No movements to execute.';
        throw_if(blank($this->movements), new Exception($message));

        StockMovement::insert($this->movements->toArray());
        $this->movements = collect();
    }
}
