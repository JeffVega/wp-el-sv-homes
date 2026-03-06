@extends('layouts.app')

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     HERO SECTION
═══════════════════════════════════════════════════════════ --}}
<section class="sv-hero" aria-label="{{ __('Home', 'sage') }}">
  {{-- Hero background photo --}}
  @if(!empty($heroImage))
    <img
      src="{{ $heroImage }}"
      alt="{{ __('Properties in El Salvador', 'sage') }}"
      class="sv-hero__bg"
      fetchpriority="high"
    >
  @endif
  <div class="sv-hero__overlay"></div>
  <div class="sv-hero__pattern"></div>

  <div class="sv-container sv-hero__content">
    <div style="max-width:720px;">
      <div class="sv-hero__badge sv-fade-up">
        🇸🇻 {{ __('El Salvador — Land of Progress', 'sage') }}
      </div>

      <h1 class="sv-hero__title sv-fade-up sv-fade-up--delay-1">
        {!! nl2br(esc_html($heroTitle)) !!}
      </h1>

      <p class="sv-hero__subtitle sv-fade-up sv-fade-up--delay-2">
        {{ $heroSubtitle }}
      </p>
    </div>

    <div class="sv-fade-up sv-fade-up--delay-3 mt-6">
      @include('partials.property-search', [
        'types'     => $propertyTypes ? collect($propertyTypes)->map(fn($t) => (object)$t)->toArray() : get_terms(['taxonomy' => 'property_type', 'hide_empty' => false]),
        'statuses'  => get_terms(['taxonomy' => 'property_status', 'hide_empty' => false]),
        'locations' => get_terms(['taxonomy' => 'property_location', 'hide_empty' => false]),
        'compact'   => false,
      ])
    </div>

    <div class="sv-hero__stats sv-fade-up sv-fade-up--delay-3">
      <div class="sv-hero__stat-item">
        <span class="sv-hero__stat-num" data-count="{{ $propertyCounts['total'] }}">{{ $propertyCounts['total'] }}</span>
        <span class="sv-hero__stat-label">{{ __('Properties', 'sage') }}</span>
      </div>
      <div class="sv-hero__stat-item">
        <span class="sv-hero__stat-num" data-count="{{ $propertyCounts['families'] }}">{{ $propertyCounts['families'] }}+</span>
        <span class="sv-hero__stat-label">{{ __('Families helped', 'sage') }}</span>
      </div>
      <div class="sv-hero__stat-item">
        <span class="sv-hero__stat-num">14</span>
        <span class="sv-hero__stat-label">{{ __('Cities covered', 'sage') }}</span>
      </div>
      <div class="sv-hero__stat-item">
        <span class="sv-hero__stat-num">10+</span>
        <span class="sv-hero__stat-label">{{ __('Years of experience', 'sage') }}</span>
      </div>
    </div>
  </div>

  <div class="sv-hero__wave" aria-hidden="true">
    <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
      <path d="M0,55 C200,10 420,75 720,45 C1000,15 1240,65 1440,38 L1440,80 L0,80 Z" fill="#FDFAF4"/>
    </svg>
  </div>
</section>


{{-- ═══════════════════════════════════════════════════════════
     PROPERTY TYPES QUICK-FILTER
═══════════════════════════════════════════════════════════ --}}
<section class="sv-section-sm sv-section--cream">
  <div class="sv-container">
    <div class="sv-section-header sv-section-header--center" style="margin-bottom:2rem;">
      <div class="sv-section-eyebrow">{{ __('Explore by category', 'sage') }}</div>
      <h2 class="sv-section-title">{{ __('What type of property are you looking for?', 'sage') }}</h2>
      <p class="sv-section-subtitle" style="margin-top:0.5rem;">{{ __('Houses, apartments, land & lots, and more across El Salvador.', 'sage') }}</p>
    </div>

    <div class="sv-type-grid">
      @php
        $typeIcons = [
          'casa'         => '🏠',
          'house'        => '🏠',
          'apartamento'  => '🏢',
          'apartment'    => '🏢',
          'terreno'      => '🌿',
          'land'         => '🌿',
          'finca'        => '🌾',
          'farm'         => '🌾',
          'comercial'    => '🏪',
          'commercial'   => '🏪',
          'playa'        => '🏖️',
          'beach'        => '🏖️',
          'bodega'       => '🏭',
          'warehouse'    => '🏭',
        ];
      @endphp

      @if(!empty($propertyTypes))
        @foreach($propertyTypes as $type)
          @php
            $icon = $typeIcons[$type['slug']] ?? '🏘️';
          @endphp
          <a href="{{ $type['link'] }}" class="sv-type-card">
            <div class="sv-type-card__icon">{{ $icon }}</div>
            <div class="sv-type-card__name">{{ $type['name'] }}</div>
            <div class="sv-type-card__count">{{ $type['count'] }} {{ __('properties', 'sage') }}</div>
          </a>
        @endforeach
      @else
        {{-- Fallback type cards --}}
        @php
          $fallbackTypes = [
            ['icon' => '🏠', 'name' => 'Houses'],
            ['icon' => '🏢', 'name' => 'Apartments'],
            ['icon' => '🌿', 'name' => 'Land'],
            ['icon' => '🌾', 'name' => 'Farms'],
            ['icon' => '🏖️', 'name' => 'Beach'],
            ['icon' => '🏪', 'name' => 'Commercial'],
          ];
          $archiveLink = get_post_type_archive_link('property');
        @endphp
        @foreach($fallbackTypes as $ft)
          <a href="{{ $archiveLink }}" class="sv-type-card">
            <div class="sv-type-card__icon">{{ $ft['icon'] }}</div>
            <div class="sv-type-card__name">{{ $ft['name'] }}</div>
            <div class="sv-type-card__count">{{ __('View properties', 'sage') }}</div>
          </a>
        @endforeach
      @endif
    </div>
  </div>
