@extends('layouts.vue')

@section('content')
    <dashboard-index
        :errors="{{ $errors }}"
        :price-changes="{{ json_encode($priceChanges) }}"
        :stock-changes="{{ json_encode($stockChanges) }}"
    ></dashboard-index>
@endsection
