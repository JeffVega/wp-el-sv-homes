{{--
  Template Name: Contact Page
--}}
@extends('layouts.app')

@section('content')

{{-- Page Hero --}}
<div class="sv-page-hero py-20 pb-12">
  <div class="sv-container">
    <div class="sv-section-eyebrow text-sv-gold-light">{{ __('We\'re here to help you', 'sage') }}</div>
    <h1 class="font-display text-2xl sm:text-3xl font-extrabold text-white">
      {{ __('Contact Us', 'sage') }}
    </h1>
  </div>
</div>

<div class="sv-container sv-section-sm">
  <div class="sv-contact-layout">

    {{-- ── Contact Form ────────────────────────────────── --}}
    <div>
      <div class="bg-white rounded-xl p-10 shadow-[var(--shadow-card)]">
        <h2 class="font-display text-2xl text-sv-navy mb-2">
          {{ __('Send us a message', 'sage') }}
        </h2>
        <p class="text-sv-stone mb-8 text-sm">
          {{ __('Our team will respond to you as soon as possible.', 'sage') }}
        </p>

        @if(isset($_GET['contact_sent']) && $_GET['contact_sent'] === '1')
          <div class="sv-notice sv-notice--success">
            ✅ {{ __('Message sent! We will be in touch soon.', 'sage') }}
          </div>
        @endif

        <form method="POST" action="{{ admin_url('admin-post.php') }}" novalidate>
          @php(wp_nonce_field('sv_contact_form', 'sv_contact_nonce'))
          <input type="hidden" name="action" value="sv_contact_form">
          <input type="hidden" name="redirect_to" value="{{ home_url('/contact?contact_sent=1') }}">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <div class="sv-form-group mb-0">
              <label class="sv-form-label">{{ __('Name', 'sage') }} <span class="text-red-600">*</span></label>
              <input type="text" name="contact_name" class="sv-form-control" required placeholder="{{ __('Your name', 'sage') }}">
            </div>
            <div class="sv-form-group mb-0">
              <label class="sv-form-label">{{ __('Last name', 'sage') }}</label>
              <input type="text" name="contact_lastname" class="sv-form-control" placeholder="{{ __('Your last name', 'sage') }}">
            </div>
          </div>

          <div class="sv-form-group">
            <label class="sv-form-label">{{ __('Email address', 'sage') }} <span class="text-red-600">*</span></label>
            <input type="email" name="contact_email" class="sv-form-control" required placeholder="youremail@example.com">
          </div>

          <div class="sv-form-group">
            <label class="sv-form-label">{{ __('Phone / WhatsApp', 'sage') }}</label>
            <input type="tel" name="contact_phone" class="sv-form-control" placeholder="+503 7000 0000">
          </div>

          <div class="sv-form-group">
            <label class="sv-form-label">{{ __('How can we help you?', 'sage') }}</label>
            <select name="contact_subject" class="sv-form-control">
              <option value="">{{ __('Select an option', 'sage') }}</option>
              <option value="comprar">{{ __('I want to buy a property', 'sage') }}</option>
              <option value="alquilar">{{ __('I want to rent', 'sage') }}</option>
              <option value="vender">{{ __('I want to sell/list my property', 'sage') }}</option>
              <option value="invertir">{{ __('I want to invest in real estate', 'sage') }}</option>
              <option value="otro">{{ __('Other', 'sage') }}</option>
            </select>
          </div>

          <div class="sv-form-group">
            <label class="sv-form-label">{{ __('Message', 'sage') }} <span class="text-red-600">*</span></label>
            <textarea name="contact_message" class="sv-form-control" rows="5" required
                      placeholder="{{ __('Tell us what you need…', 'sage') }}"></textarea>
          </div>

          <button type="submit" class="sv-btn sv-btn-primary sv-btn-lg w-full justify-center">
            {{ __('Send message', 'sage') }}
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
              <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
          </button>
        </form>
      </div>
    </div>

    {{-- ── Contact Info Sidebar ─────────────────────────── --}}
    <div>
      <div class="sv-contact-info-card">
        <div class="sv-contact-info-item">
          <div class="sv-contact-info-icon">📍</div>
          <div>
            <div class="font-bold text-sv-navy mb-1">{{ __('Office', 'sage') }}</div>
            <div class="text-sm text-sv-stone">{{ get_option('sv_office_address', 'San Salvador, El Salvador') }}</div>
          </div>
        </div>

        <div class="sv-contact-info-item">
          <div class="sv-contact-info-icon">📱</div>
          <div>
            <div class="font-bold text-sv-navy mb-1">{{ __('Phone / WhatsApp', 'sage') }}</div>
            @php($wa = get_option('sv_whatsapp_global', ''))
            <div class="text-sm text-sv-stone">{{ $wa ?: '+503 0000 0000' }}</div>
            @if($wa)
              <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa) }}" target="_blank" rel="noopener"
                 class="inline-flex items-center gap-1.5 bg-[#25D366] text-white py-1.5 px-3.5 rounded-full text-[0.78rem] font-bold no-underline mt-2">
                💬 {{ __('Chat now', 'sage') }}
              </a>
            @endif
          </div>
        </div>

        <div class="sv-contact-info-item">
          <div class="sv-contact-info-icon">✉️</div>
          <div>
            <div class="font-bold text-sv-navy mb-1">{{ __('Email', 'sage') }}</div>
            <div class="text-sm text-sv-stone">{{ get_bloginfo('admin_email') }}</div>
          </div>
        </div>

        <div class="sv-contact-info-item">
          <div class="sv-contact-info-icon">🕐</div>
          <div>
            <div class="font-bold text-sv-navy mb-1">{{ __('Business hours', 'sage') }}</div>
            <div class="text-sm text-sv-stone">
              {{ __('Monday to Friday: 8am – 6pm', 'sage') }}<br>
              {{ __('Saturdays: 9am – 1pm', 'sage') }}
            </div>
          </div>
        </div>
      </div>

      {{-- Quick CTA --}}
      <div class="sv-page-hero rounded-xl p-8 text-center mt-6">
        <div class="text-3xl mb-3">🏠</div>
        <h3 class="font-display text-white text-lg mb-2">
          {{ __('Do you want to list your property?', 'sage') }}
        </h3>
        <p class="text-white/70 text-sm mb-5">
          {{ __('List your property and reach thousands of buyers in El Salvador.', 'sage') }}
        </p>
        @if($wa)
          <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $wa) }}?text={{ urlencode(__('Hello, I want to list my property on SV Homes', 'sage')) }}"
             target="_blank" rel="noopener" class="sv-btn sv-btn-gold">
            {{ __('List property', 'sage') }}
          </a>
        @endif
      </div>
    </div>

  </div>
</div>

@endsection
