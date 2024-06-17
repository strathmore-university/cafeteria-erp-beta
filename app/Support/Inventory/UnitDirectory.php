<?php

namespace App\Support\Inventory;

use App\Models\Core\Unit;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class UnitDirectory
{
    /**
     * @throws Throwable
     */
    public function index(
        string $name,
        string $search = ''
    ): Collection|Unit {
        $root = $this->root($name, filled($search));
        $message = "Root category (' .{$name} .') not found";
        throw_if(blank($root), new Exception($message));

        return match (filled($search)) {
            true => $this->search($root, $search) ?? collect(),
            false => $root->descendants,
        };
    }

    private function root(
        string $name,
        bool $search =
        false
    ): Unit {
        return Unit::with(tannery($search, ['descendants'], []))
            ->where('name', '=', $name)
            ->select(['id', '_lft', '_rgt'])
            ->isReference()
            ->first();
    }

    private function search(Unit $root, string $search): ?Unit
    {
        return Unit::where('parent_id', $root->id)
            ->where('name', '=', $search)
            ->first();
    }
}
