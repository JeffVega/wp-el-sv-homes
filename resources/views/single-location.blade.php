@extends('layouts.app')

@section('content')

@php
  $locId      = get_the_ID();
  $parentId   = (int) wp_get_post_parent_id($locId);
  $isChildLoc = $parentId > 0;
@endphp

@if($isChildLoc)
  {{-- Child location: use the same cinematic hero + content layout as page-child --}}
  @php
    $heroImg     = get_the_post_thumbnail_url($locId, 'full');
    $parentTitle = get_the_title($parentId);
    $parentUrl   = get_permalink($parentId);
  @endphp

  <div
    class="sv-child-hero {{ $heroImg ? 'sv-child-hero--has-image' : '' }}"
    @if($heroImg) style="--hero-img: url('{{ esc_url($heroImg) }}')" @endif
  >
    <div class="sv-child-hero__bg"      aria-hidden="true"></div>
    <div class="sv-child-hero__overlay" aria-hidden="true"></div>
    <div class="sv-child-hero__pattern" aria-hidden="true"></div>

    <div class="sv-container sv-child-hero__content">

      <nav class="sv-child-breadcrumbs sv-fade-up" aria-label="{{ __('Breadcrumb', 'sage') }}">
        <a href="{{ home_url('/') }}" class="sv-child-breadcrumbs__link">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
          </svg>
          {{ __('Home', 'sage') }}
        </a>
        <span class="sv-child-breadcrumbs__sep" aria-hidden="true">›</span>
        <a href="{{ esc_url($parentUrl) }}" class="sv-child-breadcrumbs__link">{{ $parentTitle }}</a>
        <span class="sv-child-breadcrumbs__sep" aria-hidden="true">›</span>
        <span class="sv-child-breadcrumbs__current" aria-current="page">{{ get_the_title() }}</span>
      </nav>

      <div class="sv-section-eyebrow text-sv-gold-light mb-3 sv-fade-up sv-fade-up--delay-1">
        {{ $parentTitle }}
      </div>

      <h1 class="sv-child-hero__title sv-fade-up sv-fade-up--delay-1">
        {{ get_the_title() }}
      </h1>

      <div class="sv-child-hero__accent sv-fade-up sv-fade-up--delay-2" aria-hidden="true"></div>

      @if(has_excerpt())
        <p class="sv-child-hero__excerpt sv-fade-up sv-fade-up--delay-3">
          {{ get_the_excerpt() }}
        </p>
      @endif

    </div>
  </div>

  <section class="sv-child-content">
    <div class="sv-container">
      <div class="sv-child-content__inner">

        <a href="{{ esc_url($parentUrl) }}" class="sv-child-back">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
            <path d="M19 12H5M12 5l-7 7 7 7"/>
          </svg>
          {{ sprintf(__('Back to %s', 'sage'), $parentTitle) }}
        </a>

        <div class="sv-prose">
          @php(the_content())@endphp
        </div>

      </div>
    </div>
  </section>

@else
{{-- Top-level location: full location guide with hero, editorial content, and property grid --}}
@php
  $heroImg  = get_the_post_thumbnail_url($locId, 'full');
  $cityName = get_the_title();
  $content  = get_the_content();
  $paged    = max(1, (int) (get_query_var('paged') ?: get_query_var('page')));
  $hasFilters = array_filter($currentFilters ?? []);
@endphp

{{-- ── HERO ────────────────────────────────────────────────── --}}
<section class="sv-loc-hero {{ $heroImg ? '' : 'sv-loc-hero--no-image' }}" aria-label="{{ $cityName }}">

  @if($heroImg)
    <div class="sv-loc-hero__bg">
      <img src="{{ esc_url($heroImg) }}" alt="{{ esc_attr($cityName) }}">
      <div class="sv-loc-hero__overlay"></div>
    </div>
  @else
    <div class="sv-loc-hero__overlay"></div>
    <div class="sv-loc-hero__topo" aria-hidden="true"></div>
  @endif

  <div class="sv-loc-hero__inner">

    {{-- Breadcrumb --}}
    <nav class="sv-loc-hero__nav" aria-label="{{ __('Breadcrumb', 'sage') }}">
      <a href="{{ home_url('/') }}">{{ __('Home', 'sage') }}</a>
      <span>›</span>
      @if($activePropertyType)
        <a href="{{ get_permalink() }}">{{ $cityName }}</a>
        <span>›</span>
        {{ $activePropertyType->name }}
      @else
        {{ $cityName }}
      @endif
    </nav>

    {{-- Editorial title block --}}
    <div class="sv-loc-hero__body">
      <div class="sv-loc-hero__label">{{ $activePropertyType ? $activePropertyType->name : __('Location Guide', 'sage') }}</div>
      <h1 class="sv-loc-hero__name">{{ sprintf(__('Buying Homes in %s', 'sage'), $cityName) }}</h1>

      <div class="sv-loc-hero__foot">
        <div class="sv-loc-hero__stats">
          <div>
            <span class="sv-loc-hero__stat-num">{{ $totalFound }}</span>
            <span class="sv-loc-hero__stat-label">{{ __('Properties', 'sage') }}</span>
          </div>
          @if($termSlug)
            <div>
              <span class="sv-loc-hero__stat-num">El Salvador</span>
              <span class="sv-loc-hero__stat-label">{{ __('Location', 'sage') }}</span>
            </div>
          @endif
        </div>
        <div class="sv-loc-hero__scroll" aria-hidden="true">
          <div class="sv-loc-hero__scroll-line"></div>
          <span>{{ __('Scroll', 'sage') }}</span>
        </div>
      </div>
    </div>

  </div>
