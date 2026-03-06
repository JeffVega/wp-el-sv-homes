{{--
  Property Search Bar
  Optional: $types, $statuses, $locations, $compact
--}}
@php
  $compact    = $compact ?? false;
  $archiveUrl = home_url('/property-search');
@endphp

@if($compact)
  {{-- Compact layout (archive pages): single-row bar with dividers --}}
  <form
    action="{{ $archiveUrl }}"
    method="GET"
    class="sv-search-bar sv-search-bar--compact"
    role="search"
    aria-label="{{ __('Search properties', 'sage') }}"
  >
    <div class="sv-search-bar__fields">
      <div class="sv-search-field sv-search-field--keyword">
        <label for="sv-search-keyword">{{ __('Keyword', 'sage') }}</label>
        <input
          type="text"
          id="sv-search-keyword"
          name="keyword"
          value="{{ sanitize_text_field($_GET['keyword'] ?? '') }}"
          placeholder="{{ __('House, apartment, land…', 'sage') }}"
          autocomplete="off"
        >
      </div>

      <div class="sv-search-bar__divider" aria-hidden="true"></div>

      <div class="sv-search-field">
        <label for="sv-search-status">{{ __('Status', 'sage') }}</label>
        <select id="sv-search-status" name="property_status">
          <option value="">{{ __('Any', 'sage') }}</option>
          @if(!empty($statuses))
            @foreach($statuses as $term)
              <option value="{{ $term->slug }}" {{ (($_GET['property_status'] ?? '') === $term->slug) ? 'selected' : '' }}>
                {{ $term->name }}
              </option>
            @endforeach
          @else
            <option value="for-sale">{{ __('For Sale', 'sage') }}</option>
            <option value="for-rent">{{ __('For Rent', 'sage') }}</option>
          @endif
        </select>
      </div>

      <div class="sv-search-bar__divider" aria-hidden="true"></div>

      <div class="sv-search-field">
        <label for="sv-search-type">{{ __('Type', 'sage') }}</label>
        <select id="sv-search-type" name="property_type">
          <option value="">{{ __('All types', 'sage') }}</option>
          @if(!empty($types))
            @foreach($types as $term)
              <option value="{{ $term->slug }}" {{ (($_GET['property_type'] ?? '') === $term->slug) ? 'selected' : '' }}>
                {{ $term->name }}
              </option>
            @endforeach
          @endif
        </select>
      </div>

      <div class="sv-search-bar__divider" aria-hidden="true"></div>

      <div class="sv-search-field">
        <label for="sv-search-location">{{ __('Location', 'sage') }}</label>
        <select id="sv-search-location" name="location">
          <option value="">{{ __('All of El Salvador', 'sage') }}</option>
          @if(!empty($locations))
            @foreach($locations as $term)
              <option value="{{ $term->slug }}" {{ (($_GET['location'] ?? '') === $term->slug) ? 'selected' : '' }}>
                {{ $term->name }}
              </option>
            @endforeach
          @endif
        </select>
      </div>
    </div>

    <button type="submit" class="sv-btn-search">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>
      {{ __('Search', 'sage') }}
    </button>
  </form>
@else
  {{-- Hero layout: full-width single-row bar --}}
  <form
    action="{{ $archiveUrl }}"
    method="GET"
    class="sv-search-bar sv-search-bar--sandwich"
    role="search"
    aria-label="{{ __('Search properties', 'sage') }}"
  >
    <div class="sv-search-bar__fields">
      <div class="sv-search-field sv-search-field--keyword">
        <label for="sv-search-keyword">{{ __('Keyword', 'sage') }}</label>
        <input
          type="text"
          id="sv-search-keyword"
          name="keyword"
          value="{{ sanitize_text_field($_GET['keyword'] ?? '') }}"
          placeholder="{{ __('House, apartment, land…', 'sage') }}"
          autocomplete="off"
        >
      </div>

      <div class="sv-search-bar__divider" aria-hidden="true"></div>

      <div class="sv-search-field">
        <label for="sv-search-status">{{ __('Status', 'sage') }}</label>
        <select id="sv-search-status" name="property_status">
          <option value="">{{ __('Any', 'sage') }}</option>
          @if(!empty($statuses))
            @foreach($statuses as $term)
              <option value="{{ $term->slug }}" {{ (($_GET['property_status'] ?? '') === $term->slug) ? 'selected' : '' }}>
                {{ $term->name }}
              </option>
            @endforeach
          @else
            <option value="for-sale">{{ __('For Sale', 'sage') }}</option>
            <option value="for-rent">{{ __('For Rent', 'sage') }}</option>
          @endif
        </select>
      </div>

      <div class="sv-search-bar__divider" aria-hidden="true"></div>

      <div class="sv-search-field">
        <label for="sv-search-type">{{ __('Type', 'sage') }}</label>
        <select id="sv-search-type" name="property_type">
          <option value="">{{ __('All types', 'sage') }}</option>
          @if(!empty($types))
            @foreach($types as $term)
              <option value="{{ $term->slug }}" {{ (($_GET['property_type'] ?? '') === $term->slug) ? 'selected' : '' }}>
                {{ $term->name }}
              </option>
            @endforeach
          @endif
        </select>
      </div>

      <div class="sv-search-bar__divider" aria-hidden="true"></div>

      <div class="sv-search-field">
        <label for="sv-search-location">{{ __('Location', 'sage') }}</label>
        <select id="sv-search-location" name="location">
          <option value="">{{ __('All of El Salvador', 'sage') }}</option>
          @if(!empty($locations))
            @foreach($locations as $term)
              <option value="{{ $term->slug }}" {{ (($_GET['location'] ?? '') === $term->slug) ? 'selected' : '' }}>
                {{ $term->name }}
              </option>
            @endforeach
          @endif
        </select>
      </div>

      <div class="sv-search-bar__divider" aria-hidden="true"></div>

      <div class="sv-search-field">
        <label for="sv-search-price">{{ __('Max Price', 'sage') }}</label>
        <select id="sv-search-price" name="max_price">
          <option value="">{{ __('Any', 'sage') }}</option>
          <option value="50000" {{ (($_GET['max_price'] ?? '') === '50000') ? 'selected' : '' }}>&le; $50k</option>
          <option value="100000" {{ (($_GET['max_price'] ?? '') === '100000') ? 'selected' : '' }}>&le; $100k</option>
          <option value="150000" {{ (($_GET['max_price'] ?? '') === '150000') ? 'selected' : '' }}>&le; $150k</option>
          <option value="250000" {{ (($_GET['max_price'] ?? '') === '250000') ? 'selected' : '' }}>&le; $250k</option>
          <option value="500000" {{ (($_GET['max_price'] ?? '') === '500000') ? 'selected' : '' }}>&le; $500k</option>
        </select>
      </div>
    </div>

    <button type="submit" class="sv-btn-search">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
      </svg>
      {{ __('Search', 'sage') }}
    </button>
  </form>
@endif
