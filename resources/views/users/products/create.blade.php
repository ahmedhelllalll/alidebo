@extends('users.layout')

@section('title', __('products.add_product') . ' | ' . ($business->name ?? config('app.name')))
@section('page_title', __('products.add_product'))

@section('content')
    @include('users.products.form', [
        'isEdit' => false,
        'business' => $business,
        'categories' => $categories
    ])
@endsection
