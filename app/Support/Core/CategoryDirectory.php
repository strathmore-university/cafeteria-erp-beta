<?php

namespace App\Support\Core;

use App\Models\Core\Category;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

class CategoryDirectory
{
    /**
     * @throws Throwable
     */
    public function index(
        string $name,
        string $search = ''
    ): Collection|Category {
        $root = $this->root($name, filled($search));
        $message = 'Root category (' . $name . ') not found';
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
    ): Category {
        return Category::with(tannery($search, ['descendants'], []))
            ->where('name', '=', $name)
            ->select(['id', '_lft', '_rgt'])
            ->isReference()
            ->first();
    }

    private function search(
        Category $root,
        string $search
    ): ?Category {
        return Category::where('parent_id', $root->id)
            ->where('name', '=', $search)
            ->first();
    }
}
