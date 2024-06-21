<?php
/**
 * Created by PhpStorm.
 * User: csm
 * Date: 25/10/18
 * Time: 09:50
 *
 * @var \App\Models\Procurement\CreditNote $crn
 */
?>
@extends("layouts.pdf")
@section('title-1')
    <div class="header-4">{{ $crn->supplier->name }}</div>
@stop
@section("title", "Credit Note (CRN)")
@section("description", "Credit Note")
{{--@section("addressee", "Emirand Enterprises Ltd.")--}}
@section('report-details')
    <table class="table table-clear">
        <tr class="p-0 m-0">
            <td class="w-50 pl-0 m-0">
                <table class="table table-bordered">
                    <tr>
                        <th class="border">CRN No.</th>
                        <td class="border text-right">{{ $crn->code }}</td>
                    </tr>
                    <tr>
                        <th class="border">LPO No.</th>
                        <td class="border text-right">{{ $crn->purchaseOrder->code }}</td>
                    </tr>
                </table>
            </td>
            <td class="w-50 pr-0 m-0">
                <table class="table table-bordered">
                    <tr>
                        <th class="border">Issued At.</th>
                        <td class="border text-right">{{\Carbon\Carbon::parse($crn->issued_at)->isoFormat('ddd Do MMM, Y H:m:s')}}</td>
                    </tr>

                    <tr>
                        <th class="border">Issued By</th>
                        <td class="border text-right">{{$crn->creator->name}}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@stop
@section('report-body')
    <table class="table table-bordered table-striped table-condensed">
        <tbody>
        <tr>
            <th>Item</th>
            <th>Qty Written Off</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
        @foreach($crn->items as $item)
            <tr>
                <td>{{ $item->article->name }}</td>
                <td class="text-right">{{ number_format($item->units, 2) }}</td>
                <td class="text-right">{{ number_format($item->price, 2) }}</td>
                <td class="text-right">{{ number_format($item->total_value,2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@stop
@section('report-footer')
    <table class="table table-borderless" style="border-top: double !important;">
        <tr class="border-top" style="border-bottom: 1px solid black;">
            <th>Total Value:</th>
            <th class="text-right bold">{{ number_format($crn->total_value, 2) }}</th>
        </tr>
    </table>
@stop
