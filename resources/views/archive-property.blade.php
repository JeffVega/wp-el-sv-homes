@extends('layouts.app')

@section('content')

{{-- Page header --}}
<div class="sv-page-hero py-20 pb-12">
  <div class="sv-container">
    <div class="sv-section-eyebrow text-sv-gold-light">
      {{ __('Real Estate', 'sage') }}
    </div>
    <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-white mb-2">
      {{ __('Properties in El Salvador', 'sage') }}
    </h1>
    <p class="text-white/70 text-[0.95rem]">
      {{ $totalFound }} {{ __('properties found', 'sage') }}
    </p>
  </div>
</div>

<div class="sv-container py-10">

  {{-- Quick search bar --}}
  <div class="mb-8">
    @include('partials.property-search', [
      'types'     => $propertyTypes,
      'statuses'  => $propertyStatus,
      'locations' => $locations,
      'compact'   => true,
    ])
  </div>

  <div class="sv-archive-layout">

    {{-- ── Filter Sidebar ────────────────────────────── --}}
    <aside class="sv-filter-sidebar" aria-label="{{ __('Filters', 'sage') }}">
      <div class="sv-filter-sidebar__header">
        <span>🔍 {{ __('Filters', 'sage') }}</span>
        @if(array_filter($currentFilters))
          <a href="{{ get_post_type_archive_link('property') }}" style="color:rgba(255,255,255,0.7);font-size:0.78rem;text-decoration:none;">
            {{ __('Clear', 'sage') }}
          </a>
        @endif
      </div>

      <form class="sv-filter-sidebar__body" method="GET" action="{{ get_post_type_archive_link('property') }}">

        {{-- Keyword --}}
        <div class="sv-filter-group">
          <label class="sv-filter-label" for="sf-keyword">{{ __('Search', 'sage') }}</label>
          <input type="text" id="sf-keyword" name="keyword" class="sv-filter-input"
                 value="{{ $currentFilters['keyword'] }}" placeholder="{{ __('Keyword…', 'sage') }}">
        </div>

        {{-- Status --}}
        <div class="sv-filter-group">
          <label class="sv-filter-label" for="sf-status">{{ __('Type', 'sage') }}</label>
          <select id="sf-status" name="property_status" class="sv-filter-input">
            <option value="">{{ __('All', 'sage') }}</option>
            @foreach($propertyStatus as $term)
              <option value="{{ $term->slug }}" {{ $currentFilters['status'] === $term->slug ? 'selected' : '' }}>
                {{ $term->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Type --}}
        <div class="sv-filter-group">
          <label class="sv-filter-label" for="sf-type">{{ __('Type', 'sage') }}</label>
          <select id="sf-type" name="property_type" class="sv-filter-input">
            <option value="">{{ __('All types', 'sage') }}</option>
            @foreach($propertyTypes as $term)
              <option value="{{ $term->slug }}" {{ $currentFilters['type'] === $term->slug ? 'selected' : '' }}>
                {{ $term->name }} ({{ $term->count }})
              </option>
            @endforeach
          </select>
        </div>

        {{-- Location --}}
        <div class="sv-filter-group">
          <label class="sv-filter-label" for="sf-location">{{ __('Department', 'sage') }}</label>
          <select id="sf-location" name="location" class="sv-filter-input">
            <option value="">{{ __('All of El Salvador', 'sage') }}</option>
            @foreach($locations as $term)
              <option value="{{ $term->slug }}" {{ $currentFilters['location'] === $term->slug ? 'selected' : '' }}>
                {{ $term->name }}
              </option>
            @endforeach
          </select>
        </div>

        {{-- Price Range --}}
        <div class="sv-filter-group">
          <label class="sv-filter-label">{{ __('Price range (USD)', 'sage') }}</label>
          <div class="sv-price-range">
            <input type="number" name="min_price" class="sv-filter-input"
                   value="{{ $currentFilters['min'] }}" placeholder="Min" min="0" step="1000">
            <input type="number" name="max_price" class="sv-filter-input"
                   value="{{ $currentFilters['max'] }}" placeholder="Max" min="0" step="1000">
          </div>
        </div>

        {{-- Bedrooms --}}
        <div class="sv-filter-group">
          <label class="sv-filter-label" for="sf-beds">{{ __('Min. bedrooms', 'sage') }}</label>
          <select id="sf-beds" name="bedrooms" class="sv-filter-input">
            <option value="">{{ __('Any number', 'sage') }}</option>
            @foreach([1,2,3,4,5] as $n)
              <option value="{{ $n }}" {{ $currentFilters['beds'] == $n ? 'selected' : '' }}>
                {{ $n }}{{ $n === 5 ? '+' : '' }}
              </option>
            @endforeach
          </select>
        </div>

        <button type="submit" class="sv-btn sv-btn-primary" style="width:100%;justify-content:center;margin-top:0.5rem;">
          {{ __('Apply filters', 'sage') }}
        </button>

      </form>
    </aside>

    {{-- ── Property Listings ─────────────────────────── --}}
    <div>
      {{-- Results header --}}
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:0.75rem;">
        <p style="font-size:0.9rem;color:var(--color-sv-stone);font-weight:500;">
          @if($totalFound > 0)
            {{ sprintf(__('%d properties found', 'sage'), $totalFound) }}
          @else
            {{ __('No properties found', 'sage') }}
          @endif
        </p>
        <div style="display:flex;gap:0.5rem;align-items:center;">
          <label style="font-size:0.8rem;color:var(--color-sv-stone);">{{ __('Sort:', 'sage') }}</label>
          <select
            id="sv-sort"
            style="border:2px solid var(--color-sv-stone-light);border-radius:var(--radius-btn);padding:0.4rem 0.75rem;font-size:0.82rem;color:var(--color-sv-navy);"
            onchange="window.location.href=this.value"
          >
            <option value="{{ add_query_arg('orderby', 'date', '') }}">{{ __('Most recent', 'sage') }}</option>
            <option value="{{ add_query_arg(['orderby' => 'meta_value_num', 'meta_key' => '_sv_price', 'order' => 'ASC'], '') }}">
              {{ __('Price ↑', 'sage') }}
            </option>
            <option value="{{ add_query_arg(['orderby' => 'meta_value_num', 'meta_key' => '_sv_price', 'order' => 'DESC'], '') }}">
              {{ __('Price ↓', 'sage') }}
            </option>
          </select>
        </div>
      </div>

      @if(!empty($properties))
        <div class="sv-property-grid">
          @foreach($properties as $property)
            @include('partials.property-card', ['property' => $property])
          @endforeach
        </div>

        {{-- Pagination --}}
        <nav class="sv-pagination" aria-label="{{ __('Pagination', 'sage') }}">
          {!! paginate_links([
            'type'      => 'list',
            'prev_text' => '←',
            'next_text' => '→',
          ]) !!}
        </nav>

      @else
        <div style="text-align:center;padding:4rem 2rem;background:#fff;border-radius:var(--radius-card);box-shadow:var(--shadow-card);">
          <div style="font-size:4rem;margin-bottom:1rem;">🏘️</div>
          <h3 style="font-family:var(--font-display);color:var(--color-sv-navy);margin-bottom:0.75rem;">
            {{ __('No properties found', 'sage') }}
          </h3>
          <p style="color:var(--color-sv-stone);margin-bottom:1.5rem;">
            {{ __('Try adjusting the filters or search all of El Salvador.', 'sage') }}
          </p>
          <a href="{{ get_post_type_archive_link('property') }}" class="sv-btn sv-btn-primary">
            {{ __('View all properties', 'sage') }}
          </a>
        </div>
      @endif
    </div>

  </div>{{-- .sv-archive-layout --}}
</div>

@endsection
