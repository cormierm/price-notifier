@extends('layouts.vue')

@section('content')
    <dashboard-index
        :errors="{{ $errors }}"
        :price-changes="{{ $priceChanges }}"
        :stock-changes="{{ $stockChanges }}"
    ></dashboard-index>
@endsection
