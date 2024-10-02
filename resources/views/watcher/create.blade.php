@extends('layouts.vue')

@section('content')
    <div class="flex justify-center bg-white">
        <div class="p-4 max-w-5xl w-full">
            <h1 class="text-2xl">Create Watcher</h1>
            <watcher-form
                class="mt-4"
                :intervals="{{ $intervals }}"
                :regions="{{ $regions }}"
            ></watcher-form>
        </div>
    </div>
@endsection
