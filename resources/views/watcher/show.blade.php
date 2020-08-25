@extends('layouts.vue')

@section('content')
    <div class="container">
        <h1 class="title">Watcher Details</h1>

        <article class="panel is-primary">
            <p class="panel-heading">
                <strong>{{ $watcher->name }}<br></strong>
                {{ $watcher->url }}
            </p>
            <p class="panel-block">
                Created: {{ $watcher->created_at }}<br><br>

                Initial Price: {{ $watcher->initial_value }}<br>
                Current Price: {{ $watcher->value }} ({{ $watcher->last_sync }})<br>
                Lowest Price: {{ $watcher->lowest_price }} ({{ $watcher->lowest_at }})<br>
                Alert Price: {{ $watcher->alert_value }}<br><br>

                Interval: {{ $watcher->interval->name }}<br>
                XPath Query Price: {{ $watcher->query }}
            </p>
        </article>

        <watcher-logs :watcher-id="{{ $watcher->id }}"></watcher-logs>
    </div>
@endsection
