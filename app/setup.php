<?php

/**
 * Theme setup.
 */

namespace App;

use App\PostTypes\Property;
use App\MetaBoxes\PropertyMeta;
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
PropertyMeta::register();

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
 * Add a global WhatsApp + Maps settings page under Settings menu.
 */
add_action('admin_menu', function () {
    add_options_page(
        __('SV Homes Settings', 'sage'),
        __('SV Homes', 'sage'),
        'manage_options',
        'sv-homes-settings',
        function () {
            if (isset($_POST['sv_homes_nonce']) && wp_verify_nonce($_POST['sv_homes_nonce'], 'sv_homes_save')) {
                update_option('sv_whatsapp_global', sanitize_text_field($_POST['sv_whatsapp_global'] ?? ''));
                update_option('sv_google_maps_key', sanitize_text_field($_POST['sv_google_maps_key'] ?? ''));
                update_option('sv_hero_title', sanitize_text_field($_POST['sv_hero_title'] ?? ''));
                update_option('sv_hero_subtitle', sanitize_text_field($_POST['sv_hero_subtitle'] ?? ''));
                update_option('sv_families_helped', intval($_POST['sv_families_helped'] ?? 500));
                echo '<div class="notice notice-success"><p>' . esc_html__('Settings saved!', 'sage') . '</p></div>';
            }
            ?>
            <div class="wrap">
                <h1><?= esc_html__('El Salvador Homes — Settings', 'sage') ?></h1>
                <form method="post">
                    <?php wp_nonce_field('sv_homes_save', 'sv_homes_nonce'); ?>
                    <table class="form-table">
                        <tr><th><?= esc_html__('Global WhatsApp Number', 'sage') ?></th>
                            <td><input type="text" name="sv_whatsapp_global" class="regular-text" value="<?= esc_attr(get_option('sv_whatsapp_global')) ?>" placeholder="+50370000000"></td>
                        </tr>
                        <tr><th><?= esc_html__('Google Maps API Key', 'sage') ?></th>
                            <td><input type="text" name="sv_google_maps_key" class="regular-text" value="<?= esc_attr(get_option('sv_google_maps_key')) ?>"></td>
                        </tr>
                        <tr><th><?= esc_html__('Hero Headline', 'sage') ?></th>
                            <td><input type="text" name="sv_hero_title" class="regular-text" value="<?= esc_attr(get_option('sv_hero_title', 'Tu Hogar en El Salvador')) ?>"></td>
                        </tr>
                        <tr><th><?= esc_html__('Hero Subheadline', 'sage') ?></th>
                            <td><input type="text" name="sv_hero_subtitle" class="large-text" value="<?= esc_attr(get_option('sv_hero_subtitle', 'Descubre las mejores propiedades en el país del progreso')) ?>"></td>
                        </tr>
                        <tr><th><?= esc_html__('Families Helped (stat)', 'sage') ?></th>
                            <td><input type="number" name="sv_families_helped" value="<?= esc_attr(get_option('sv_families_helped', 500)) ?>"></td>
                        </tr>
                    </table>
                    <?php submit_button(__('Save Settings', 'sage')); ?>
                </form>
            </div>
            <?php
        }
    );
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
