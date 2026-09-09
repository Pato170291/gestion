@extends('layouts.app')

@section('title', 'Nueva compra')

@section('content')
    <h1>Nueva compra</h1>
    @include('partials.form-compra', ['compra' => null, 'action' => '/compras'])
@endsection