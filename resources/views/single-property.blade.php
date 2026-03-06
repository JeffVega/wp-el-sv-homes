@extends('layouts.app')

@section('content')

@php
  $p          = $property;
  $price      = $p['price'] ? '$' . number_format((float)$p['price']) : __('Ask for price', 'sage');
  $waNum      = $p['whatsapp'] ? preg_replace('/[^0-9]/', '', $p['whatsapp']) : preg_replace('/[^0-9]/', '', get_option('sv_whatsapp_global', ''));
  $waMsg      = urlencode(__('Hello, I\'m interested in the property: ', 'sage') . $p['title'] . ' - ' . $p['permalink']);
  $locationTx = trim(implode(', ', array_filter([$p['address'], $p['city'], $p['location']])));
  $mapsKey    = get_option('sv_google_maps_key', '');
  $hasMap     = $p['mapLat'] && $p['mapLng'];
@endphp

{{-- ── Breadcrumb ──────────────────────────────────────── --}}
<div class="bg-sv-sky py-3.5 border-b border-sv-stone-light">
  <div class="sv-container text-[0.82rem] text-sv-stone flex items-center gap-2 flex-wrap">
    <a href="{{ home_url('/') }}" class="text-sv-blue no-underline hover:text-sv-blue-mid">{{ __('Home', 'sage') }}</a>
    <span>›</span>
    <a href="{{ get_post_type_archive_link('property') }}" class="text-sv-blue no-underline hover:text-sv-blue-mid">{{ __('Properties', 'sage') }}</a>
    @if($p['type'])
      <span>›</span>
      <span>{{ $p['type'] }}</span>
    @endif
    <span>›</span>
    <span class="text-sv-navy font-semibold">{{ mb_strlen($p['title']) > 45 ? mb_substr($p['title'], 0, 45) . '…' : $p['title'] }}</span>
  </div>
</div>

<div class="sv-container pt-8 pb-0">
  <h1 class="font-display text-2xl sm:text-3xl font-bold text-sv-navy leading-tight">
    {{ $p['title'] }}
  </h1>
</div>

