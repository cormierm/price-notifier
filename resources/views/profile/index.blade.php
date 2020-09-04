@extends('layouts.vue')

@section('content')
    <profile-index :user="{{ $user }}"></profile-index>
@endsection
