<header class="sv-header" id="sv-header">
  <div class="sv-header__inner">

    {{-- Logo --}}
    <a href="{{ home_url('/') }}" class="sv-header__logo">
      <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <rect width="32" height="32" rx="8" fill="#1B3A8A"/>
        <path d="M8 22L16 8L24 22H8Z" fill="#F0A500" opacity="0.9"/>
        <rect x="12" y="18" width="8" height="6" rx="1" fill="white"/>
      </svg>
      <span>SV <span>Homes</span></span>
    </a>

    {{-- Mobile toggle --}}
    <button class="sv-mobile-toggle" id="sv-mobile-toggle" aria-label="{{ __('Toggle navigation', 'sage') }}" aria-expanded="false">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
        <line x1="3" y1="6" x2="21" y2="6" class="sv-nav-line sv-nav-line-1"/>
        <line x1="3" y1="12" x2="21" y2="12" class="sv-nav-line sv-nav-line-2"/>
        <line x1="3" y1="18" x2="21" y2="18" class="sv-nav-line sv-nav-line-3"/>
      </svg>
    </button>

    {{-- Navigation --}}
    <div class="sv-nav-wrapper" id="sv-nav-wrapper">
      <ul class="sv-nav">
        <li><a href="{{ home_url('/') }}">{{ __('Home', 'sage') }}</a></li>
        <li><a href="{{ get_post_type_archive_link('property') }}">{{ __('Properties', 'sage') }}</a></li>
        <li><a href="{{ home_url('/about') }}">{{ __('About Us', 'sage') }}</a></li>
        <li><a href="{{ home_url('/blog') }}">{{ __('Blog', 'sage') }}</a></li>
        <li><a href="{{ home_url('/contact') }}">{{ __('Contact', 'sage') }}</a></li>
        @php($whatsapp = get_option('sv_whatsapp_global', ''))
        @if($whatsapp)
          <li class="sv-nav__cta">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}" target="_blank" rel="noopener">
              💬 {{ __('WhatsApp', 'sage') }}
            </a>
          </li>
        @endif
      </ul>
    </div>

  </div>
</header>
