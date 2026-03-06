@extends('layouts.app')

@section('content')

{{-- Hero --}}
<div class="sv-blog-hero">
  <div class="sv-container">
    <div class="sv-section-eyebrow" style="color:var(--color-sv-gold-light);justify-content:center;">
      {{ __('Tips & News', 'sage') }}
    </div>
    <h1 class="sv-blog-hero__title">{{ __('Blog', 'sage') }}</h1>
    <p class="sv-blog-hero__subtitle">
      {{ __('Real estate insights, market tips, and news from El Salvador', 'sage') }}
    </p>
  </div>
</div>

<div class="sv-container sv-blog-archive">

  @if(have_posts())

    {{-- Featured / First Post --}}
    @php(the_post())
    <div class="sv-blog-featured">
      <a href="{{ get_permalink() }}" class="sv-blog-featured__img-wrap">
        @if(has_post_thumbnail())
          <img src="{{ get_the_post_thumbnail_url(null, 'large') }}" alt="{{ esc_attr(get_the_title()) }}" loading="eager">
        @else
          <div class="sv-blog-featured__img-placeholder">📝</div>
        @endif
      </a>
      <div class="sv-blog-featured__body">
        <div class="sv-blog-card__cat">
          @php($cats = get_the_category())
          {{ !empty($cats) ? esc_html($cats[0]->name) : __('Blog', 'sage') }}
        </div>
        <h2 class="sv-blog-featured__title">
          <a href="{{ get_permalink() }}">{{ get_the_title() }}</a>
        </h2>
        <div class="sv-blog-featured__meta">
          <span>{{ get_the_date() }}</span>
          <span class="sv-blog-featured__meta-dot">·</span>
          <span>{{ __('By', 'sage') }} {{ get_the_author() }}</span>
        </div>
        <p class="sv-blog-featured__excerpt">{{ wp_trim_words(get_the_excerpt(), 30) }}</p>
        <a href="{{ get_permalink() }}" class="sv-btn sv-btn-primary">
          {{ __('Read article', 'sage') }}
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
      </div>
    </div>

    {{-- Remaining posts grid --}}
    @if(have_posts())
      <div class="sv-blog-grid sv-blog-grid--below-featured">
        @while(have_posts()) @php(the_post())
          <article class="sv-blog-card">
            <div class="sv-blog-card__img">
              @if(has_post_thumbnail())
                <a href="{{ get_permalink() }}">
                  <img src="{{ get_the_post_thumbnail_url(null, 'medium_large') }}" alt="{{ esc_attr(get_the_title()) }}" loading="lazy">
                </a>
              @else
                <a href="{{ get_permalink() }}" class="sv-blog-card__no-img">📝</a>
              @endif
            </div>
            <div class="sv-blog-card__body">
              <div class="sv-blog-card__cat">
                @php($cats = get_the_category())
                {{ !empty($cats) ? esc_html($cats[0]->name) : __('Blog', 'sage') }}
              </div>
              <h3 class="sv-blog-card__title">
                <a href="{{ get_permalink() }}">{{ get_the_title() }}</a>
              </h3>
              <p class="sv-blog-card__excerpt">{{ wp_trim_words(get_the_excerpt(), 18) }}</p>
              <div class="sv-blog-card__footer-row">
                <span class="sv-blog-card__meta">{{ get_the_date() }}</span>
                <a href="{{ get_permalink() }}" class="sv-blog-card__read-more">
                  {{ __('Read more', 'sage') }} →
                </a>
              </div>
            </div>
          </article>
        @endwhile
      </div>
    @endif

    {{-- Pagination --}}
    <nav class="sv-pagination" aria-label="{{ __('Blog pagination', 'sage') }}">
      {!! paginate_links(['type' => 'list', 'prev_text' => '←', 'next_text' => '→']) !!}
    </nav>

  @else
    <div class="sv-blog-empty">
      <div class="sv-blog-empty__icon">📝</div>
      <h2>{{ __('No posts yet', 'sage') }}</h2>
      <p>{{ __('Check back soon for articles about El Salvador real estate.', 'sage') }}</p>
      <a href="{{ home_url('/') }}" class="sv-btn sv-btn-primary">{{ __('Back to Home', 'sage') }}</a>
    </div>
  @endif

</div>

@endsection
