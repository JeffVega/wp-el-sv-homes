{{--
  Template Name: Child Page
  Template Post Type: page, location
  Cinematic hero (featured image or navy gradient fallback),
  breadcrumbs, H1 title, and an editorial content column.
--}}
@extends('layouts.app')

@section('content')
@while(have_posts())
@php
  the_post();
  $heroImg     = get_the_post_thumbnail_url(get_the_ID(), 'full');
  $parentId    = (int) wp_get_post_parent_id(get_the_ID());
  $parentTitle = $parentId ? get_the_title($parentId) : null;
  $parentUrl   = $parentId ? get_permalink($parentId) : null;
@endphp

{{-- ── Hero ────────────────────────────────────────────────── --}}
<div
  class="sv-child-hero {{ $heroImg ? 'sv-child-hero--has-image' : '' }}"
  @if($heroImg) style="--hero-img: url('{{ esc_url($heroImg) }}')" @endif
>
  <div class="sv-child-hero__bg"      aria-hidden="true"></div>
  <div class="sv-child-hero__overlay" aria-hidden="true"></div>
  <div class="sv-child-hero__pattern" aria-hidden="true"></div>

  <div class="sv-container sv-child-hero__content">

    {{-- Breadcrumbs --}}
    <nav class="sv-child-breadcrumbs sv-fade-up" aria-label="{{ __('Breadcrumb', 'sage') }}">
      <a href="{{ home_url('/') }}" class="sv-child-breadcrumbs__link">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
        </svg>
        {{ __('Home', 'sage') }}
      </a>
      @if($parentTitle && $parentUrl)
        <span class="sv-child-breadcrumbs__sep" aria-hidden="true">›</span>
        <a href="{{ esc_url($parentUrl) }}" class="sv-child-breadcrumbs__link">{{ $parentTitle }}</a>
      @endif
      <span class="sv-child-breadcrumbs__sep" aria-hidden="true">›</span>
      <span class="sv-child-breadcrumbs__current" aria-current="page">{{ get_the_title() }}</span>
    </nav>

    {{-- Eyebrow --}}
    @if($parentTitle)
      <div class="sv-section-eyebrow text-sv-gold-light mb-3 sv-fade-up sv-fade-up--delay-1">
        {{ $parentTitle }}
      </div>
    @endif

    {{-- H1 --}}
    <h1 class="sv-child-hero__title sv-fade-up sv-fade-up--delay-1">
      {{ get_the_title() }}
    </h1>

    {{-- Gold accent bar --}}
    <div class="sv-child-hero__accent sv-fade-up sv-fade-up--delay-2" aria-hidden="true"></div>

    {{-- Optional excerpt as sub-heading --}}
    @if(has_excerpt())
      <p class="sv-child-hero__excerpt sv-fade-up sv-fade-up--delay-3">
        {{ get_the_excerpt() }}
      </p>
    @endif

  </div>
</div>

{{-- ── Content ──────────────────────────────────────────────── --}}
<section class="sv-child-content">
  <div class="sv-container">
    <div class="sv-child-content__inner">

      {{-- Back to parent --}}
      @if($parentTitle && $parentUrl)
        <a href="{{ esc_url($parentUrl) }}" class="sv-child-back">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
          </svg>
          {{ sprintf(__('Back to %s', 'sage'), $parentTitle) }}
        </a>
      @endif

      {{-- Page body --}}
      <div class="sv-prose">
        @php(the_content())
      </div>

    </div>
  </div>
</section>

@endwhile
@endsection
