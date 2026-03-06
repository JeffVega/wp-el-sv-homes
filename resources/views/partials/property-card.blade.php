{{--
  Property Card Partial
  Required: $property (array from FrontPage::hydrateProperties)
  Optional: $variant ('default' | 'compact')
--}}
@php
  $variant      = $variant ?? 'default';
  $p            = $property;
  $price        = $p['price'] ? '$' . number_format((float) $p['price']) : __('Inquire', 'sage');
  $locationText = trim(implode(', ', array_filter([$p['city'], $p['location']])));
  $waRaw        = $p['whatsapp'] ?: get_option('sv_whatsapp_global', '');
  $whatsappNum  = preg_replace('/[^0-9]/', '', $waRaw);
  $waMsg        = urlencode(__('Hello, I\'m interested in the property: ', 'sage') . get_the_title($p['id']) . ' - ' . get_permalink($p['id']));
@endphp

<article class="sv-card" itemscope itemtype="https://schema.org/RealEstateListing">
  {{-- Image --}}
  <div class="sv-card__image-wrap">
    @if($p['thumbnail'])
      <img
        src="{{ esc_url($p['thumbnail']) }}"
        alt="{{ esc_attr($p['title']) }}"
        loading="lazy"
        itemprop="image"
      >
    @else
      <div class="sv-card__image-placeholder">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
          <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
      </div>
    @endif

    {{-- Badges --}}
    <div class="sv-card__badges">
      @if($p['featured'])
        <span class="sv-badge sv-badge--featured">⭐ {{ __('Featured', 'sage') }}</span>
      @endif
      @if($p['hotDeal'])
        <span class="sv-badge sv-badge--hot">🔥 {{ __('Deal', 'sage') }}</span>
      @endif
      @if($p['statusSlug'] === 'for-sale' || $p['status'])
        <span class="sv-badge sv-badge--{{ $p['statusSlug'] === 'for-rent' ? 'rent' : 'sale' }}">
          {{ $p['status'] ?: __('For Sale', 'sage') }}
        </span>
      @endif
    </div>

    {{-- Quick actions --}}
    <div class="sv-card__image-actions">
      @if($whatsappNum)
        <a
          href="https://wa.me/{{ $whatsappNum }}?text={{ $waMsg }}"
          class="sv-card__action-btn"
          target="_blank"
          rel="noopener"
          title="{{ __('Inquire via WhatsApp', 'sage') }}"
          aria-label="{{ __('WhatsApp', 'sage') }}"
        >
          <svg width="16" height="16" viewBox="0 0 24 24" fill="#25D366">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            <path d="M11.994 1.998C6.478 1.998 2 6.476 2 11.992c0 1.762.46 3.416 1.264 4.851L2 22.002l5.337-1.397a9.961 9.961 0 0 0 4.657 1.149c5.516 0 9.994-4.478 9.994-9.994 0-5.516-4.478-9.762-9.994-9.762z"/>
          </svg>
        </a>
      @endif
      <a href="{{ esc_url($p['permalink']) }}" class="sv-card__action-btn" title="{{ __('View details', 'sage') }}" aria-label="{{ __('View property', 'sage') }}">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
          <path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/>
        </svg>
      </a>
    </div>
  </div>

  {{-- Body --}}
  <div class="sv-card__body">
    <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:0.375rem;">
      <div class="sv-card__price" itemprop="price">
        {{ $price }}
        @if($p['priceLabel'])
          <span class="sv-card__price-label">{{ $p['priceLabel'] }}</span>
        @endif
      </div>
    </div>

    @if($p['type'])
      <div class="sv-card__type">{{ $p['type'] }}</div>
    @endif

    <h3 class="sv-card__title" itemprop="name">
      <a href="{{ esc_url($p['permalink']) }}">{{ esc_html($p['title']) }}</a>
    </h3>

    @if($locationText)
      <div class="sv-card__location" itemprop="address">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
          <circle cx="12" cy="10" r="3"/>
        </svg>
        {{ $locationText }}
      </div>
    @endif

    {{-- Stats --}}
    <div class="sv-card__stats">
      @if($p['bedrooms'])
        <div class="sv-card__stat" title="{{ __('Bedrooms', 'sage') }}">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 4v16"/><path d="M22 4v16"/><path d="M2 8h20"/><path d="M2 16h20"/><path d="M12 8v8"/></svg>
          {{ $p['bedrooms'] }} {{ __('bd.', 'sage') }}
        </div>
      @endif
      @if($p['bathrooms'])
        <div class="sv-card__stat" title="{{ __('Bathrooms', 'sage') }}">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.683 3 4 3.683 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5"/><line x1="10" y1="5" x2="8" y2="7"/><line x1="2" y1="12" x2="22" y2="12"/></svg>
          {{ $p['bathrooms'] }} {{ __('baths', 'sage') }}
        </div>
      @endif
      @if($p['areaM2'])
        <div class="sv-card__stat" title="{{ __('Build area', 'sage') }}">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
          {{ number_format((float)$p['areaM2']) }} m²
        </div>
      @endif
      @if(!$p['areaM2'] && $p['landAreaM2'])
        <div class="sv-card__stat" title="{{ __('Land area', 'sage') }}">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
          {{ number_format((float)$p['landAreaM2']) }} m²
        </div>
      @endif
    </div>

    {{-- Footer CTA --}}
    <div class="sv-card__footer">
      <a href="{{ esc_url($p['permalink']) }}" class="sv-btn sv-btn-primary sv-btn-sm">
        {{ __('View details', 'sage') }}
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </a>
      @if($whatsappNum)
        <a
          href="https://wa.me/{{ $whatsappNum }}?text={{ $waMsg }}"
          class="sv-btn sv-btn-sm"
          target="_blank"
          rel="noopener"
          style="background:#25D366;color:#fff;border-color:#25D366;flex-shrink:0;"
          aria-label="WhatsApp"
        >
          <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.994 1.998C6.478 1.998 2 6.476 2 11.992c0 1.762.46 3.416 1.264 4.851L2 22.002l5.337-1.397a9.961 9.961 0 0 0 4.657 1.149c5.516 0 9.994-4.478 9.994-9.994 0-5.516-4.478-9.762-9.994-9.762z"/></svg>
        </a>
      @endif
    </div>
  </div>
</article>
