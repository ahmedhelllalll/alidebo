@extends('layouts.app')

@section('title', __('landing.title') ?? 'The Ultimate Business Directory')
@section('meta_description', __('landing.meta_description') ?? 'Find the perfect business or build your empire. The ultimate directory for clients and companies.')

@section('content')
    @include('landing.companies.hero-search')
    @include('landing.companies.featured')
    @include('landing.companies.cta-ads')
@endsection
