<?php

namespace App\Support\Inventory;

use App\Models\Inventory\Article;
use App\Models\Inventory\Store;
use Throwable;

class FetchArticleCapacity
{
    private Article $article;

    private ?Store $store = null;

    private bool $soldStock = false;

    /**
     * @throws Throwable
     */
    public function index(
        Article $article,
        ?Store $store = null,
        bool $soldStock = false
    ): float|int {
        $this->soldStock = $soldStock;
        $this->article = $article;
        $this->store = $store;

        $isReference = $article->getAttribute('is_reference') ?? false;

        return match ((bool) $isReference) {
            default => $this->articleCapacity($article),
            true => $this->referenceCapacity(),
        };
    }

    private function referenceCapacity(): int|float
    {
        $total = 0;

        $this->article->descendants->each(
            /**
             * @throws Throwable
             */
            function (Article $article) use (&$total): void {
                $capacity = $this->articleCapacity($article);
                $to = $this->article->getAttribute('unit_id');
                $from = $article->getAttribute('unit_id');

                match ($from === $to) {
                    false => $total += quantity_converter($from, $to, $capacity),
                    true => $total += $capacity,
                };
            }
        );

        return $total;
    }

    /**
     * @throws Throwable
     */
    private function articleCapacity(Article $article): float|int
    {
        $units = article_units($article, $this->store, $this->soldStock);

        return $units * ($article->unit_capacity ?? 1);
    }
}
