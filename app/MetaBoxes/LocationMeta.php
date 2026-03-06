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
        $termSlug = get_post_meta($post->ID, '_sv_location_term_slug', true);
        $terms    = get_terms(['taxonomy' => 'property_location', 'hide_empty' => false]);

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
    }
}
