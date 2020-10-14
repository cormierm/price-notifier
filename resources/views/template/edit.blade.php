@extends('layouts.vue')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h1 class="title">Edit Domain Query</h1>
            <div class="col-md-8">
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <template-form :template="{{ $template }}" type="Update"></template-form>
            </div>
        </div>
    </div>
@endsection
