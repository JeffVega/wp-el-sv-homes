{{--
  Template Name: About Page
--}}
@extends('layouts.app')

@section('content')

{{-- Hero (light background for readability) --}}
<div class="sv-page-hero sv-page-hero--light pt-24 pb-16">
  <div class="sv-page-hero__pattern" aria-hidden="true"></div>
  <div class="sv-container relative z-10 text-center">
    <div class="sv-section-eyebrow text-sv-gold flex justify-center">
      {{ __('Who we are', 'sage') }}
    </div>
    <h1 class="font-display text-2xl sm:text-3xl md:text-4xl font-extrabold text-sv-navy max-w-2xl mx-auto mt-3 mb-4">
      {{ __('El Salvador Homes — Your home, our passion', 'sage') }}
    </h1>
    <p class="text-sv-stone text-[1.05rem] max-w-[560px] mx-auto">
      {{ __('We are the leading real estate portal in El Salvador, connecting Salvadoran families with their dream home for over a decade.', 'sage') }}
    </p>
  </div>
</div>

{{-- Story / Mission --}}
<section class="sv-section bg-sv-cream">
  <div class="sv-container">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
      <div>
        <div class="sv-section-eyebrow">{{ __('Our story', 'sage') }}</div>
        <h2 class="sv-section-title">{{ __('Born in El Salvador, for El Salvador', 'sage') }}</h2>
        <div class="text-sv-stone leading-relaxed text-[0.95rem]">
          @if(get_the_content())
            {!! wp_kses_post(get_the_content()) !!}
          @else
            <p>{{ __('SV Homes was born from the vision of connecting Salvadorans — both in the country and abroad — with the best real estate opportunities in El Salvador.', 'sage') }}</p>
            <p class="mt-4">{{ __('We know every department, every municipality, every neighborhood. From the Pacific coast to the volcanoes, from San Salvador to the most picturesque towns in the country, we are your trusted guide.', 'sage') }}</p>
          @endif
        </div>
      </div>
      <div class="relative">
        {{-- Placeholder card --}}
        <div class="bg-gradient-to-br from-sv-blue to-sv-navy rounded-xl aspect-[4/3] flex items-center justify-center">
          <div class="text-center text-white/30">
            <div class="text-7xl">🇸🇻</div>
            <div class="text-sm mt-2 font-semibold">El Salvador Homes</div>
          </div>
        </div>
        {{-- Accent decorations --}}
        <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-sv-gold rounded-xl -z-10 opacity-25" aria-hidden="true"></div>
        <div class="absolute -top-4 -left-4 w-16 h-16 border-[3px] border-sv-blue rounded-full -z-10 opacity-30" aria-hidden="true"></div>
      </div>
    </div>
  </div>
</section>

{{-- Values --}}
<section class="sv-section bg-white">
  <div class="sv-container">
    <div class="sv-section-header sv-section-header--center">
      <div class="sv-section-eyebrow">{{ __('What drives us', 'sage') }}</div>
      <h2 class="sv-section-title">{{ __('Our Values', 'sage') }}</h2>
    </div>

    <div class="sv-features-grid">
      @foreach([
        ['🤝', __('Trust', 'sage'), __('We work with transparency and honesty. Your trust is the pillar of everything we do.', 'sage')],
        ['🏡', __('Commitment', 'sage'), __('We commit to finding the perfect property for you, regardless of your budget or needs.', 'sage')],
        ['🗺️', __('Local Knowledge', 'sage'), __('14 departments, hundreds of municipalities. We know El Salvador better than anyone.', 'sage')],
        ['⚡', __('Speed', 'sage'), __('Fast response, agile processes. We know your time is valuable.', 'sage')],
      ] as [$icon, $title, $desc])
        <div class="sv-feature-card">
          <div class="sv-feature-icon">{{ $icon }}</div>
          <h3>{{ $title }}</h3>
          <p>{{ $desc }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Stats --}}
<section class="sv-page-hero py-16">
  <div class="sv-container">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
      @foreach([
        [get_option('sv_families_helped', 500) . '+', __('Families helped', 'sage')],
        ['14', __('Departments covered', 'sage')],
        ['10+', __('Years of experience', 'sage')],
        [wp_count_posts('property')->publish ?? 0, __('Active properties', 'sage')],
      ] as [$num, $label])
        <div>
          <div class="font-display text-4xl font-extrabold text-sv-gold leading-none">{{ $num }}</div>
          <div class="text-white/70 text-sm mt-2 uppercase tracking-wider">{{ $label }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- CTA --}}
<section class="sv-section bg-sv-cream text-center">
  <div class="sv-container max-w-2xl mx-auto">
    <div class="text-5xl mb-4">🏠</div>
    <h2 class="sv-section-title">{{ __('Ready to find your home?', 'sage') }}</h2>
    <p class="text-sv-stone text-base mb-8 leading-relaxed">
      {{ __('Explore hundreds of properties throughout El Salvador or contact us and one of our advisors will guide you.', 'sage') }}
    </p>
    <div class="flex justify-center flex-wrap gap-4">
      <a href="{{ get_post_type_archive_link('property') }}" class="sv-btn sv-btn-primary sv-btn-lg">
        {{ __('View properties', 'sage') }}
      </a>
      <a href="{{ home_url('/contact') }}" class="sv-btn sv-btn-outline sv-btn-lg">
        {{ __('Contact an advisor', 'sage') }}
      </a>
    </div>
  </div>
</section>

@endsection
