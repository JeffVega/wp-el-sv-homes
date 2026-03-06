@extends('layouts.app')

@section('content')
  <div class="sv-single-post">
    @while(have_posts()) @php(the_post())
      {{-- Breadcrumb --}}
      <div style="background:var(--color-sv-sky);padding:0.875rem 0;border-bottom:1px solid var(--color-sv-stone-light);">
        <div class="sv-container" style="font-size:0.82rem;color:var(--color-sv-stone);display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap;">
          <a href="{{ home_url('/') }}" style="color:var(--color-sv-blue);text-decoration:none;">{{ __('Home', 'sage') }}</a>
          <span>›</span>
          <a href="{{ get_permalink(get_option('page_for_posts')) ?: home_url('/blog') }}" style="color:var(--color-sv-blue);text-decoration:none;">{{ __('Blog', 'sage') }}</a>
          <span>›</span>
          <span style="color:var(--color-sv-navy);font-weight:600;">{{ get_the_title() }}</span>
        </div>
      </div>
      @php($thumb = get_the_post_thumbnail_url(null, 'full'))
      <div class="sv-blog-single-hero {{ $thumb ? 'sv-blog-single-hero--has-image' : '' }}" @if($thumb) style="background-image: linear-gradient(135deg, rgba(15, 35, 55, 0.88) 0%, rgba(25, 55, 85, 0.82) 100%), url({{ $thumb }}); background-size: cover; background-position: center;" @endif>
        <div class="sv-container">
          <div class="sv-section-eyebrow" style="color:var(--color-sv-gold-light);">
            {{ __('Blog', 'sage') }}
          </div>
          <h1 class="sv-blog-single-hero__title">{{ get_the_title() }}</h1>
          <div class="sv-blog-single-hero__meta">
            <time datetime="{{ get_post_time('c', true) }}">{{ get_the_date() }}</time>
            <span aria-hidden="true"> · </span>
            <span>{{ __('By', 'sage') }} {{ get_the_author() }}</span>
          </div>
        </div>
      </div>
      <div class="sv-container sv-blog-single-body">
        @includeFirst(['partials.content-single-' . get_post_type(), 'partials.content-single'])
      </div>
    @endwhile
  </div>
@endsection
