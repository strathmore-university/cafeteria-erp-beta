<?php

namespace App\Concerns\Retail\Frontend\Items;

trait AlterItem
{
    public function increase(string $code): void
    {
        $this->saleItems = $this->saleItems->map(function ($item) use ($code) {
            if ($item['code'] === $code) {
                $this->saleTotal += $item['price'];
                $this->recalculate();

                $item['units'] += 1;
            }

            return $item;
        });
    }

    public function decrease(string $code): void
    {
        $this->saleItems = $this->saleItems->map(function ($item) use ($code) {
            $one = $item['code'] === $code;
            $two = $item['units'] !== 1;

            if (and_check($one, $two)) {
                $this->saleTotal -= $item['price'];
                $this->recalculate();

                $item['units'] -= 1;
            }

            return $item;
        });
    }

    public function remove(string $code): void
    {
        $items = $this->saleItems;
        $this->saleItems = $items->filter(function ($item) use ($code) {
            if ($item['code'] === $code) {
                $this->saleTotal -= $item['units'] * $item['price'];
                $this->recalculate();
            }

            return $item['code'] !== $code;
        });
    }
}
