<?php

/**
 * Theme setup.
 */

namespace App;

use App\PostTypes\Property;
use App\PostTypes\Location;
use App\MetaBoxes\PropertyMeta;
use App\MetaBoxes\LocationMeta;
use Illuminate\Support\Facades\Vite;

/**
 * Inject styles into the block editor.
 *
 * @return array
 */
add_filter('block_editor_settings_all', function ($settings) {
    $style = Vite::asset('resources/css/editor.css');

    $settings['styles'][] = [
        'css' => "@import url('{$style}')",
    ];

    return $settings;
});

/**
 * Inject scripts into the block editor.
 *
 * @return void
 */
add_action('admin_head', function () {
    if (! get_current_screen()?->is_block_editor()) {
        return;
    }

    if (! Vite::isRunningHot()) {
        $dependencies = json_decode(Vite::content('editor.deps.json'));

        foreach ($dependencies as $dependency) {
            if (! wp_script_is($dependency)) {
                wp_enqueue_script($dependency);
            }
        }
    }
    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

/**
 * Use the generated theme.json file.
 *
 * @return string
 */
add_filter('theme_file_path', function ($path, $file) {
    return $file === 'theme.json'
        ? public_path('build/assets/theme.json')
        : $path;
}, 10, 2);

/**
 * Disable on-demand block asset loading.
 *
 * @link https://core.trac.wordpress.org/ticket/61965
 */
add_filter('should_load_separate_core_block_assets', '__return_false');

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action('after_setup_theme', function () {
    /**
     * Disable full-site editing support.
     *
     * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
     */
    remove_theme_support('block-templates');

    /**
     * Register the navigation menus.
     *
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage'),
    ]);

    /**
     * Disable the default block patterns.
     *
     * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
     */
    remove_theme_support('core-block-patterns');

    /**
     * Enable plugins to manage the document title.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Enable post thumbnail support.
     *
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable responsive embed support.
     *
     * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
     */
    add_theme_support('responsive-embeds');

    /**
     * Enable HTML5 markup support.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', [
        'caption',
        'comment-form',
        'comment-list',
        'gallery',
        'search-form',
        'script',
        'style',
    ]);

    /**
     * Enable selective refresh for widgets in customizer.
     *
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
     */
    add_theme_support('customize-selective-refresh-widgets');
}, 20);

/**
 * Register Property CPT and meta boxes.
 */
add_action('init', [Property::class, 'register']);
add_action('init', [Location::class, 'register']);

PropertyMeta::register();
LocationMeta::register();

/**
 * Generate /{slug}/ and /{parent}/{child}/ permalinks for locations.
 *
 * Uses get_page_uri() — the same core function WordPress uses for pages.
 */
add_filter('post_type_link', function (string $link, \WP_Post $post): string {
    if ($post->post_type === 'location') {
        return home_url('/' . get_page_uri($post) . '/');
    }
    return $link;
}, 10, 2);

/**
 * Resolve incoming URLs as locations when they match.
 *
 * Hooks into parse_request and reads the raw URL path ($wp->request)
 * directly — no dependency on which rewrite rule matched or whether
 * pagename/name was set. Uses get_page_by_path() which handles
 * hierarchical parent/child resolution natively (same as pages).
 */
add_action('parse_request', function (\WP $wp): void {
    $path = trim($wp->request ?? '', '/');
    if (! $path || is_admin()) {
        return;
    }

    $location = get_page_by_path($path, OBJECT, 'location');
    if ($location) {
        $wp->query_vars = [
            'post_type' => 'location',
            'p'         => $location->ID,
        ];
    }
});

/**
 * Filter property archive queries to support custom search/filter params.
 */
add_action('pre_get_posts', function (\WP_Query $query) {
    if (is_admin() || ! $query->is_main_query()) {
        return;
    }

    if ($query->is_post_type_archive('property') || $query->is_tax(['property_type', 'property_status', 'property_location'])) {
        $query->set('posts_per_page', 12);

        $metaQuery = [];

        $minPrice = sanitize_text_field($_GET['min_price'] ?? '');
        $maxPrice = sanitize_text_field($_GET['max_price'] ?? '');

        if ($minPrice !== '') {
            $metaQuery[] = ['key' => '_sv_price', 'value' => (int) $minPrice, 'compare' => '>=', 'type' => 'NUMERIC'];
        }
        if ($maxPrice !== '') {
            $metaQuery[] = ['key' => '_sv_price', 'value' => (int) $maxPrice, 'compare' => '<=', 'type' => 'NUMERIC'];
        }

        $bedrooms = sanitize_text_field($_GET['bedrooms'] ?? '');
        if ($bedrooms !== '') {
            $metaQuery[] = ['key' => '_sv_bedrooms', 'value' => (int) $bedrooms, 'compare' => '>=', 'type' => 'NUMERIC'];
        }

        if (! empty($metaQuery)) {
            $metaQuery['relation'] = 'AND';
            $query->set('meta_query', $metaQuery);
        }

        $taxQuery = [];
        $type     = sanitize_text_field($_GET['property_type'] ?? '');
        $status   = sanitize_text_field($_GET['property_status'] ?? '');
        $location = sanitize_text_field($_GET['location'] ?? '');

        if ($type) {
            $taxQuery[] = ['taxonomy' => 'property_type', 'field' => 'slug', 'terms' => $type];
        }
        if ($status) {
            $taxQuery[] = ['taxonomy' => 'property_status', 'field' => 'slug', 'terms' => $status];
        }
        if ($location) {
            $taxQuery[] = ['taxonomy' => 'property_location', 'field' => 'slug', 'terms' => $location];
        }

        if (! empty($taxQuery)) {
            $taxQuery['relation'] = 'AND';
            $query->set('tax_query', $taxQuery);
        }

        $keyword = sanitize_text_field($_GET['keyword'] ?? '');
        if ($keyword) {
            $query->set('s', $keyword);
        }
    }
});

/**
 * Add custom admin columns for properties.
 */
add_filter('manage_property_posts_columns', function (array $cols): array {
    $new = [];
    foreach ($cols as $k => $v) {
        $new[$k] = $v;
        if ($k === 'title') {
            $new['sv_featured'] = __('Featured', 'sage');
            $new['sv_price']    = __('Price', 'sage');
            $new['sv_type']     = __('Type', 'sage');
            $new['sv_status']   = __('Status', 'sage');
        }
    }
    return $new;
});

add_action('manage_property_posts_custom_column', function (string $col, int $postId) {
    switch ($col) {
        case 'sv_featured':
            echo get_post_meta($postId, '_sv_featured', true) === '1' ? '<span style="color:#F0A500;font-size:18px;">&#9733;</span>' : '&mdash;';
            break;
        case 'sv_price':
            $price = get_post_meta($postId, '_sv_price', true);
            echo $price ? '<strong>$' . number_format((float) $price) . '</strong>' : '&mdash;';
            break;
        case 'sv_type':
            $terms = wp_get_post_terms($postId, 'property_type');
            echo ! is_wp_error($terms) && ! empty($terms) ? esc_html($terms[0]->name) : '&mdash;';
            break;
        case 'sv_status':
            $terms = wp_get_post_terms($postId, 'property_status');
            if (! is_wp_error($terms) && ! empty($terms)) {
                echo '<span style="background:#1B3A8A;color:#fff;padding:2px 8px;border-radius:3px;font-size:11px;">' . esc_html($terms[0]->name) . '</span>';
            } else {
                echo '&mdash;';
            }
            break;
    }
}, 10, 2);

/**
 * Register all SV Homes settings in the Customizer.
 */
add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {

    $wp_customize->add_panel('sv_homes', [
        'title'    => __('SV Homes', 'sage'),
        'priority' => 30,
    ]);

    /* ── Header / Logo ────────────────────────────────────────── */

    $wp_customize->add_section('sv_header', [
        'title' => __('Header & Logo', 'sage'),
        'panel' => 'sv_homes',
    ]);

    $wp_customize->add_setting('sv_header_logo', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'sv_header_logo', [
        'label'       => __('Logo Image', 'sage'),
        'description' => __('Upload a custom logo. Leave blank to use the default SVG. Recommended: square or 2:1 wide, transparent PNG.', 'sage'),
        'section'     => 'sv_header',
    ]));

    $wp_customize->add_setting('sv_logo_primary', [
        'type'              => 'option',
        'default'           => 'SV',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('sv_logo_primary', [
        'label'   => __('Logo Primary Text', 'sage'),
        'section' => 'sv_header',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('sv_logo_secondary', [
        'type'              => 'option',
        'default'           => 'Homes',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('sv_logo_secondary', [
        'label'   => __('Logo Secondary Text', 'sage'),
        'description' => __('Displayed in gold accent (e.g. "Homes" in "SV Homes").', 'sage'),
        'section' => 'sv_header',
        'type'    => 'text',
    ]);

    /* ── Hero Section ─────────────────────────────────────────── */

    $wp_customize->add_section('sv_hero', [
        'title' => __('Hero Section', 'sage'),
        'panel' => 'sv_homes',
    ]);

    $wp_customize->add_setting('sv_hero_title', [
        'type'              => 'option',
        'default'           => 'Your Home in El Salvador',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('sv_hero_title', [
        'label'   => __('Headline', 'sage'),
        'section' => 'sv_hero',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('sv_hero_subtitle', [
        'type'              => 'option',
        'default'           => 'Discover the best properties in the country of progress',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('sv_hero_subtitle', [
        'label'   => __('Subheadline', 'sage'),
        'section' => 'sv_hero',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('sv_hero_image_url', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control(new \WP_Customize_Image_Control($wp_customize, 'sv_hero_image_url', [
        'label'       => __('Background Image', 'sage'),
        'description' => __('Upload or select a high-quality image (min 2000 px wide). Leave blank for the default.', 'sage'),
        'section'     => 'sv_hero',
    ]));

    $wp_customize->add_setting('sv_families_helped', [
        'type'              => 'option',
        'default'           => 500,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('sv_families_helped', [
        'label'   => __('Families Helped (stat)', 'sage'),
        'section' => 'sv_hero',
        'type'    => 'number',
    ]);

    /* ── Homepage: Explore by location ─────────────────────────── */

    $wp_customize->add_section('sv_home_locations', [
        'title'       => __('Homepage locations', 'sage'),
        'description' => __('Choose which location pages appear in the "Explore by location" grid on the front page. Leave all unchecked to show every location.', 'sage'),
        'panel'       => 'sv_homes',
    ]);

    $wp_customize->add_setting('sv_home_location_ids', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => function ($value) {
            if (! is_string($value)) {
                return '';
            }
            $ids = array_filter(array_map('absint', explode(',', $value)));
            return implode(',', $ids);
        },
    ]);

    $wp_customize->add_control(new \App\Customizer\LocationChecklistControl($wp_customize, 'sv_home_location_ids', [
        'label'       => __('Location pages to show on homepage', 'sage'),
        'description' => __('Check the locations you want in the grid. Order is preserved.', 'sage'),
        'section'     => 'sv_home_locations',
    ]));

    /* ── Contact / WhatsApp ───────────────────────────────────── */

    $wp_customize->add_section('sv_contact', [
        'title' => __('Contact & WhatsApp', 'sage'),
        'panel' => 'sv_homes',
    ]);

    $wp_customize->add_setting('sv_whatsapp_global', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('sv_whatsapp_global', [
        'label'       => __('Global WhatsApp Number', 'sage'),
        'description' => __('Include country code, e.g. +50370000000', 'sage'),
        'section'     => 'sv_contact',
        'type'        => 'text',
    ]);

    /* ── API Keys ─────────────────────────────────────────────── */

    $wp_customize->add_section('sv_api_keys', [
        'title'       => __('API Keys', 'sage'),
        'panel'       => 'sv_homes',
        'capability'  => 'manage_options',
    ]);

    $wp_customize->add_setting('sv_google_maps_key', [
        'type'              => 'option',
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('sv_google_maps_key', [
        'label'       => __('Google Maps API Key', 'sage'),
        'description' => __('Restrict this key in Google Cloud Console by HTTP referrer to your domain. Keys are stored in the database and only visible to administrators.', 'sage'),
        'section'     => 'sv_api_keys',
        'type'        => 'text',
    ]);
});

/**
 * Store API keys with autoload=false so they are not loaded on every page request.
 */
add_action('customize_save_sv_google_maps_key', function () {
    $value = get_option('sv_google_maps_key', '');
    update_option('sv_google_maps_key', $value, false);
});

/**
 * Live preview for Customizer: hero headline and subheadline update as you type.
 */
add_action('customize_preview_init', function () {
    wp_add_inline_script('customize-preview', "
        (function() {
            if (typeof wp === 'undefined' || !wp.customize) return;
            function esc(s) { var d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML; }
            wp.customize('sv_hero_title', function(setting) {
                setting.bind(function(value) {
                    var el = document.getElementById('sv-hero-title');
                    if (el) el.innerHTML = (value || '').split('\\n').map(esc).join('<br>');
                });
            });
            wp.customize('sv_hero_subtitle', function(setting) {
                setting.bind(function(value) {
                    var el = document.getElementById('sv-hero-subtitle');
                    if (el) el.textContent = value || '';
                });
            });
        })();
    ");
});

/**
 * Register the theme sidebars.
 *
 * @return void
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ];

    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary',
    ] + $config);

    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer',
    ] + $config);
});
