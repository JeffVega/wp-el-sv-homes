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
