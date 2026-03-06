{{--
  Featured Listings Spotlight
  Magazine-style asymmetric layout: one hero card + 2 mini cards + "see all" tile.

  Required: $properties  (array from FrontPage::hydrateProperties, ideally 3 items)
  Optional: $sectionTitle   (string)
            $sectionEyebrow (string)
            $viewAllUrl     (string)
--}}
@php
  $sectionTitle   = $sectionTitle   ?? __('Properties in the Spotlight', 'sage');
  $sectionEyebrow = $sectionEyebrow ?? __('Hand-picked for you', 'sage');
  $viewAllUrl     = $viewAllUrl     ?? get_post_type_archive_link('property');
  $hero           = $properties[0]  ?? null;
  $sideProps      = array_slice($properties, 1, 2);
@endphp

@if($hero)
@php
  $hp         = $hero;
  $heroPrice  = $hp['price'] ? '$' . number_format((float)$hp['price']) : __('Inquire', 'sage');
  $heroLoc    = trim(implode(', ', array_filter([$hp['city'], $hp['location']])));
  $heroWaRaw  = $hp['whatsapp'] ?: get_option('sv_whatsapp_global', '');
  $heroWaNum  = preg_replace('/[^0-9]/', '', $heroWaRaw);
  $heroWaMsg  = urlencode(__("Hello, I'm interested in: ", 'sage') . $hp['title'] . ' — ' . $hp['permalink']);
@endphp

