@extends('layouts.base')

@section('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font@5.8.55/css/materialdesignicons.min.css">

    @vite(['resources/js/vue.js'])
@endsection

@section('navbar')
    <nav-bar username="{{ auth()->user()->name }}"></nav-bar>
@endsection
