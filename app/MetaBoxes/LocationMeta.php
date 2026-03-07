<?php

namespace App\MetaBoxes;

class LocationMeta
{
    public static function register(): void
    {
        add_action('add_meta_boxes', [self::class, 'addMetaBoxes']);
        add_action('save_post_location', [self::class, 'saveMetaBoxes'], 10, 2);
    }

    public static function addMetaBoxes(): void
    {
        add_meta_box(
            'sv_location_settings',
            __('Location Settings', 'sage'),
            [self::class, 'renderSettingsBox'],
            'location',
            'side',
            'high'
        );
    }

    public static function renderSettingsBox(\WP_Post $post): void
    {
        $termSlug      = get_post_meta($post->ID, '_sv_location_term_slug', true);
        $showProps     = get_post_meta($post->ID, '_sv_show_properties', true);
        $showProps     = $showProps === '1'; // default off
        $terms         = get_terms(['taxonomy' => 'property_location', 'hide_empty' => false]);

        wp_nonce_field('sv_location_save', 'sv_location_nonce');
        ?>
        <p style="margin-bottom:6px;">
            <label for="sv_location_term_slug">
                <strong><?= esc_html__('Linked Property Location', 'sage') ?></strong>
            </label>
        </p>
        <select name="sv_location_term_slug" id="sv_location_term_slug" style="width:100%;">
            <option value=""><?= esc_html__('— Select location term —', 'sage') ?></option>
            <?php if (! is_wp_error($terms)) : foreach ($terms as $term) : ?>
                <option value="<?= esc_attr($term->slug) ?>" <?= selected($termSlug, $term->slug, false) ?>>
                    <?= esc_html($term->name) ?> (<?= (int) $term->count ?> <?= esc_html__('properties', 'sage') ?>)
                </option>
            <?php endforeach; endif; ?>
        </select>
        <p style="margin-top:8px;font-size:11px;color:#666;">
            <?= esc_html__('Properties tagged with this location term will appear in the grid below the editorial content.', 'sage') ?>
        </p>
        <hr style="margin:12px 0;">
        <label style="display:flex;align-items:center;gap:6px;cursor:pointer;">
            <input type="checkbox" name="sv_show_properties" value="1" <?= checked($showProps, true, false) ?>>
            <strong><?= esc_html__('Show properties grid', 'sage') ?></strong>
        </label>
        <p style="margin-top:6px;font-size:11px;color:#666;">
            <?= esc_html__('Uncheck to hide the property grid and skip the query on this page.', 'sage') ?>
        </p>
        <hr style="margin:12px 0;">
        <?php $mapAddress = get_post_meta($post->ID, '_sv_map_address', true); ?>
        <p style="margin-bottom:6px;">
            <label for="sv_map_address">
                <strong><?= esc_html__('Google Maps Address', 'sage') ?></strong>
            </label>
        </p>
        <input type="text" name="sv_map_address" id="sv_map_address"
               value="<?= esc_attr($mapAddress) ?>" style="width:100%;"
               placeholder="<?= esc_attr__('e.g. San Salvador, El Salvador', 'sage') ?>">
        <p style="margin-top:6px;font-size:11px;color:#666;">
            <?= esc_html__('Used for the Google Maps embed at the bottom of the page. Requires Google Maps API key in Customizer.', 'sage') ?>
        </p>
        <?php
    }

    public static function saveMetaBoxes(int $postId, \WP_Post $post): void
    {
        if (
            ! isset($_POST['sv_location_nonce']) ||
            ! wp_verify_nonce($_POST['sv_location_nonce'], 'sv_location_save') ||
            (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
            ! current_user_can('edit_post', $postId)
        ) {
            return;
        }

        update_post_meta($postId, '_sv_location_term_slug', sanitize_key($_POST['sv_location_term_slug'] ?? ''));
        update_post_meta($postId, '_sv_show_properties', isset($_POST['sv_show_properties']) ? '1' : '0');
        update_post_meta($postId, '_sv_map_address', sanitize_text_field($_POST['sv_map_address'] ?? ''));
    }
}
