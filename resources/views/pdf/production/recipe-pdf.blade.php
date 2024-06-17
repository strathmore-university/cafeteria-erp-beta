<?php
/**
 * Created by PhpStorm.
 * User: csm
 * Date: 25/10/18
 * Time: 09:50
 */
?>
@extends("layouts.pdf")
@section("title", "Recipe")
@section("addressee", $recipe->name)
@section("description", "A single Recipe PDF")
@push("css")
    <style>
        #table-wrapper {
            overflow: visible !important;
        }
    </style>
@endpush
{{--@section("addressee", "Emirand Enterprises Ltd.")--}}
@section('report-details')
    <div class="row border border-bottom">
        <div class="col-4" id="table-wrapper">
            <table class="table table-condensed">
                <tbody class="">
                <tr class="border">
                    <th class="border">Recipe No.</th>
                    <td class="border text-righ t">{{$recipe->recipe_number}}</td>
                </tr>
                <tr>
                    <th class="border">Production Article</th>
                    <td class=" border text-right"><span style="font-weight: bolder;">{{$recipe->productionArticle->display_name }}</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="col-4" id="table-wrapper">
            {{--<table class="table table-condensed">
                <tbody class="">
                <tr>
                    <th class="border">Ordered By.</th>
                    <td class=" border text-right">{{$invoice->creator->name}}</td>
                </tr>
                <tr class="border">
                    <th class="border">Order Date.</th>
                    <td class="border text-right">{{\Carbon\Carbon::parse($invoice->ordered_at)}}</td>
                </tr>
                </tbody>
            </table>--}}
        </div>
        <div class="col-4" id="table-wrapper">
            <table class="table table-condensed">
                <tbody class="">
                <tr>
                    <th class="border">Recipe Group</th>
                    <td class=" border text-right">{{$recipe->recipeGroup->display_name}}</td>
                </tr>
                <tr>
                    <th class="border">Yield (portions)</th>
                    <td class=" border text-right">{{$recipe->yield}}</td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
@stop
@section('report-body')
    <div class="font-weight-bold">Ingredients</div>
    <table class="table table-bordered table-striped table-condensed">
        <tbody>
        <tr>
            <th>#</th>
            <th>Ingredient</th>
            <th>Quantity</th>
            <th>Units</th>
            <th>Net Weight (kg)</th>
            <th>Unit Price</th>
            <th>Total Cost</th>
        </tr>
        @php
            $grand_total = 0;
        @endphp
        @foreach($recipe->ingredients as $ng)
            @php
                //Totals
                $total = $ng->ingredient->cost_per_unit * $ng->ingredient->portion_quantity;
                $grand_total +=  $total;
            @endphp
        <tr>
            <td class="">{{$ng->article_number}}</td>
            <td class="">{{$ng->display_name}}</td>
            <td class="text-right">{{number_format($ng->ingredient->portion_quantity , 2)}}</td>
            <td class="">{{\App\DerivedUnit::find($ng->ingredient->derived_unit_id)->name }}</td>
            <td class="text-right">{{number_format($ng->weight_factor , 4)}}</td>
            <td class="text-right">{{number_format($ng->ingredient->cost_per_unit , 2)}}</td>
            <td class="text-right">{{number_format($total , 2)}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
@stop
@section('report-footer')
    <div class="row">
        <div class="col-8"></div>
        <div class="col-4">
            <table class="table table-borderless" style="border-top: 1px solid black !important;">
                <tr class="border-top" style="border-bottom: double;">
                    <th>Total Cost:</th>
                    <th class="text-right bold">{{number_format($grand_total, 2)}}</th>
                </tr>
            </table>
        </div>
    </div>
@stop
