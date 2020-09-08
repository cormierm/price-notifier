@extends('layouts.vue')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h1 class="title">Edit Watcher</h1>
            <div class="col-md-8">
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <watcher-form
                    :intervals="{{ $intervals }}"
                    :regions="{{ $regions }}"
                    :watcher="{{ $watcher }}"
                    type="Update"
                ></watcher-form>
            </div>
        </div>
    </div>
@endsection
