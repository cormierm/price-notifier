@extends('layouts.app')

@section('content')
    <home :watchers="{{ json_encode($watchers) }}" :intervals="{{ $intervals }}"></home>
@endsection
