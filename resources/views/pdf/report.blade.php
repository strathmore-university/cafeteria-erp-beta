@extends('layouts.pdf')
@section('title', $title)
@section('report-body')
    @include($excelView, ['pdf' => true, 'collection' => $collection, 'date' => $date, 'title' => $title, 'useCurrentCost' => $useCurrentCost])
@endsection