</section>

{{-- ── EDITORIAL CONTENT ───────────────────────────────────── --}}
@if($content)
  <section class="sv-loc-editorial">
    <div class="sv-container">
      <div class="sv-loc-editorial__wrap">
        <div class="sv-loc-editorial__kicker">
          {{ sprintf(__('About %s', 'sage'), $cityName) }}
        </div>
        <div class="sv-loc-editorial__body">
          {!! wp_kses_post(apply_filters('the_content', $content)) !!}
        </div>
      </div>
    </div>
  </section>
@endif

{{-- ── PROPERTIES ──────────────────────────────────────────── --}}
<section class="sv-loc-properties">
  <div class="sv-container">

    {{-- Section header --}}
    <div class="sv-loc-properties__head">
      <div>
        <h2 class="sv-loc-properties__title">
          @if($activePropertyType)
            {{ sprintf(__('%s in %s', 'sage'), $activePropertyType->name, $cityName) }}
          @else
            {{ sprintf(__('Properties in %s', 'sage'), $cityName) }}
          @endif
        </h2>
        <p class="sv-loc-properties__sub">
          @if($totalFound > 0)
            {{ sprintf(__('%d listings available', 'sage'), $totalFound) }}
          @else
            {{ __('No listings at this time', 'sage') }}
          @endif
        </p>
      </div>
      <a href="{{ get_post_type_archive_link('property') }}" class="sv-btn sv-btn-outline sv-btn-sm">
        {{ __('All properties', 'sage') }} →
      </a>
    </div>

    {{-- Mobile filter toggle --}}
    <button class="sv-loc-filter-toggle" id="sv-loc-filter-toggle" type="button">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="6" y1="12" x2="18" y2="12"/><line x1="9" y1="18" x2="15" y2="18"/></svg>
      {{ __('Filters', 'sage') }}
      @if($hasFilters)
        <span style="background:var(--color-sv-gold);color:var(--color-sv-navy);font-size:0.65rem;font-weight:800;padding:0.1rem 0.45rem;border-radius:999px;margin-left:0.25rem;">
          {{ count(array_filter($currentFilters)) }}
        </span>
      @endif
    </button>

    {{-- Horizontal filter bar --}}
    <div class="sv-loc-filter" id="sv-loc-filter">
      <form class="sv-loc-filter__row" method="GET" action="{{ $formAction }}">

        <div class="sv-loc-filter__group">
          <label class="sv-loc-filter__lbl" for="lf-keyword">{{ __('Search', 'sage') }}</label>
          <input class="sv-loc-filter__input" type="text" id="lf-keyword" name="keyword"
                 value="{{ $currentFilters['keyword'] }}" placeholder="{{ __('Keyword…', 'sage') }}">
        </div>

        <div class="sv-loc-filter__group sv-loc-filter__group--md">
          <label class="sv-loc-filter__lbl" for="lf-status">{{ __('Status', 'sage') }}</label>
          <select class="sv-loc-filter__select" id="lf-status" name="property_status">
            <option value="">{{ __('Any status', 'sage') }}</option>
            @foreach($propertyStatus as $term)
              <option value="{{ $term->slug }}" {{ $currentFilters['status'] === $term->slug ? 'selected' : '' }}>
                {{ $term->name }}
              </option>
            @endforeach
          </select>
        </div>

        @unless($activePropertyType)
        <div class="sv-loc-filter__group sv-loc-filter__group--md">
          <label class="sv-loc-filter__lbl" for="lf-type">{{ __('Type', 'sage') }}</label>
          <select class="sv-loc-filter__select" id="lf-type" name="property_type">
            <option value="">{{ __('Any type', 'sage') }}</option>
            @foreach($propertyTypes as $term)
              <option value="{{ $term->slug }}" {{ $currentFilters['type'] === $term->slug ? 'selected' : '' }}>
                {{ $term->name }}
              </option>
            @endforeach
          </select>
        </div>
        @endunless

        <div class="sv-loc-filter__group">
          <label class="sv-loc-filter__lbl">{{ __('Price (USD)', 'sage') }}</label>
          <div class="sv-loc-filter__price">
            <input class="sv-loc-filter__input" type="number" name="min_price"
                   value="{{ $currentFilters['min'] }}" placeholder="Min" min="0" step="5000">
            <span class="sv-loc-filter__price-sep">—</span>
            <input class="sv-loc-filter__input" type="number" name="max_price"
                   value="{{ $currentFilters['max'] }}" placeholder="Max" min="0" step="5000">
          </div>
        </div>

        <div class="sv-loc-filter__group sv-loc-filter__group--sm">
          <label class="sv-loc-filter__lbl" for="lf-beds">{{ __('Beds', 'sage') }}</label>
          <select class="sv-loc-filter__select" id="lf-beds" name="bedrooms">
            <option value="">{{ __('Any', 'sage') }}</option>
            @foreach([1,2,3,4,5] as $n)
              <option value="{{ $n }}" {{ $currentFilters['beds'] == $n ? 'selected' : '' }}>
                {{ $n }}{{ $n === 5 ? '+' : '' }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="sv-loc-filter__actions">
          <button class="sv-loc-filter__btn" type="submit">{{ __('Search', 'sage') }}</button>
          @if($hasFilters)
            <a class="sv-loc-filter__clear" href="{{ $formAction }}">{{ __('Clear', 'sage') }}</a>
          @endif
        </div>

      </form>
    </div>

    {{-- Grid toolbar --}}
    <div class="sv-loc-grid-bar">
      <span class="sv-loc-grid-bar__count">
        @if($totalFound > 0)
          {{ sprintf(__('%d properties found', 'sage'), $totalFound) }}
        @else
          {{ __('No properties found', 'sage') }}
        @endif
      </span>
      <div class="sv-loc-grid-bar__sort">
        <span>{{ __('Sort:', 'sage') }}</span>
        <select onchange="window.location.href=this.value">
          <option value="{{ add_query_arg('orderby', 'date', '') }}">{{ __('Most recent', 'sage') }}</option>
          <option value="{{ add_query_arg(['orderby' => 'meta_value_num', 'meta_key' => '_sv_price', 'order' => 'ASC'], '') }}">{{ __('Price ↑', 'sage') }}</option>
          <option value="{{ add_query_arg(['orderby' => 'meta_value_num', 'meta_key' => '_sv_price', 'order' => 'DESC'], '') }}">{{ __('Price ↓', 'sage') }}</option>
        </select>
      </div>
    </div>

    {{-- Property grid --}}
    @if(!empty($properties))
      <div class="sv-property-grid">
        @foreach($properties as $property)
          @include('partials.property-card', ['property' => $property])
        @endforeach
      </div>

      @if($maxPages > 1)
        <nav class="sv-pagination" aria-label="{{ __('Pagination', 'sage') }}">
          {!! paginate_links([
            'total'     => $maxPages,
            'current'   => $paged,
            'type'      => 'list',
            'prev_text' => '←',
            'next_text' => '→',
          ]) !!}
        </nav>
      @endif

    @else
      <div style="text-align:center;padding:4rem 2rem;background:#fff;border-radius:var(--radius-card);box-shadow:var(--shadow-card);">
        <div style="font-size:3.5rem;margin-bottom:1rem;">🏘️</div>
        <h3 style="font-family:var(--font-display);color:var(--color-sv-navy);font-size:1.25rem;margin-bottom:0.75rem;">
          {{ __('No properties found', 'sage') }}
        </h3>
        <p style="color:var(--color-sv-stone);margin-bottom:1.5rem;font-size:0.9rem;">
          {{ __('Try adjusting the filters or browse all listings.', 'sage') }}
        </p>
        <a href="{{ $formAction }}" class="sv-btn sv-btn-primary">{{ __('Clear filters', 'sage') }}</a>
      </div>
    @endif

  </div>
</section>

<script>
(function () {
  var toggle = document.getElementById('sv-loc-filter-toggle');
  var filter = document.getElementById('sv-loc-filter');
  if (!toggle || !filter) return;
  toggle.addEventListener('click', function () {
    filter.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', filter.classList.contains('is-open'));
  });
})();
</script>

@endif

@endsection
