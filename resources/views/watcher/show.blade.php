@extends('layouts.vue')

@section('content')
    <watcher-details
        :watcher="{{ json_encode($watcher) }}"
        :price-changes="{{ $priceChanges }}"
        :stock-changes="{{ $stockChanges }}"
        :intervals="{{ $intervals }}"
    ></watcher-details>
@endsection
