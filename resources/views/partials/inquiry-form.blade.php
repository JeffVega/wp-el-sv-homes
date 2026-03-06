{{--
  Property Inquiry Form
  Optional: $propertyId, $propertyTitle, $whatsapp
--}}
@php
  $propId    = $propertyId ?? get_the_ID();
  $propTitle = $propertyTitle ?? get_the_title($propId);
  $wa        = $whatsapp ?? get_option('sv_whatsapp_global', '');
  $waNum     = preg_replace('/[^0-9]/', '', $wa);
  $waMsg     = urlencode(__('Hello, I\'m interested in the property: ', 'sage') . $propTitle . ' - ' . get_permalink($propId));
@endphp

@php($inquiryStatus = sanitize_text_field($_GET['inquiry_sent'] ?? ''))
@if($inquiryStatus === '1')
  <div class="sv-notice sv-notice--success">
    ✅ {{ __('Thank you! We will be in touch soon.', 'sage') }}
  </div>
@elseif($inquiryStatus === 'error')
  <div class="sv-notice sv-notice--error">
    ⚠️ {{ __('There was an error. Please try again.', 'sage') }}
  </div>
@endif

<form
  method="POST"
  action="{{ admin_url('admin-post.php') }}"
  class="sv-inquiry-form"
  novalidate
>
  @php(wp_nonce_field('sv_inquiry_' . $propId, 'sv_inquiry_nonce'))
  <input type="hidden" name="action" value="sv_property_inquiry">
  <input type="hidden" name="property_id" value="{{ $propId }}">
  <input type="hidden" name="property_title" value="{{ esc_attr($propTitle) }}">
  <input type="hidden" name="redirect_to" value="{{ get_permalink($propId) }}">
  <div aria-hidden="true" style="position:absolute;left:-9999px;"><input type="text" name="sv_hp_field" tabindex="-1" autocomplete="off" value=""></div>

  <div class="sv-form-group">
    <label class="sv-form-label" for="inq-name">{{ __('Full name', 'sage') }} <span style="color:#dc2626;">*</span></label>
    <input type="text" id="inq-name" name="inq_name" class="sv-form-control" required
           placeholder="{{ __('John Smith', 'sage') }}">
  </div>

  <div class="sv-form-group">
    <label class="sv-form-label" for="inq-phone">{{ __('Phone / WhatsApp', 'sage') }} <span style="color:#dc2626;">*</span></label>
    <input type="tel" id="inq-phone" name="inq_phone" class="sv-form-control" required
           placeholder="+503 7000 0000">
  </div>

  <div class="sv-form-group">
    <label class="sv-form-label" for="inq-email">{{ __('Email address', 'sage') }}</label>
    <input type="email" id="inq-email" name="inq_email" class="sv-form-control"
           placeholder="john@example.com">
  </div>

  <div class="sv-form-group">
    <label class="sv-form-label" for="inq-message">{{ __('Message', 'sage') }}</label>
    <textarea id="inq-message" name="inq_message" class="sv-form-control" rows="3"
              placeholder="{{ __('I\'m interested in this property, I would like more information…', 'sage') }}"></textarea>
  </div>

  <button type="submit" class="sv-btn sv-btn-primary" style="width:100%;justify-content:center;">
    {{ __('Send inquiry', 'sage') }}
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
      <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
    </svg>
  </button>

  @if($waNum)
    <div style="position:relative;text-align:center;margin:1rem 0;">
      <hr style="border-color:var(--color-sv-stone-light);">
      <span style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);background:#fff;padding:0 0.75rem;font-size:0.75rem;color:var(--color-sv-stone);">
        {{ __('or contact directly', 'sage') }}
      </span>
    </div>

    <a
      href="https://wa.me/{{ $waNum }}?text={{ $waMsg }}"
      target="_blank"
      rel="noopener"
      class="sv-btn"
      style="width:100%;justify-content:center;background:#25D366;color:#fff;border-color:#25D366;"
    >
      <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        <path d="M11.994 1.998C6.478 1.998 2 6.476 2 11.992c0 1.762.46 3.416 1.264 4.851L2 22.002l5.337-1.397a9.961 9.961 0 0 0 4.657 1.149c5.516 0 9.994-4.478 9.994-9.994 0-5.516-4.478-9.762-9.994-9.762z"/>
      </svg>
      {{ __('WhatsApp direct', 'sage') }}
    </a>
  @endif
</form>
