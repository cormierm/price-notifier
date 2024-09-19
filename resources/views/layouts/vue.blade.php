@extends('layouts.base')

@section('head')
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/5.3.45/css/materialdesignicons.min.css">

    @vite(['resources/js/vue.js'])
@endsection

@section('navbar')
    <nav-bar username="{{ auth()->user()->name }}"></nav-bar>
@endsection
