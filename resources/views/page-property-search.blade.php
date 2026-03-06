{{--
  Template Name: Property Search
  Displays the property search hub. Create a Page with slug
  "property-search" and assign this template.
--}}
@extends('layouts.app')

@section('content')

{{-- Swap global $wp_query so paginate_links() works inside the partial --}}
@php
  global $wp_query;
  $__origQuery = $wp_query;
  $wp_query    = $propertyQuery;
@endphp

@include('partials.archive-property-body')

@php
  $wp_query = $__origQuery;
@endphp

@endsection
