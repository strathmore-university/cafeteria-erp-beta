<?php

namespace App\Support\Core;

use App\Models\Core\Unit;
use App\Models\Core\UnitConversion;
use Exception;
use Illuminate\Support\Facades\Cache;
use Throwable;

class QuantityConverter
{
    /**
     * @throws Throwable
     */
    public function index(
        Unit|int $from,
        Unit|int $to,
        float $value
    ): float {
        $from = $this->fetchUnit($from);
        $to = $this->fetchUnit($to);

        $check = and_check(blank($from), blank($to));
        throw_if($check, new Exception('Unit not found'));

        return match ($from->id === $to->id) {
            default => $this->convert($from, $to, $value),
            true => $value,
        };
    }

    private function fetchUnit(Unit|int $value)
    {
        $id = match ($value instanceof Unit) {
            true => $value->id,
            false => $value,
        };

        $key = 'unit_' . $id;

        return Cache::rememberForever($key, function () use ($value) {
            return match ($value instanceof Unit) {
                false => Unit::select(['id', 'parent_id'])->find($value),
                true => $value,
            };
        });
    }

    /**
     * @throws Throwable
     */
    private function convert(
        Unit $from,
        Unit $to,
        int|float $value
    ): float {
        $message = 'Units ' . $from->id . ' and ' . $to->id . ' are not compatible';
        throw_if( ! $from->isSiblingOf($to), new Exception($message));

        $key = 'unit_conversion_' . $from->id . '_' . $to->id;
        $factor = Cache::rememberForever($key, function () use ($from, $to) {
            return UnitConversion::whereFromUnitId($from->id)
                ->whereToUnitId($to->id)
                ->select('factor')
                ->firstOrFail()
                ->factor;
        });

        return $value * $factor;
    }
}