<div class="sv-container py-10">
  <div class="sv-single-layout">

    {{-- ── LEFT: Gallery + Details ────────────────────── --}}
    <div>

      {{-- Gallery --}}
      <div class="sv-gallery" id="sv-gallery">
        <div class="sv-gallery__main">
          @if(!empty($galleryImages))
            <img
              id="sv-gallery-main-img"
              src="{{ $galleryImages[0]['url'] }}"
              alt="{{ $galleryImages[0]['alt'] }}"
              style="width:100%;height:100%;object-fit:cover;"
            >
          @else
            <div class="sv-card__image-placeholder" style="aspect-ratio:16/9;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,var(--color-sv-blue),var(--color-sv-navy));font-size:4rem;color:rgba(255,255,255,0.2);">🏠</div>
          @endif

          {{-- Gallery nav arrows --}}
          @if(count($galleryImages) > 1)
            <button id="sv-gallery-prev" aria-label="{{ __('Previous', 'sage') }}"
              style="position:absolute;left:1rem;top:50%;transform:translateY(-50%);width:2.5rem;height:2.5rem;background:rgba(0,0,0,0.5);border:none;border-radius:50%;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.2rem;transition:background 0.2s;">‹</button>
            <button id="sv-gallery-next" aria-label="{{ __('Next', 'sage') }}"
              style="position:absolute;right:1rem;top:50%;transform:translateY(-50%);width:2.5rem;height:2.5rem;background:rgba(0,0,0,0.5);border:none;border-radius:50%;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:1.2rem;transition:background 0.2s;">›</button>
            <div style="position:absolute;bottom:1rem;right:1rem;background:rgba(0,0,0,0.55);color:#fff;font-size:0.78rem;padding:0.3rem 0.75rem;border-radius:999px;">
              <span id="sv-gallery-counter">1</span> / {{ count($galleryImages) }}
            </div>
          @endif
        </div>

        @if(count($galleryImages) > 1)
          <div class="sv-gallery__thumbs" id="sv-gallery-thumbs">
            @foreach($galleryImages as $i => $img)
              <div class="sv-gallery__thumb {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                <img src="{{ $img['url'] }}" alt="{{ $img['alt'] }}" loading="lazy">
              </div>
            @endforeach
          </div>
        @endif
      </div>

      {{-- Key Stats Bar --}}
      <div class="sv-property-meta-bar">
        @if($p['bedrooms'])
          <div class="sv-property-meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M2 4v16"/><path d="M22 4v16"/><path d="M2 8h20"/><path d="M2 16h20"/><path d="M12 8v8"/></svg>
            <span><strong>{{ $p['bedrooms'] }}</strong> {{ __('Bedrooms', 'sage') }}</span>
          </div>
        @endif
        @if($p['bathrooms'])
          <div class="sv-property-meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-.5C4.683 3 4 3.683 4 4.5V17a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5"/><line x1="2" y1="12" x2="22" y2="12"/></svg>
            <span><strong>{{ $p['bathrooms'] }}</strong> {{ __('Bathrooms', 'sage') }}</span>
          </div>
        @endif
        @if($p['areaM2'])
          <div class="sv-property-meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
            <span><strong>{{ number_format((float)$p['areaM2']) }} m²</strong> {{ __('built', 'sage') }}</span>
          </div>
        @endif
        @if($p['landAreaM2'])
          <div class="sv-property-meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="3" width="18" height="18" rx="2"/></svg>
            <span><strong>{{ number_format((float)$p['landAreaM2']) }} m²</strong> {{ __('land', 'sage') }}</span>
          </div>
        @endif
        @if($p['parking'])
          <div class="sv-property-meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
            <span><strong>{{ $p['parking'] }}</strong> {{ __('Parking', 'sage') }}</span>
          </div>
        @endif
        @if($p['yearBuilt'])
          <div class="sv-property-meta-item">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <span>{{ __('Built in', 'sage') }} <strong>{{ $p['yearBuilt'] }}</strong></span>
          </div>
        @endif
      </div>

      {{-- Description --}}
      <div style="background:#fff;border-radius:var(--radius-card);padding:2rem;box-shadow:var(--shadow-card);margin-bottom:1.5rem;">
        <h2 style="font-family:var(--font-display);font-size:1.25rem;color:var(--color-sv-navy);margin-bottom:1rem;">
          {{ __('Property description', 'sage') }}
        </h2>
        <div class="sv-prose">
          {!! wp_kses_post(get_the_content(null, false, $p['id'])) !!}
        </div>
      </div>

      {{-- Amenities --}}
      @if(!empty($p['amenities']))
        <div style="background:#fff;border-radius:var(--radius-card);padding:2rem;box-shadow:var(--shadow-card);margin-bottom:1.5rem;">
          <h2 style="font-family:var(--font-display);font-size:1.25rem;color:var(--color-sv-navy);margin-bottom:1.25rem;">
            ✨ {{ __('Amenities and features', 'sage') }}
          </h2>
          <div class="sv-amenity-grid">
            @foreach($p['amenities'] as $key)
              @if(isset($amenitiesList[$key]))
                <div class="sv-amenity-item">{{ $amenitiesList[$key] }}</div>
              @endif
            @endforeach
          </div>
        </div>
      @endif

      {{-- Video Tour --}}
      @if($p['videoUrl'])
        @php
          $ytMatch = [];
          $vmMatch = [];
          preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $p['videoUrl'], $ytMatch);
          preg_match('/vimeo\.com\/(\d+)/', $p['videoUrl'], $vmMatch);
        @endphp
        <div style="background:#fff;border-radius:var(--radius-card);padding:2rem;box-shadow:var(--shadow-card);margin-bottom:1.5rem;">
          <h2 style="font-family:var(--font-display);font-size:1.25rem;color:var(--color-sv-navy);margin-bottom:1.25rem;">
            🎥 {{ __('Video tour', 'sage') }}
          </h2>
          <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:0.5rem;">
            @if(!empty($ytMatch[1]))
              <iframe src="https://www.youtube.com/embed/{{ $ytMatch[1] }}" style="position:absolute;inset:0;width:100%;height:100%;" frameborder="0" allowfullscreen title="{{ $p['title'] }} video tour" loading="lazy"></iframe>
            @elseif(!empty($vmMatch[1]))
              <iframe src="https://player.vimeo.com/video/{{ $vmMatch[1] }}" style="position:absolute;inset:0;width:100%;height:100%;" frameborder="0" allowfullscreen loading="lazy"></iframe>
            @endif
          </div>
        </div>
      @endif

      {{-- Map --}}
      <div style="background:#fff;border-radius:var(--radius-card);padding:2rem;box-shadow:var(--shadow-card);margin-bottom:1.5rem;">
        <h2 style="font-family:var(--font-display);font-size:1.25rem;color:var(--color-sv-navy);margin-bottom:1.25rem;">
          📍 {{ __('Location', 'sage') }}
        </h2>
        @if($locationTx)
          <p style="font-size:0.9rem;color:var(--color-sv-stone);margin-bottom:1rem;">{{ $locationTx }}</p>
        @endif

        @if($hasMap && $mapsKey)
          <div class="sv-map-container" id="sv-map" data-lat="{{ $p['mapLat'] }}" data-lng="{{ $p['mapLng'] }}" data-title="{{ esc_attr($p['title']) }}" style="height:320px;"></div>
        @elseif($hasMap)
          {{-- Placeholder - coordinates available but no API key yet --}}
          <div class="sv-map-placeholder" style="height:280px;">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <div style="text-align:center;">
              <p style="font-weight:600;margin-bottom:0.375rem;">{{ __('Coordinates available', 'sage') }}</p>
              <p style="font-size:0.8rem;color:var(--color-sv-stone);">{{ $p['mapLat'] }}, {{ $p['mapLng'] }}</p>
              <a href="https://www.google.com/maps?q={{ $p['mapLat'] }},{{ $p['mapLng'] }}" target="_blank" rel="noopener" class="sv-btn sv-btn-primary sv-btn-sm" style="margin-top:0.75rem;">
                {{ __('View on Google Maps', 'sage') }} ↗
              </a>
            </div>
          </div>
        @else
          <div class="sv-map-placeholder" style="height:180px;">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <p style="font-size:0.85rem;color:var(--color-sv-stone);">{{ __('Map coming soon', 'sage') }}</p>
          </div>
        @endif
      </div>

    </div>{{-- END left col --}}

    {{-- ── RIGHT: Inquiry Sidebar ──────────────────────── --}}
    <div>
      <div class="sv-inquiry-card">
        <div class="sv-inquiry-card__header">
          @if($p['featured'])
            <div style="font-size:0.72rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--color-sv-gold);margin-bottom:0.5rem;">
              ⭐ {{ __('Featured Property', 'sage') }}
            </div>
          @endif
          <div class="sv-inquiry-card__price">{{ $price }}</div>
          @if($p['priceLabel'])
            <div style="color:rgba(255,255,255,0.7);font-size:0.85rem;margin-top:0.25rem;">{{ $p['priceLabel'] }}</div>
          @endif
          @if($p['status'])
            <div style="margin-top:0.75rem;">
              <span style="background:rgba(255,255,255,0.15);color:#fff;font-size:0.72rem;font-weight:700;padding:0.25rem 0.75rem;border-radius:999px;letter-spacing:0.06em;text-transform:uppercase;">
                {{ $p['status'] }}
              </span>
            </div>
          @endif
          @if($p['type'])
            <div style="color:rgba(255,255,255,0.6);font-size:0.8rem;margin-top:0.5rem;">{{ $p['type'] }}</div>
          @endif
        </div>

        <div class="sv-inquiry-card__body">
          @if($p['agentName'])
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;padding-bottom:1.25rem;border-bottom:1px solid var(--color-sv-stone-light);">
              <div style="width:2.5rem;height:2.5rem;background:var(--color-sv-blue);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1rem;flex-shrink:0;">
                {{ strtoupper(substr($p['agentName'], 0, 1)) }}
              </div>
              <div>
                <div style="font-weight:700;font-size:0.9rem;color:var(--color-sv-navy);">{{ $p['agentName'] }}</div>
                <div style="font-size:0.75rem;color:var(--color-sv-stone);">{{ __('Real estate agent', 'sage') }}</div>
              </div>
            </div>
          @endif

          @include('partials.inquiry-form', [
            'propertyId'    => $p['id'],
            'propertyTitle' => $p['title'],
            'whatsapp'      => $p['whatsapp'] ?: get_option('sv_whatsapp_global', ''),
          ])
        </div>
      </div>

      {{-- Share --}}
      <div style="background:#fff;border-radius:var(--radius-card);padding:1.25rem;box-shadow:var(--shadow-card);margin-top:1rem;text-align:center;">
        <p style="font-size:0.78rem;font-weight:700;color:var(--color-sv-stone);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:0.875rem;">
          {{ __('Share this property', 'sage') }}
        </p>
        <div style="display:flex;justify-content:center;gap:0.625rem;">
          <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($p['permalink']) }}"
             target="_blank" rel="noopener"
             style="background:#1877F2;color:#fff;width:2.25rem;height:2.25rem;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:0.8rem;font-weight:700;">f</a>
          @if($waNum)
            <a href="https://wa.me/?text={{ $waMsg }}" target="_blank" rel="noopener"
               style="background:#25D366;color:#fff;width:2.25rem;height:2.25rem;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.994 1.998C6.478 1.998 2 6.476 2 11.992c0 1.762.46 3.416 1.264 4.851L2 22.002l5.337-1.397a9.961 9.961 0 0 0 4.657 1.149c5.516 0 9.994-4.478 9.994-9.994 0-5.516-4.478-9.762-9.994-9.762z"/></svg>
            </a>
          @endif
          <button onclick="navigator.clipboard.writeText('{{ $p['permalink'] }}').then(()=>alert('{{ __('Link copied!', 'sage') }}')).catch(()=>{})"
                  style="background:var(--color-sv-stone-light);color:var(--color-sv-navy);width:2.25rem;height:2.25rem;border-radius:50%;display:flex;align-items:center;justify-content:center;border:none;cursor:pointer;"
                  title="{{ __('Copy link', 'sage') }}">
            🔗
          </button>
        </div>
      </div>
    </div>

  </div>{{-- END .sv-single-layout --}}

  {{-- ── Similar Properties ──────────────────────────────── --}}
  @if(!empty($similarProps))
    <div style="margin-top:3rem;">
      <div class="sv-section-header">
        <div class="sv-section-eyebrow">{{ __('You might also like', 'sage') }}</div>
        <h2 class="sv-section-title">{{ __('Similar properties', 'sage') }}</h2>
      </div>
      <div class="sv-property-grid">
        @foreach($similarProps as $property)
          @include('partials.property-card', ['property' => $property])
        @endforeach
      </div>
    </div>
  @endif

</div>{{-- END .sv-container --}}

@if($hasMap && $mapsKey)
  <script>
    window.addEventListener('load', function() {
      var lat = parseFloat('{{ $p['mapLat'] }}');
      var lng = parseFloat('{{ $p['mapLng'] }}');
      var map = new google.maps.Map(document.getElementById('sv-map'), {
        center: { lat: lat, lng: lng },
        zoom: 15,
        styles: [
          { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#A8D5F5' }] },
          { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
        ]
      });
      new google.maps.Marker({
        position: { lat: lat, lng: lng },
        map: map,
        title: '{{ esc_js($p['title']) }}',
        icon: {
          path: google.maps.SymbolPath.CIRCLE,
          fillColor: '#1B3A8A',
          fillOpacity: 1,
          strokeColor: '#F0A500',
          strokeWeight: 3,
          scale: 12,
        }
      });
    });
  </script>
  <script defer src="https://maps.googleapis.com/maps/api/js?key={{ esc_attr($mapsKey) }}&callback=Function.prototype"></script>
@endif

@endsection
