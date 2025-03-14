@extends('acc::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('acc.name') !!}</p>
@endsection