</section>


{{-- ═══════════════════════════════════════════════════════════
     EXPLORE BY LOCATION (14 DEPARTMENTS)
═══════════════════════════════════════════════════════════ --}}
@if(!empty($locations))
<section class="sv-section-sm sv-section--white">
  <div class="sv-container">
    <div class="sv-section-header sv-section-header--center" style="margin-bottom:2rem;">
      <div class="sv-section-eyebrow">{{ __('Explore by location', 'sage') }}</div>
      <h2 class="sv-section-title">{{ __('Properties by department', 'sage') }}</h2>
      <p class="sv-section-subtitle" style="margin-top:0.5rem;">{{ __('Browse houses, land and apartments across the major cities of El Salvador.', 'sage') }}</p>
    </div>

    <div class="sv-type-grid">
      @foreach($locations as $loc)
        <a href="{{ $loc['link'] }}" class="sv-type-card">
          <div class="sv-type-card__icon">📍</div>
          <div class="sv-type-card__name">{{ $loc['name'] }}</div>
          <div class="sv-type-card__count">{{ $loc['count'] }} {{ __('properties', 'sage') }}</div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════════════════
     FEATURED PROPERTIES — Spotlight layout
═══════════════════════════════════════════════════════════ --}}
@if(!empty($featuredProperties))
  @include('partials.featured-listings-spotlight', [
    'properties'     => array_slice($featuredProperties, 0, 3),
    'sectionEyebrow' => __('Special selection', 'sage'),
    'sectionTitle'   => __('Properties in the Spotlight', 'sage'),
    'viewAllUrl'     => get_post_type_archive_link('property'),
  ])
@endif


{{-- ═══════════════════════════════════════════════════════════
     WHY CHOOSE US — Cultural / Trust Section
═══════════════════════════════════════════════════════════ --}}
<section class="sv-section sv-section--cream">
  <div class="sv-container">
    <div class="sv-section-header sv-section-header--center">
      <div class="sv-section-eyebrow">{{ __('Why choose us', 'sage') }}</div>
      <h2 class="sv-section-title">{{ __('Your trust, our mission', 'sage') }}</h2>
      <p class="sv-section-subtitle">
        {{ __('We are Salvadorans who know the market, the culture and every corner of the country. Your home is one step away.', 'sage') }}
      </p>
    </div>

    <div class="sv-features-grid">
      <div class="sv-feature-card">
        <div class="sv-feature-icon">🔍</div>
        <h3>{{ __('Personalized Search', 'sage') }}</h3>
        <p>{{ __('Filter by location, price and property type. We find exactly what you need across the major cities of El Salvador.', 'sage') }}</p>
      </div>
      <div class="sv-feature-card">
        <div class="sv-feature-icon">💬</div>
        <h3>{{ __('Immediate Contact', 'sage') }}</h3>
        <p>{{ __('Communicate directly via WhatsApp. No intermediaries. Fast response and personalized attention in your language.', 'sage') }}</p>
      </div>
      <div class="sv-feature-card">
        <div class="sv-feature-icon">🛡️</div>
        <h3>{{ __('Verified Properties', 'sage') }}</h3>
        <p>{{ __('Every listing is reviewed by our team. Real information, real photos and updated market prices.', 'sage') }}</p>
      </div>
      <div class="sv-feature-card">
        <div class="sv-feature-icon">🗺️</div>
        <h3>{{ __('We Know El Salvador', 'sage') }}</h3>
        <p>{{ __('From the Pacific beaches to the volcanoes, from San Salvador to the small towns. We are local experts with years of experience.', 'sage') }}</p>
      </div>
    </div>
  </div>
