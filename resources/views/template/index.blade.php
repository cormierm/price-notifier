@extends('layouts.vue')

@section('content')
    <template-index :templates="{{ json_encode($templates) }}"></template-index>
@endsection
