@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <watcher-form :watcher="{{ $watcher }}" type="Update"></watcher-form>
            </div>
        </div>
    </div>
@endsection
