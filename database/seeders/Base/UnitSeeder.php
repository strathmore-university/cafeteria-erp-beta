<?php

namespace Database\Seeders\Base;

use App\Models\Core\Unit;
use App\Models\Core\UnitConversion;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $UnitNodes = ['Volume', 'Count', 'Weight', 'Portion'];
        collect($UnitNodes)->each(function ($node): void {
            Unit::create(['name' => $node, 'is_reference' => true]);
        });

        $volumeUnits = [
            ['name' => 'Liter', 'symbol' => 'L'],
            ['name' => 'Milliliter', 'symbol' => 'mL'],
        ];

        $weightUnits = [
            ['name' => 'Kilogram', 'symbol' => 'kg'],
            ['name' => 'Gram', 'symbol' => 'g'],
        ];

        $countUnits = [
            ['name' => 'Piece', 'symbol' => 'pcs'],
            ['name' => 'Slice', 'symbol' => 'slice'],
        ];

        $volumeRoot = Unit::where('name', 'Volume')->first();
        $weightRoot = Unit::where('name', 'Weight')->first();
        $countRoot = Unit::where('name', 'Count')->first();
        $portionRoot = Unit::where('name', 'Portion')->first();

        collect($volumeUnits)->each(function ($unit) use ($volumeRoot): void {
            $node = Unit::create([
                'name' => $unit['name'],
                'code' => $unit['symbol'],
            ]);

            $volumeRoot->appendNode($node);
        });

        collect($weightUnits)->each(function ($unit) use ($weightRoot): void {
            $node = Unit::create([
                'name' => $unit['name'],
                'code' => $unit['symbol'],
            ]);

            $weightRoot->appendNode($node);
        });

        collect($countUnits)->each(function ($unit) use ($countRoot): void {
            $node = Unit::create([
                'name' => $unit['name'],
                'code' => $unit['symbol'],
            ]);

            $countRoot->appendNode($node);
        });

        $kg = Unit::where('name', 'Kilogram')->first()->id;
        $gram = Unit::where('name', 'Gram')->first()->id;
        UnitConversion::create(['from_unit_id' => $kg, 'to_unit_id' => $gram, 'factor' => 1000]);
        UnitConversion::create(['from_unit_id' => $gram, 'to_unit_id' => $kg, 'factor' => 0.001]);

        $liter = Unit::where('name', 'Liter')->first()->id;
        $ml = Unit::where('name', 'Milliliter')->first()->id;
        UnitConversion::create(['from_unit_id' => $liter, 'to_unit_id' => $ml, 'factor' => 1000]);
        UnitConversion::create(['from_unit_id' => $ml, 'to_unit_id' => $liter, 'factor' => 0.001]);

        $portionUnits = [
            ['name' => 'Full', 'symbol' => 'x1'],
            ['name' => 'Half', 'symbol' => 'x0.5'],
            ['name' => 'Double', 'symbol' => 'x2'],
            ['name' => 'Cup', 'symbol' => 'cup'],
            ['name' => 'Glass', 'symbol' => 'glass'],
        ];

        collect($portionUnits)->each(function ($unit) use ($portionRoot): void {
            $node = Unit::create([
                'name' => $unit['name'],
                'code' => $unit['symbol'],
            ]);

            $portionRoot->appendNode($node);
        });

        $full = Unit::where('name', '=', 'Full')->first();
        $half = Unit::where('name', '=', 'Half')->first();

        UnitConversion::create(['from_unit_id' => $half->id, 'to_unit_id' => $full->id, 'factor' => 0.5]);
        UnitConversion::create(['from_unit_id' => $full->id, 'to_unit_id' => $half->id, 'factor' => 2]);
    }
}
