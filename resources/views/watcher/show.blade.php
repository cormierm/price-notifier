@extends('layouts.vue')

@section('content')
    <watcher-details
        :watcher="{{ json_encode($watcher) }}"
        :price-changes="{{ $priceChanges }}"
        :intervals="{{ $intervals }}"
    ></watcher-details>
@endsection
