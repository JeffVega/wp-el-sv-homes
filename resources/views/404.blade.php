@extends('layouts.app')

@section('content')

<section class="sv-404" aria-label="{{ __('Page not found', 'sage') }}">

  {{-- Decorative background --}}
  <div class="sv-404__pattern" aria-hidden="true"></div>
  <div class="sv-404__bg-number" aria-hidden="true">404</div>

  <div class="sv-container sv-404__inner">

    <div class="sv-hero__badge sv-fade-up">
      🇸🇻 {{ __('Error 404', 'sage') }}
    </div>

    <h1 class="sv-404__title sv-fade-up sv-fade-up--delay-1">
      {{ __('Lost in the volcanoes?', 'sage') }}
    </h1>

    <p class="sv-404__subtitle sv-fade-up sv-fade-up--delay-2">
      {{ __("The page you're looking for may have moved, been renamed, or no longer exists. But there are plenty of beautiful properties waiting for you across El Salvador.", 'sage') }}
    </p>

    <div class="sv-404__actions sv-fade-up sv-fade-up--delay-2">
      <a href="{{ home_url('/') }}" class="sv-btn sv-btn-gold sv-btn-lg">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
          <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        {{ __('Back to Home', 'sage') }}
      </a>
      <a href="{{ get_post_type_archive_link('property') }}" class="sv-btn sv-btn-ghost sv-btn-lg">
        {{ __('Browse Properties', 'sage') }}
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
          <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
        </svg>
      </a>
    </div>

    <div class="sv-404__links sv-fade-up sv-fade-up--delay-3">
      <span class="sv-404__links-label">{{ __('Quick links', 'sage') }}</span>
      <a href="{{ get_post_type_archive_link('property') }}">{{ __('All Properties', 'sage') }}</a>
      <span aria-hidden="true">·</span>
      <a href="{{ home_url('/about') }}">{{ __('About Us', 'sage') }}</a>
      <span aria-hidden="true">·</span>
      <a href="{{ home_url('/contact') }}">{{ __('Contact', 'sage') }}</a>
      @php($wa = get_option('sv_whatsapp_global', ''))
      @if($wa)
        <span aria-hidden="true">·</span>
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa) }}" target="_blank" rel="noopener">
          💬 {{ __('WhatsApp', 'sage') }}
        </a>
      @endif
    </div>

  </div>

  <div class="sv-hero__wave" aria-hidden="true">
    <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
      <path d="M0,55 C200,10 420,75 720,45 C1000,15 1240,65 1440,38 L1440,80 L0,80 Z" fill="#FDFAF4"/>
    </svg>
  </div>

</section>

@endsection
