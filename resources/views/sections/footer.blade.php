<footer class="sv-footer">
  <div class="sv-container">
    <div class="sv-footer__grid">

      {{-- Brand Column --}}
      <div>
        <div class="sv-footer__brand-name">SV <span>Homes</span></div>
        <p class="sv-footer__tagline">
          {{ __('Your trusted real estate partner in El Salvador. Connecting families with their dream home.', 'sage') }}
        </p>
        <div class="sv-footer__flag" aria-label="{{ __('El Salvador flag', 'sage') }}">
          <span></span><span></span><span></span>
        </div>
        <div class="sv-social-links">
          <a href="#" class="sv-social-link" aria-label="Facebook">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
          </a>
          <a href="#" class="sv-social-link" aria-label="Instagram">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
          <a href="#" class="sv-social-link" aria-label="WhatsApp">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.994 1.998C6.478 1.998 2 6.476 2 11.992c0 1.762.46 3.416 1.264 4.851L2 22.002l5.337-1.397a9.961 9.961 0 0 0 4.657 1.149c5.516 0 9.994-4.478 9.994-9.994 0-5.516-4.478-9.762-9.994-9.762z"/></svg>
          </a>
          <a href="#" class="sv-social-link" aria-label="YouTube">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.97C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="white"/></svg>
          </a>
        </div>
      </div>

      {{-- Properties --}}
      <div>
        <div class="sv-footer__col-title">{{ __('Properties', 'sage') }}</div>
        <ul class="sv-footer__links">
          <li><a href="{{ home_url('/property-search') }}">{{ __('All properties', 'sage') }}</a></li>
          <li><a href="{{ home_url('/property-search') }}?property_status=for-sale">{{ __('For sale', 'sage') }}</a></li>
          <li><a href="{{ home_url('/property-search') }}?property_status=for-rent">{{ __('For rent', 'sage') }}</a></li>
          <li><a href="{{ home_url('/property-search') }}?property_type=casa">{{ __('Houses', 'sage') }}</a></li>
          <li><a href="{{ home_url('/property-search') }}?property_type=apartamento">{{ __('Apartments', 'sage') }}</a></li>
          <li><a href="{{ home_url('/property-search') }}?property_type=terreno">{{ __('Land', 'sage') }}</a></li>
        </ul>
      </div>

      {{-- Cities — linked to Location CPT pages for SEO --}}
      <div>
        <div class="sv-footer__col-title">{{ __('Cities', 'sage') }}</div>
        @php
          $footerLocations = get_posts([
            'post_type'      => 'location',
            'posts_per_page' => 14,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
          ]);
        @endphp
        @if(!empty($footerLocations))
          <ul class="sv-footer__links">
            @foreach($footerLocations as $loc)
              <li><a href="{{ get_permalink($loc) }}">{{ get_the_title($loc) }}</a></li>
            @endforeach
          </ul>
        @else
          <p class="sv-footer__empty">{{ __('Coming soon', 'sage') }}</p>
        @endif
      </div>

      {{-- Company --}}
      <div>
        <div class="sv-footer__col-title">{{ __('Company', 'sage') }}</div>
        <ul class="sv-footer__links">
          <li><a href="{{ home_url('/about') }}">{{ __('About Us', 'sage') }}</a></li>
          <li><a href="{{ home_url('/blog') }}">{{ __('Blog', 'sage') }}</a></li>
          <li><a href="{{ home_url('/contact') }}">{{ __('Contact', 'sage') }}</a></li>
          <li><a href="{{ home_url('/privacy-policy') }}">{{ __('Privacy Policy', 'sage') }}</a></li>
          <li><a href="{{ home_url('/terms') }}">{{ __('Terms of Use', 'sage') }}</a></li>
        </ul>

        @php($whatsapp = get_option('sv_whatsapp_global', ''))
        @if($whatsapp)
          <div class="sv-footer__wa-wrap">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}"
               target="_blank" rel="noopener"
               class="sv-footer__wa-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                <path d="M11.994 1.998C6.478 1.998 2 6.476 2 11.992c0 1.762.46 3.416 1.264 4.851L2 22.002l5.337-1.397a9.961 9.961 0 0 0 4.657 1.149c5.516 0 9.994-4.478 9.994-9.994 0-5.516-4.478-9.762-9.994-9.762z"/>
              </svg>
              {{ __('Message us on WhatsApp', 'sage') }}
            </a>
          </div>
        @endif
      </div>

    </div>

    <div class="sv-footer__bottom">
      <span>&copy; {{ date('Y') }} SV Homes El Salvador. {{ __('All rights reserved.', 'sage') }}</span>
      <span>{{ __('Made with', 'sage') }} ❤️ {{ __('in El Salvador', 'sage') }} 🇸🇻</span>
    </div>
  </div>
</footer>
