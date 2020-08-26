@extends('layouts.vue')

@section('content')
    <watcher-index :watchers="{{ json_encode($watchers) }}" :intervals="{{ $intervals }}" :user-id="{{ auth()->user()->id }}"></watcher-index>
@endsection
