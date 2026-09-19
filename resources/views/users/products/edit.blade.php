@extends('users.layout')

@section('title', __('products.edit_product') . ' | ' . ($product->name ?? __('products.edit_product')))
@section('page_title', __('products.edit_product'))

@section('content')
    @include('users.products.form', [
        'isEdit' => true,
        'product' => $product,
        'business' => $business,
        'categories' => $categories
    ])
@endsection
