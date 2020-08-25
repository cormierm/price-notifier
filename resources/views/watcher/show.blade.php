@extends('layouts.vue')

@section('content')
    <watcher-details :watcher="{{ json_encode($watcher) }}" :intervals="{{ $intervals }}"></watcher-details>
@endsection
