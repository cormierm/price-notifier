@extends('layouts.vue')

@section('content')
    <home :watchers="{{ json_encode($watchers) }}" :intervals="{{ $intervals }}" :user-id="{{ auth()->user()->id }}"></home>
@endsection
