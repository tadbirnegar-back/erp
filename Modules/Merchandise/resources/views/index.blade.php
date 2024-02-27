@extends('merchandise::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('merchandise.name') !!}</p>
@endsection
