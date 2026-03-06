<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Add page slug to body class for easier per-page styling (e.g. header on contact).
 */
add_filter('body_class', function (array $classes) {
    if (is_singular() && get_post()) {
        $classes[] = 'page-slug-' . get_post()->post_name;
    }
    return $classes;
});

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return sprintf(' &hellip; <a href="%s">%s</a>', get_permalink(), __('Continued', 'sage'));
});

/**
 * Handle property inquiry form submission.
 */
add_action('admin_post_sv_property_inquiry', function () {
    if (
        ! isset($_POST['sv_inquiry_nonce']) ||
        ! wp_verify_nonce($_POST['sv_inquiry_nonce'], 'sv_inquiry_' . intval($_POST['property_id'] ?? 0))
    ) {
        wp_die(__('Security check failed.', 'sage'));
    }

    $propId    = intval($_POST['property_id'] ?? 0);
    $propTitle = sanitize_text_field($_POST['property_title'] ?? '');
    $name      = sanitize_text_field($_POST['inq_name'] ?? '');
    $phone     = sanitize_text_field($_POST['inq_phone'] ?? '');
    $email     = sanitize_email($_POST['inq_email'] ?? '');
    $message   = sanitize_textarea_field($_POST['inq_message'] ?? '');
    $redirect  = esc_url_raw($_POST['redirect_to'] ?? home_url('/'));

    if (empty($name) || empty($phone)) {
        wp_safe_redirect(add_query_arg('inquiry_sent', 'error', $redirect));
        exit;
    }

    $to      = get_option('admin_email');
    $subject = sprintf(__('New Inquiry: %s', 'sage'), $propTitle);
    $body    = "Property: {$propTitle}\nName: {$name}\nPhone: {$phone}\nEmail: {$email}\n\nMessage:\n{$message}";
    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    if ($email) {
        $headers[] = "Reply-To: {$name} <{$email}>";
    }

    wp_mail($to, $subject, $body, $headers);

    wp_safe_redirect(add_query_arg('inquiry_sent', '1', $redirect));
    exit;
});

add_action('admin_post_nopriv_sv_property_inquiry', function () {
    do_action('admin_post_sv_property_inquiry');
});

/**
 * Handle contact page form submission.
 */
add_action('admin_post_sv_contact_form', function () {
    if (
        ! isset($_POST['sv_contact_nonce']) ||
        ! wp_verify_nonce($_POST['sv_contact_nonce'], 'sv_contact_form')
    ) {
        wp_die(__('Security check failed.', 'sage'));
    }

    $name     = sanitize_text_field($_POST['contact_name'] ?? '');
    $last     = sanitize_text_field($_POST['contact_lastname'] ?? '');
    $email    = sanitize_email($_POST['contact_email'] ?? '');
    $phone    = sanitize_text_field($_POST['contact_phone'] ?? '');
    $subject  = sanitize_text_field($_POST['contact_subject'] ?? '');
    $message  = sanitize_textarea_field($_POST['contact_message'] ?? '');
    $redirect = esc_url_raw($_POST['redirect_to'] ?? home_url('/'));

    if (empty($name) || empty($email) || empty($message)) {
        wp_safe_redirect(add_query_arg('contact_sent', 'error', $redirect));
        exit;
    }

    $to      = get_option('admin_email');
    $subLine = sprintf(__('Contact from: %s %s', 'sage'), $name, $last);
    $body    = "Name: {$name} {$last}\nEmail: {$email}\nPhone: {$phone}\nSubject: {$subject}\n\nMessage:\n{$message}";
    $headers = ['Content-Type: text/plain; charset=UTF-8', "Reply-To: {$name} <{$email}>"];

    wp_mail($to, $subLine, $body, $headers);

    wp_safe_redirect($redirect);
    exit;
});

add_action('admin_post_nopriv_sv_contact_form', function () {
    do_action('admin_post_sv_contact_form');
});

/**
 * Noindex filtered archive/taxonomy pages to prevent cannibalization.
 */
add_filter('wp_robots', function (array $robots): array {
    if (!is_post_type_archive('property') &&
        !is_tax(['property_location', 'property_type', 'property_status'])) {
        return $robots;
    }

    $has_filter = false;

    foreach (['keyword', 'min_price', 'max_price', 'bedrooms', 'orderby'] as $p) {
        if (!empty($_GET[$p])) { $has_filter = true; break; }
    }

    if (!$has_filter) {
        $tax_params = array_filter(
            ['property_type', 'property_status', 'location'],
            fn($p) => !empty($_GET[$p])
        );
        if (is_tax() && !empty($tax_params)) {
            $has_filter = true;
        } elseif (is_post_type_archive('property') && count($tax_params) > 1) {
            $has_filter = true;
        }
    }

    if ($has_filter) {
        $robots['noindex'] = true;
        $robots['follow']  = true;
        unset($robots['index']);
    }

    return $robots;
});

/**
 * Output canonical for single-taxonomy-param archive pages.
 * Runs at priority 1 so SEO plugins (running later) can override if needed.
 */
add_action('wp_head', function (): void {
    if (!is_post_type_archive('property')) return;

    $non_empty = array_filter($_GET, fn($v) => $v !== '');
    if (count($non_empty) !== 1) return;

    $map = [
        'location'        => 'property_location',
        'property_type'   => 'property_type',
        'property_status' => 'property_status',
    ];

    foreach ($map as $param => $taxonomy) {
        if (!empty($_GET[$param])) {
            $term = get_term_by('slug', sanitize_key($_GET[$param]), $taxonomy);
            if ($term && !is_wp_error($term)) {
                $url = get_term_link($term);
                if (!is_wp_error($url)) {
                    echo '<link rel="canonical" href="' . esc_url($url) . '" />' . "\n";
                }
            }
            return;
        }
    }
}, 1);

/**
 * Meta description fallback — only fires when no SEO plugin is active.
 */
add_action('wp_head', function (): void {
    if (defined('WPSEO_VERSION') || function_exists('rank_math') || function_exists('aioseo')) return;

    $desc = '';

    if (is_singular('property')) {
        $exc = get_the_excerpt();
        if ($exc) $desc = wp_strip_all_tags($exc);
    } elseif (is_tax(['property_location', 'property_type', 'property_status'])) {
        $term = get_queried_object();
        if ($term && !empty($term->description)) {
            $desc = wp_strip_all_tags($term->description);
        } elseif ($term) {
            $desc = sprintf(
                __('Browse %d properties in %s — houses, apartments, land and more in El Salvador.', 'sage'),
                (int) $term->count,
                esc_html($term->name)
            );
        }
    } elseif (is_post_type_archive('property')) {
        $desc = __('Browse properties for sale and rent across the major cities of El Salvador — houses, apartments, land, farms, and commercial properties.', 'sage');
    }

    if ($desc) {
        echo '<meta name="description" content="' . esc_attr(wp_trim_words($desc, 30)) . '">' . "\n";
    }
}, 5);
