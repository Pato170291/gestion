@extends('layouts.app')

@section('title', 'Editar compra')

@section('content')
    <h1>Editar compra</h1>
    @include('partials.form-compra', ['action' => '/compras/' . $compra->id])
@endsection