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
@extends("layouts.pdf")
@section('title-1')
    <div class="header-4">{{$grn->lpo->purchaseOrder->supplier->name }}</div>
@stop
@section("title", "Goods Received Note (GRN)")
@section("description", "Goods Received Note")
@push("css")
    <style>
        #table-wrapper {
            overflow: visible !important;
        }
    </style>
@endpush
{{--@section("addressee", "Emirand Enterprises Ltd.")--}}
@section('report-details')
    <table class="table table-clear">
        <tr class="p-0 m-0">
            <td class="w-50 pl-0 m-0">
                <table class="table table-bordered">
                    <tr>
                        <th class="border">GRN No.</th>
                        <td class="border text-right">{{$grn->grn_number}}</td>
                    </tr>
                    <tr>
                        <th class="border">Delivery Note No.</th>
                        <td class="border text-right">{{$grn->delivery_note_number}}</td>
                    </tr>
                    <tr>
                        <th class="border">Invoice No.</th>
                        <td class="border text-right">{{$grn->supplier_invoice_number}}</td>
                    </tr>
                    <tr>
                        <th class="border">LPO No.</th>
                        <td class="border text-right">{{$grn->lpo->lpo_number}}</td>
                    </tr>
                </table>
            </td>
            <td class="w-50 pr-0 m-0">
                <table class="table table-bordered">
                    <tr>
                        <th class="border">Expected Delivery Date.</th>
                        <td class="border text-right">{{\Carbon\Carbon::parse($grn->lpo->purchaseOrder->delivery_date)->isoFormat('ddd Do MMM, Y')}}</td>
                    </tr>
                    @if ($grn->booked)
                        <tr>
                            <th class="border">Delivered At.</th>
                            <td class="border text-right">{{\Carbon\Carbon::parse($grn->generated_at)->isoFormat('ddd Do MMM, Y H:m:s')}}</td>
                        </tr>
                        <tr>
                            <th class="border">Received By</th>
                            <td class="border text-right">{{$grn->generator->username}}</td>
                        </tr>
                        <tr>
                            <th class="border">Batch No.</th>
                            <td class="border text-right">{{$grn->batch ? $grn->batch->batch_number : null}}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>
@stop
@section('report-body')
    <table class="table table-bordered table-striped table-condensed">
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
        @foreach($grn->items as $item)
            @php
                /***/
            @endphp
        <tr>
            <td>{{$item->purchaseOrderItem->article->id}}</td>
            <td>{{$item->purchaseOrderItem->article->name}}</td>
            <td>{{$item->purchaseOrderItem->purchaseOrder->depot->name}}</td>
            <td>{{$item->expiry_date}}</td>
            <td class="text-right">{{number_format($item->required_quantity, 2)." ".str_plural($item->derivedUnit->name, $item->quantity)}}</td>
            <td class="text-right">{{number_format($item->quantity, 2)." ".str_plural($item->derivedUnit->name, $item->derivedUnit->quantity)}}</td>
            <td class="text-right">{{number_format($item->price, 2)}}</td>
            <td class="text-right">{{number_format($item->price * $item->quantity,2)}}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
@stop
@section('report-footer')
    <table class="table table-borderless" style="border-top: double !important;">
        <tr class="border-top" style="border-bottom: 1px solid black;">
            <th>Total Value:</th>
            <th class="text-right bold">{{number_format($grn->total, 2)}}</th>
        </tr>
    </table>
@stop
