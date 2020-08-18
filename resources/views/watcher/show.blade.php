@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="title">Watcher Details</h1>

        Name: <strong>{{ $watcher->name }}</strong><br>
        Url: {{ $watcher->url }}<br>
        Last Synced: {{ $watcher->last_sync }}<br>
        Initial: {{ $watcher->interval->name }}<br>
        Original Price: {{ $watcher->initial_value }}<br>
        Current Price: {{ $watcher->initial_value }}<br>
        Alert Price: {{ $watcher->initial_value }}<br>
        XPath Query Price: {{ $watcher->query }}

        <h2 class="mt-3 subtitle">Api logs</h2>
        <watcher-logs :logs="{{ $watcher->logs }}"></watcher-logs>
    </div>
@endsection
