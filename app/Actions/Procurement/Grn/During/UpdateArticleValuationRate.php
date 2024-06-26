<?php

namespace App\Actions\Procurement\Grn\During;

use App\Models\Procurement\GoodsReceivedNoteItem;
use Throwable;

class UpdateArticleValuationRate
{
    /**
     * @throws Throwable
     */
    public function execute(GoodsReceivedNoteItem $item): void
    {
        $article = $item->article;
        $previousUnits = article_units($article);
        $totalUnits = $previousUnits + $item->units;

        $newStockValue = $item->price * $item->units;
        $previousStockValue = $article->valuation_rate * $previousUnits;

        $totalNewValuation = $previousStockValue + $newStockValue;
        $newValuationRate = $totalNewValuation / $totalUnits;
        $article->update(['valuation_rate' => $newValuationRate]);
    }
}
