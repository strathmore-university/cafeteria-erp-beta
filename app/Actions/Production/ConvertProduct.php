<?php

namespace App\Actions\Production;

use App\Models\Inventory\Article;
use App\Models\Inventory\Batch;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\Store;
use App\Models\Production\ProductConversion;
use App\Models\Production\Station;
use App\Support\Inventory\UpdateStockLevel;
use Illuminate\Support\Facades\DB;
use Throwable;

class ConvertProduct
{
    public function execute(array $data): void
    {
        try {
            DB::transaction(function () use ($data) {
                $from = Article::find($data['from_id']);
                $to = Article::find($data['to_id']);
                $quantity = $data['quantity'];
                $movements = collect();
                $teamId = $to->getAttribute('team_id');

                $id = $data['station_id'];
                $station = Station::select(['id'])->find($id);
                $store = Store::whereOwnerId($station->id)
                    ->whereOwnerType(Station::class)
                    ->first();

                $batches = Batch::where('store_id', $store->id)
                    ->where('article_id', $from->id)
                    ->orderBy('created_at')
                    ->get();

                $remaining = $quantity;
                $batches->each(function (Batch $batch) use (
                    $station,
                    $to,
                    $from,
                    $store,
                    $teamId,
                    $movements,
                    &
                    $remaining
                ) {
                    if ($remaining === 0) {
                        return;
                    }

                    $units = match ($remaining > $batch->current_units) {
                        true => -$batch->current_units,
                        false => -$remaining,
                    };

                    $name = $from->getAttribute('name');

                    $narration = build_string([
                        'Converted'.abs($units).' portions of', $name,
                        'to', $to->getAttribute('name'), 'at',
                        $station->getAttribute('name'),
                    ]);

                    $movements->push([
                        'stock_value' => abs($units) * ($from->valuation_rate ?? 0),
                        'article_id' => $batch->getAttribute('article_id'),
                        'weighted_cost' => $from->valuation_rate ?? 0,
                        'narration' => $narration,
                        'store_id' => $store->id,
                        'batch_id' => $batch->id,
                        'team_id' => $teamId,
                        'units' => $units,
                    ]);

                    $batch->previous_units = $batch->current_units;
                    $batch->current_units += $units;
                    $batch->update();

                    $remaining += $units;
                });

                (new UpdateStockLevel())->team($teamId)
                    ->units((int) $quantity)
                    ->store($store->id)
                    ->article($from->id)
                    ->reduce()
                    ->index();

                $conversion = ProductConversion::create([
                    'station_id' => $data['station_id'],
                    'quantity' => $data['quantity'],
                    'from_id' => $from->id,
                    'to_id' => $to->id,
                ]);

                $narration = build_string([
                    'Received ', $quantity, ' portions of ',
                    $to->getAttribute('name'), 'from',
                    $from->getAttribute('name'), 'at',
                    $station->getAttribute('name'),
                    'from conversion id:', $conversion->id
                ]);

                $batch = Batch::create([
                    // todo: review expiry dates
                    // 'expires_at' => now()->addDays(7),
                    'weighted_cost' => $to->valuation_rate ?? 0,
                    'initial_units' => $quantity,
                    'narration' => $narration,
                    'store_id' => $store->id,
                    'article_id' => $to->id,
                    'previous_units' => 0,
                    'team_id' => $teamId,
                ]);

                $movements->push([
                    // todo: what is the value of the by-product
                    'stock_value' => $quantity * ($to->valuation_rate ?? 0),
                    'weighted_cost' => $to->valuation_rate ?? 0,
                    'narration' => $narration,
                    'store_id' => $store->id,
                    'batch_id' => $batch->id,
                    'article_id' => $to->id,
                    'team_id' => $teamId,
                    'units' => $quantity,
                ]);

                (new UpdateStockLevel())->team($teamId)
                    ->units((int) $quantity)
                    ->store($store->id)
                    ->article($to->id)
                    ->index();

                StockMovement::insert($movements->toArray());

                success();

                return $conversion;
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }
}