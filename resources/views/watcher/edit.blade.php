@extends('layouts.vue')

@section('content')
    <div class="flex justify-center bg-white">
        <div class="p-4 max-w-5xl w-full">
            <h1 class="text-2xl">Edit Watcher</h1>
            <watcher-form
                :intervals="{{ $intervals }}"
                :regions="{{ $regions }}"
                :watcher="{{ $watcher }}"
                type="Update"
            ></watcher-form>
        </div>
    </div>

@endsection