</section>


{{-- ═══════════════════════════════════════════════════════════
     LATEST PROPERTIES
═══════════════════════════════════════════════════════════ --}}
@if(!empty($latestProperties))
<section class="sv-section sv-section--white">
  <div class="sv-container">
    <div class="sv-section-header sv-section-header--split">
      <div>
        <div class="sv-section-eyebrow">{{ __('Just added', 'sage') }}</div>
        <h2 class="sv-section-title">{{ __('Recent Properties', 'sage') }}</h2>
      </div>
      <a href="{{ get_post_type_archive_link('property') }}" class="sv-btn sv-btn-outline sv-btn-sm">
        {{ __('Explore all', 'sage') }}
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </a>
    </div>

    <div class="sv-property-grid sv-property-grid--4col">
      @foreach($latestProperties as $property)
        @include('partials.property-card', ['property' => $property])
      @endforeach
    </div>
  </div>
</section>
@endif


{{-- ═══════════════════════════════════════════════════════════
     CTA — El Salvador Cultural Banner
═══════════════════════════════════════════════════════════ --}}
<section class="sv-cta-banner">
  <div class="sv-container">
    <div style="max-width:680px;">
      <div class="sv-section-eyebrow" style="color:var(--color-sv-gold-light);">
        {{ __('Ready for your new home?', 'sage') }}
      </div>
      <h2 class="sv-cta-banner__title">
        {{ __('Find your ideal property in El Salvador today', 'sage') }}
      </h2>
      <p style="color:rgba(255,255,255,0.78);font-size:1.05rem;margin-bottom:2rem;line-height:1.7;">
        {{ __('More than 500 families have already found their home with us. Will you be next?', 'sage') }}
      </p>
      <div style="display:flex;flex-wrap:wrap;gap:1rem;">
        <a href="{{ get_post_type_archive_link('property') }}" class="sv-btn sv-btn-gold sv-btn-lg">
          {{ __('View properties', 'sage') }}
        </a>
    @php
          $wa = get_option('sv_whatsapp_global', '');
          $waNumber = preg_replace('/[^0-9]/', '', $wa);
    @endphp

        @if($wa)
          <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa) }}" target="_blank" rel="noopener" class="sv-btn sv-btn-ghost sv-btn-lg">
            💬 {{ __('Talk to an advisor', 'sage') }}
          </a>
        @else
          <a href="{{ home_url('/contact') }}" class="sv-btn sv-btn-ghost sv-btn-lg">
            {{ __('Contact us', 'sage') }}
          </a>
        @endif
      </div>
    </div>
  </div>
</section>


{{-- ═══════════════════════════════════════════════════════════
     LATEST BLOG POSTS
═══════════════════════════════════════════════════════════ --}}
@php
  $recentPosts = get_posts(['numberposts' => 3, 'post_status' => 'publish']);
@endphp
@if(!empty($recentPosts))
<section class="sv-section sv-section--cream">
  <div class="sv-container">
    <div class="sv-section-header sv-section-header--center">
      <div class="sv-section-eyebrow">{{ __('Tips and news', 'sage') }}</div>
      <h2 class="sv-section-title">{{ __('Latest from the Blog', 'sage') }}</h2>
    </div>

    <div class="sv-blog-grid">
      @foreach($recentPosts as $blogPost)
        <article class="sv-blog-card">
          @if(has_post_thumbnail($blogPost))
            <div class="sv-blog-card__img">
              <a href="{{ get_permalink($blogPost) }}">
                <img src="{{ get_the_post_thumbnail_url($blogPost, 'medium') }}" alt="{{ esc_attr(get_the_title($blogPost)) }}" loading="lazy">
              </a>
            </div>
          @endif
          <div class="sv-blog-card__body">
            <div class="sv-blog-card__cat">{{ __('Blog', 'sage') }}</div>
            <h3 class="sv-blog-card__title">
              <a href="{{ get_permalink($blogPost) }}">{{ get_the_title($blogPost) }}</a>
            </h3>
            <div class="sv-blog-card__meta">{{ get_the_date('', $blogPost) }}</div>
          </div>
        </article>
      @endforeach
    </div>

    <div class="sv-section-cta">
      <a href="{{ get_permalink(get_option('page_for_posts')) ?: home_url('/blog') }}" class="sv-btn sv-btn-outline">
        {{ __('View all articles', 'sage') }}
      </a>
    </div>
  </div>
</section>
@endif

@endsection
