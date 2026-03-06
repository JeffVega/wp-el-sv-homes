{{-- Shared body for property archive and location taxonomy archive (SEO-friendly location pages). --}}
<div class="sv-page-hero py-20 pb-12">
  <div class="sv-container">
    <div class="sv-section-eyebrow text-sv-gold-light">
      {{ __('Real Estate', 'sage') }}
    </div>
    <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-white mb-2">
      {{ $archiveTitle ?? __('Properties in El Salvador', 'sage') }}
    </h1>
    <p class="text-white/70 text-[0.95rem]">
      {{ $totalFound }} {{ __('properties found', 'sage') }}
    </p>
    @if(!empty($termDescription))
      <div class="text-white/75 text-sm mt-3 max-w-2xl leading-relaxed">
        {!! $termDescription !!}
      </div>
    @endif
  </div>
</div>

<div class="sv-container py-10">

  {{-- Desktop search bar (hidden on mobile — use the filter sidebar's keyword field) --}}
  <div class="mb-8 sv-search-bar-desktop-only">
    @include('partials.property-search', [
      'types'     => $propertyTypes,
      'statuses'  => $propertyStatus,
      'locations' => $locations,
      'compact'   => true,
    ])
  </div>

  {{-- Mobile search + filter toggle bar --}}
  <div class="sv-mobile-filter-bar">
    <form class="sv-mobile-filter-bar__search" action="{{ $formAction }}" method="GET">
      <input
        type="text"
        name="keyword"
        class="sv-mobile-search-input"
        value="{{ $currentFilters['keyword'] }}"
        placeholder="{{ __('Search properties…', 'sage') }}"
        autocomplete="off"
      >
      <button type="submit" class="sv-mobile-search-btn" aria-label="{{ __('Search', 'sage') }}">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
      </button>
    </form>
    <button type="button" id="sv-filter-toggle" class="sv-mobile-filter-toggle" aria-expanded="false" aria-controls="sv-filter-sidebar">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
      {{ __('Filters', 'sage') }}
      <span id="sv-filter-badge" class="sv-filter-badge" style="display:none;"></span>
    </button>
  </div>

  <div class="sv-archive-layout">

    <aside id="sv-filter-sidebar" class="sv-filter-sidebar" aria-label="{{ __('Filters', 'sage') }}">
      <div class="sv-filter-sidebar__header" id="sv-filter-sidebar-header">
        <span>🔍 {{ __('Filters', 'sage') }}</span>
        <div style="display:flex;align-items:center;gap:0.75rem;">
          @if(array_filter($currentFilters))
            <a href="{{ get_post_type_archive_link('property') }}" style="color:rgba(255,255,255,0.7);font-size:0.78rem;text-decoration:none;">
              {{ __('Clear', 'sage') }}
            </a>
          @endif
          <span class="sv-filter-sidebar__chevron" aria-hidden="true">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><polyline points="18 15 12 9 6 15"/></svg>
          </span>
        </div>
      </div>

      <form id="sv-filter-body" class="sv-filter-sidebar__body" method="GET" action="{{ $formAction }}">

        <div class="sv-filter-group">
          <label class="sv-filter-label" for="sf-keyword">{{ __('Search', 'sage') }}</label>
          <input type="text" id="sf-keyword" name="keyword" class="sv-filter-input"
                 value="{{ $currentFilters['keyword'] }}" placeholder="{{ __('Keyword…', 'sage') }}">
        </div>

        <div class="sv-filter-group">
          <label class="sv-filter-label" for="sf-status">{{ __('Status', 'sage') }}</label>
          <select id="sf-status" name="property_status" class="sv-filter-input">
            <option value="">{{ __('All', 'sage') }}</option>
            @foreach($propertyStatus as $term)
              <option value="{{ $term->slug }}" {{ $currentFilters['status'] === $term->slug ? 'selected' : '' }}>
                {{ $term->name }}
              </option>
            @endforeach
          </select>
        </div>

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

        <div class="sv-filter-group">
          <label class="sv-filter-label">{{ __('Price range (USD)', 'sage') }}</label>
          <div class="sv-price-range">
            <input type="number" name="min_price" class="sv-filter-input"
                   value="{{ $currentFilters['min'] }}" placeholder="Min" min="0" step="1000">
            <input type="number" name="max_price" class="sv-filter-input"
                   value="{{ $currentFilters['max'] }}" placeholder="Max" min="0" step="1000">
          </div>
        </div>

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

    <div>
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

  </div>
</div>
