@extends('layouts.vue')

@section('content')
    <div class="container">
        <h1 class="title">Watcher Details</h1>

        Name: <strong>{{ $watcher->name }}</strong><br>
        Url: {{ $watcher->url }}<br>
        Last Synced: {{ $watcher->last_sync }}<br>
        Interval: {{ $watcher->interval->name }}<br>
        Initial Price: {{ $watcher->initial_value }}<br>
        Current Price: {{ $watcher->value }}<br>
        Alert Price: {{ $watcher->alert_value }}<br>
        XPath Query Price: {{ $watcher->query }}
        XPath Query Name: {{ $watcher->xpath_name }}

        <h2 class="mt-3 subtitle">Api logs</h2>
        <watcher-logs :watcher-id="{{ $watcher->id }}"></watcher-logs>
    </div>
@endsection
