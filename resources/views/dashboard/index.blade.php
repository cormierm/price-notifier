@extends('layouts.vue')

@section('content')
    <dashboard-index :price-changes="{{ $priceChanges }}"></dashboard-index>
@endsection
