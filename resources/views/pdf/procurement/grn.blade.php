<?php
/**
 * Created by PhpStorm.
 * User: csm
 * Date: 25/10/18
 * Time: 09:50
 *
 * @var \App\Models\Grn $grn
 */
?>

@extends('layouts.pdf')
@section('title-1')
<div class="header-4">{{ $grn->supplier->name }}</div>
@stop
@section('title', 'Goods Received Note (GRN)')
@section('description', 'Goods Received Note')
@push('css')
    <style>
        #table-wrapper {
            overflow: visible !important;
        }
    </style>
@endpush

{{-- @section("addressee", "Emirand Enterprises Ltd.") --}}
@section('report-details')
<table class="table-clear table">
    <tr class="m-0 p-0">
        <td class="w-50 m-0 pl-0">
            <table class="table-bordered table">
                <tr>
                    <th class="border">GRN No.</th>
                    <td class="border text-right">{{ $grn->code }}</td>
                </tr>
                <tr>
                    <th class="border">Delivery Note No.</th>
                    <td class="border text-right">{{ $grn->delivery_note_number }}</td>
                </tr>
                <tr>
                    <th class="border">Invoice No.</th>
                    <td class="border text-right">{{ $grn->invoice_number }}</td>
                </tr>
                <tr>
                    <th class="border">LPO No.</th>
                    <td class="border text-right">{{ $grn->purchaseOrder->code }}</td>
                </tr>
            </table>
        </td>
        <td class="w-50 m-0 pr-0">
            <table class="table-bordered table">
                <tr>
                    <th class="border">Expected Delivery Date.</th>
                    <td class="border text-right">
                        {{ \Carbon\Carbon::parse($grn->purchaseOrder->expected_delivery_date)->isoFormat('ddd Do MMM, Y') }}
                    </td>
                </tr>
                @if ($grn->status == 'received')
                    <tr>
                        <th class="border">Delivered At.</th>
                        <td class="border text-right">
                            {{ \Carbon\Carbon::parse($grn->received_at)->isoFormat('ddd Do MMM, Y H:m:s') }}
                        </td>
                    </tr>
                    <tr>
                        <th class="border">Received By</th>
                        <td class="border text-right">{{ $grn->creator->name }}</td>
                    </tr>
                    {{-- <tr> --}}
                    {{-- <th class="border">Batch No.</th> --}}
                    {{-- <td class="border text-right">{{$grn->batch ? $grn->batch->batch_number : null}}</td> --}}
                    {{-- </tr> --}}
                @endif
            </table>
        </td>
    </tr>
</table>
@stop
@section('report-body')
<table class="table-bordered table-striped table-condensed table">
    <tbody>
        <tr>
            <th>ID</th>
            <th>Item</th>
            <th>Store</th>
            <th>Expiry Date</th>
            <th>Qty Ordered</th>
            <th>Qty Delivered</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
        @foreach ($grn->items as $item)
            <tr>
                <td>{{ $item->article_id }}</td>
                <td>{{ $item->article->name }}</td>
                <td>{{ $item->batch->store->name }}</td>
                <td>{{ $item->batch->expires_at }}</td>
                <td class="text-right">{{ number_format($item->units, 2) }}</td>
                <td class="text-right">
                    {{ number_format($item->purchaseOrderItem->ordered_units - $item->purchaseOrderItem->remaining_units, 2) }}
                </td>
                <td class="text-right">{{ number_format($item->price, 2) }}</td>
                <td class="text-right">{{ number_format($item->total_value, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@stop
@section('report-footer')
<table class="table-borderless table" style="border-top: double !important">
    <tr class="border-top" style="border-bottom: 1px solid black">
        <th>Total Value:</th>
        {{-- <th class="text-right bold">{{ number_format($grn->total_value, 2) }}</th> --}}
    </tr>
</table>
@stop
