@extends('layouts.app')

@section('content')
    <home :watchers="{{ json_encode($watchers) }}"></home>
@endsection