<section class="sv-section sv-section--cream">
  <div class="sv-container">

    <div class="sv-section-header sv-section-header--split">
      <div>
        <div class="sv-section-eyebrow">{{ $sectionEyebrow }}</div>
        <h2 class="sv-section-title">{{ $sectionTitle }}</h2>
      </div>
      <a href="{{ $viewAllUrl }}" class="sv-btn sv-btn-outline sv-btn-sm">
        {{ __('Browse all', 'sage') }}
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </a>
    </div>

    <div class="sv-spotlight">

      {{-- ── HERO CARD ─────────────────────────────────── --}}
      <a href="{{ $hp['permalink'] }}"
         class="sv-spotlight__hero"
         aria-label="{{ esc_attr($hp['title']) }}"
         itemscope itemtype="https://schema.org/RealEstateListing">

        @if($hp['thumbnail'])
          <img src="{{ $hp['thumbnail'] }}"
               alt="{{ esc_attr($hp['title']) }}"
               loading="eager"
               class="sv-spotlight__hero-img"
               itemprop="image">
        @else
          <div class="sv-spotlight__hero-img sv-spotlight__hero-img--placeholder"></div>
        @endif

        <div class="sv-spotlight__hero-overlay" aria-hidden="true"></div>

        {{-- Top badges --}}
        <div class="sv-spotlight__hero-badges">
          @if($hp['featured'])
            <span class="sv-badge sv-badge--featured">⭐ {{ __('Featured', 'sage') }}</span>
          @endif
          @if($hp['hotDeal'])
            <span class="sv-badge sv-badge--hot">🔥 {{ __('Deal', 'sage') }}</span>
          @endif
          @if($hp['status'])
            <span class="sv-badge sv-badge--{{ $hp['statusSlug'] === 'for-rent' ? 'rent' : 'sale' }}">
              {{ $hp['status'] }}
            </span>
          @endif
        </div>

        {{-- Bottom content --}}
        <div class="sv-spotlight__hero-body">
          @if($hp['type'])
            <div class="sv-spotlight__hero-type">{{ $hp['type'] }}</div>
          @endif

          <div class="sv-spotlight__hero-price" itemprop="price">{{ $heroPrice }}</div>

          <h3 class="sv-spotlight__hero-title" itemprop="name">{{ $hp['title'] }}</h3>

          @if($heroLoc)
            <div class="sv-spotlight__hero-loc" itemprop="address">
              <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              {{ $heroLoc }}
            </div>
          @endif

          <div class="sv-spotlight__hero-stats">
            @if($hp['bedrooms'])
              <span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M2 4v16"/><path d="M22 4v16"/><path d="M2 8h20"/><path d="M2 16h20"/><path d="M12 8v8"/></svg>
                {{ $hp['bedrooms'] }} {{ __('bd.', 'sage') }}
              </span>
            @endif
            @if($hp['bathrooms'])
              <span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.683 3 4 3.683 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5"/><line x1="10" y1="5" x2="8" y2="7"/><line x1="2" y1="12" x2="22" y2="12"/></svg>
                {{ $hp['bathrooms'] }} {{ __('baths', 'sage') }}
              </span>
            @endif
            @if($hp['areaM2'])
              <span>
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                {{ number_format((float)$hp['areaM2']) }} m²
              </span>
            @endif
          </div>

          {{-- WhatsApp quick action --}}
          @if($heroWaNum)
            <a href="https://wa.me/{{ $heroWaNum }}?text={{ $heroWaMsg }}"
               class="sv-spotlight__hero-wa"
               target="_blank"
               rel="noopener"
               aria-label="{{ __('Inquire via WhatsApp', 'sage') }}"
               onclick="event.stopPropagation()">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                <path d="M11.994 1.998C6.478 1.998 2 6.476 2 11.992c0 1.762.46 3.416 1.264 4.851L2 22.002l5.337-1.397a9.961 9.961 0 0 0 4.657 1.149c5.516 0 9.994-4.478 9.994-9.994 0-5.516-4.478-9.762-9.994-9.762z"/>
              </svg>
              {{ __('WhatsApp', 'sage') }}
            </a>
          @endif
        </div>
      </a>{{-- /.sv-spotlight__hero --}}

      {{-- ── SIDE STACK ────────────────────────────────── --}}
      <div class="sv-spotlight__side">

        @foreach($sideProps as $sp)
          @php
            $sPrice = $sp['price'] ? '$' . number_format((float)$sp['price']) : __('Inquire', 'sage');
            $sLoc   = trim(implode(', ', array_filter([$sp['city'], $sp['location']])));
          @endphp

          <a href="{{ $sp['permalink'] }}"
             class="sv-spotlight__mini"
             aria-label="{{ esc_attr($sp['title']) }}"
             itemscope itemtype="https://schema.org/RealEstateListing">

            <div class="sv-spotlight__mini-img">
              @if($sp['thumbnail'])
                <img src="{{ $sp['thumbnail'] }}" alt="{{ esc_attr($sp['title']) }}" loading="lazy" itemprop="image">
              @else
                <div class="sv-spotlight__mini-img--placeholder"></div>
              @endif
              @if($sp['hotDeal'])
                <div class="sv-spotlight__mini-badge sv-spotlight__mini-badge--hot">🔥</div>
              @elseif($sp['featured'])
                <div class="sv-spotlight__mini-badge sv-spotlight__mini-badge--featured">⭐</div>
              @endif
            </div>

            <div class="sv-spotlight__mini-body">
              @if($sp['type'])
                <div class="sv-spotlight__mini-type">{{ $sp['type'] }}</div>
              @endif
              <div class="sv-spotlight__mini-price" itemprop="price">{{ $sPrice }}</div>
              <h3 class="sv-spotlight__mini-title" itemprop="name">{{ $sp['title'] }}</h3>
              @if($sLoc)
                <div class="sv-spotlight__mini-loc" itemprop="address">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                  {{ $sLoc }}
                </div>
              @endif
              <div class="sv-spotlight__mini-stats">
                @if($sp['bedrooms'])<span>🛏 {{ $sp['bedrooms'] }}</span>@endif
                @if($sp['bathrooms'])<span>🚿 {{ $sp['bathrooms'] }}</span>@endif
                @if($sp['areaM2'])<span>📐 {{ number_format((float)$sp['areaM2']) }} m²</span>@endif
              </div>
              <span class="sv-spotlight__mini-cta">
                {{ __('View details', 'sage') }}
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
              </span>
            </div>
          </a>
        @endforeach

        {{-- "See all" tile --}}
        <a href="{{ $viewAllUrl }}" class="sv-spotlight__more">
          <div class="sv-spotlight__more-icon" aria-hidden="true">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <div>
            <div class="sv-spotlight__more-label">{{ __('See all properties', 'sage') }}</div>
            <div class="sv-spotlight__more-sub">{{ __('Browse the full catalogue', 'sage') }}</div>
          </div>
          <svg class="sv-spotlight__more-arrow" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>

      </div>{{-- /.sv-spotlight__side --}}
    </div>{{-- /.sv-spotlight --}}

  </div>
</section>
@endif
