@extends('layouts.app')

@section('content')
    <home :watchers="{{ $watchers }}"></home>
@endsection
