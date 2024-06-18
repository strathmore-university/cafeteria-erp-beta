<?php

namespace App\Support\Core;

use App\Models\Core\Unit;
use App\Models\Core\UnitConversion;
use Exception;
use Throwable;

class QuantityConverter
{
    /**
     * @throws Throwable
     */
    public function index(Unit|int $from, Unit|int $to, float $value): float
    {
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
        return match ($value instanceof Unit) {
            false => Unit::select(['id', 'parent_id'])->find($value),
            true => $value,
        };
    }

    /**
     * @throws Throwable
     */
    private function convert(
        Unit $from,
        Unit $to,
        int|float $value
    ): float|int {
        $message = 'Units ' . $from->id . ' and ' . $to->id . ' are not compatible';
        throw_if(! $from->isSiblingOf($to), new Exception($message));

        $conversion = UnitConversion::whereFromUnitId($from->id)
            ->whereToUnitId($to->id)
            ->select('factor')
            ->firstOrFail();

        return $value * $conversion->factor;
    }
}
