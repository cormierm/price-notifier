@extends('layouts.vue')

@section('content')
    <dashboard-index
        :price-changes="{{ $priceChanges }}"
        :stock-changes="{{ $stockChanges }}"
    ></dashboard-index>
@endsection
