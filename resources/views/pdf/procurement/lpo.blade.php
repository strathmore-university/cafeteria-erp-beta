<?php
/**
 * Created by PhpStorm.
 * User: csm
 * Date: 25/10/18
 * Time: 09:50
 * @var \App\Models\Procurement\PurchaseOrder $purchaseOrder
 */
//$purchaseOrder = $lpo->purchaseOrder;
?>
@extends("layouts.pdf")
@section("title-1")
    <div class="header-4">{{ $purchaseOrder->supplier->name }}</div>
@stop
@section("title","LOCAL PURCHASE ORDER")
@section("description")
        <p>Local purchase order</p>
@endsection

{{--@section("addressee", "Emirand Enterprises Ltd.")--}}
@section('report-details')
    <table class="w-100 border p-2 table-borderless">
        <tr>
            @if($purchaseOrder->isExpired())
                <td>
                    <dl style="color: red;" class="px-2">
                        <dt>Expired</dt>
                        <dd>This LPO is no longer valid!</dd>
                    </dl>
                </td>
            @endif

            <td>
                <dl class="px-2">
                    <dt>Address</dt>
                    <dd>{{ $purchaseOrder->supplier->address }}</dd>
                </dl>
            </td>
            <td>
                <dl>
                    <dt>LPO Number</dt>
                    <dd class="font-weight-bolder text-muted">{{ $purchaseOrder->getAttribute('code') }}</dd>
                </dl>
            </td>
        </tr>
    </table>
@stop

@section('report-body')
    <h4>Requested Articles</h4>
    <table class="w-100 table-condensed">
        <tbody>
        <tr class="border-bottom">
            <th>Article</th>
            <th class="text-right">Qty Ordered</th>
            <th class="text-right">Unit Price</th>
            <th class="text-right">Total Amount</th>
        </tr>
        @foreach($purchaseOrder->items as $item)
            <tr class="p-0 m-0 border-bottom">
                <td>{{ $item->article->name }}</td>
                <td class="text-right">
                    {{ number_format($item->ordered_units, 2) }}
                </td>
                <td class="text-right">{{ number_format($item->price, 2) }}</td>
                <td class="text-right">{{ number_format($item->total_value, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@stop
@section('report-footer')
    <div class="mb-5" style="border-bottom: double !important;">
        <table class="table table-borderless">
            <tr style="border-top: 1px solid black;">
                <th>Total Amount:</th>
                <th class="text-right bold">KES {{ number_format( $purchaseOrder->total_value, 2) }}</th>
            </tr>
        </table>
    </div>

    <hr>

    <div class="border p-2">
        <span class="header-4">Acceptance of This Order is The Acceptance of All Conditions Here-in</span>
        <ol>
            <li>This Order is Valid Until
                <b>{{ \Carbon\Carbon::parse($purchaseOrder->expires_at)->isoFormat(' dddd Do MMMM, Y') }}</b></li>
            <li>Deliveries Accepted Subject to COUNT, WEIGHT and QUALITY.</li>
            <li>LPO No. Must be Quoted in Full on all Delivery Notes, Invoices and Correspondence.</li>
            <li>ALL Invoices/Delivery Notes Quantities and Prices MUST Match this LPO</li>
        </ol>
        <p class="h5 font-weight-bolder">This LPO has been electronically approved, therefore substitutes
            signatures.</p>
        <table class="w-100 table-borderless">
            <tr>
                    <td class="text-center">
                        <dl>
                            <dt>Approved By</dt>
                            <dd>{{ $purchaseOrder->latestApprovedReview()->reviewer->name }}</dd>
                        </dl>
                    </td>
                <td class="text-right">
                    <dl>
                        <dt>Authorization Date</dt>
                        <dd>{{ $purchaseOrder->latestApprovedReview()->reviewed_at }}</dd>
                    </dl>
                </td>
            </tr>
        </table>

        <table class="w-100 table-borderless">
            <tr>
                <td>
                    <dl>
                        <dt>Order Requested By</dt>
                        <dd>{{ $purchaseOrder->creator->name }}</dd>
                    </dl>
                </td>
                <td class="text-center">
                    <dl>
                        <dt>Order Request Date</dt>
                        <dd>{{ $purchaseOrder->lpo_generated_at }}</dd>
                    </dl>
                </td>
                <td class="text-right">
                    <dl>
                        <dt>Expected Delivery Date</dt>
                        <dd>{{ \Carbon\Carbon::parse($purchaseOrder->expected_delivery_date)->isoFormat('dddd Do MMMM, Y') }}</dd>
                    </dl>
                </td>
            </tr>
            <tr class="text-center">
                <td colspan="3" class="font-weight-bolder">
                    Kindly ensure you obtain a copy of our supplier terms and conditions
                </td>
            </tr>
        </table>
    </div>
@stop
@push('css')
    <style>
        table th, table td {
            padding: 1px;
        }
    </style>
@endpush
