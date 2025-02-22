@extends('eval::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('eval.name') !!}</p>
@endsection
