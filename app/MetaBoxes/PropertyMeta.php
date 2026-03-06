<?php

namespace App\MetaBoxes;

class PropertyMeta
{
    public static function register(): void
    {
        add_action('add_meta_boxes', [self::class, 'addMetaBoxes']);
        add_action('save_post_property', [self::class, 'saveMetaBoxes'], 10, 2);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAdminScripts']);
    }

    public static function addMetaBoxes(): void
    {
        add_meta_box(
            'sv_property_details',
            __('Property Details', 'sage'),
            [self::class, 'renderDetailsBox'],
            'property',
            'normal',
            'high'
        );

        add_meta_box(
            'sv_property_location',
            __('Location & Map', 'sage'),
            [self::class, 'renderLocationBox'],
            'property',
            'normal',
            'high'
        );

        add_meta_box(
            'sv_property_gallery',
            __('Photo Gallery', 'sage'),
            [self::class, 'renderGalleryBox'],
            'property',
            'normal',
            'default'
        );

        add_meta_box(
            'sv_property_contact',
            __('Contact / Listing Agent', 'sage'),
            [self::class, 'renderContactBox'],
            'property',
            'side',
            'default'
        );

        add_meta_box(
            'sv_property_flags',
            __('Listing Flags', 'sage'),
            [self::class, 'renderFlagsBox'],
            'property',
            'side',
            'high'
        );
    }

    public static function enqueueAdminScripts(string $hook): void
    {
        global $post;
        if (! in_array($hook, ['post.php', 'post-new.php'], true)) {
            return;
        }
        if (! isset($post) || $post->post_type !== 'property') {
            return;
        }
        wp_enqueue_media();
    }

    // ─── Render Callbacks ────────────────────────────────────────────────────

    public static function renderDetailsBox(\WP_Post $post): void
    {
        wp_nonce_field('sv_property_meta_nonce', 'sv_property_meta_nonce');
        $m = self::getMeta($post->ID);
        ?>
        <style>
            .sv-meta-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
            .sv-meta-field label { display:block; font-weight:600; margin-bottom:4px; color:#1B3A8A; }
            .sv-meta-field input, .sv-meta-field select, .sv-meta-field textarea {
                width:100%; padding:6px 10px; border:1px solid #ddd; border-radius:4px;
            }
            .sv-meta-section-title { font-size:13px; font-weight:700; color:#1B3A8A;
                border-bottom:2px solid #1B3A8A; padding-bottom:4px; margin:16px 0 12px;
                text-transform:uppercase; letter-spacing:.05em; }
            .sv-amenities-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; }
            .sv-amenities-grid label { font-weight:400; display:flex; align-items:center; gap:6px; cursor:pointer; }
        </style>

        <div class="sv-meta-section-title"><?= esc_html__('Pricing', 'sage') ?></div>
        <div class="sv-meta-grid">
            <div class="sv-meta-field">
                <label><?= esc_html__('Price (USD)', 'sage') ?></label>
                <input type="number" name="_sv_price" value="<?= esc_attr($m['_sv_price']) ?>" min="0" step="100" placeholder="150000">
            </div>
            <div class="sv-meta-field">
                <label><?= esc_html__('Price Label', 'sage') ?></label>
                <select name="_sv_price_label">
                    <?php foreach (['', '/month', '/year', 'Negotiable', 'Consult Price'] as $opt): ?>
                        <option value="<?= esc_attr($opt) ?>" <?= selected($m['_sv_price_label'], $opt, false) ?>><?= esc_html($opt ?: '— none —') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="sv-meta-field">
                <label><?= esc_html__('Year Built', 'sage') ?></label>
                <input type="number" name="_sv_year_built" value="<?= esc_attr($m['_sv_year_built']) ?>" min="1900" max="<?= date('Y') ?>" placeholder="2020">
            </div>
        </div>

        <div class="sv-meta-section-title"><?= esc_html__('Dimensions', 'sage') ?></div>
        <div class="sv-meta-grid">
            <div class="sv-meta-field">
                <label><?= esc_html__('Construction Area (m²)', 'sage') ?></label>
                <input type="number" name="_sv_area_m2" value="<?= esc_attr($m['_sv_area_m2']) ?>" min="0" step="0.5">
            </div>
            <div class="sv-meta-field">
                <label><?= esc_html__('Land / Lot Area (m²)', 'sage') ?></label>
                <input type="number" name="_sv_land_area_m2" value="<?= esc_attr($m['_sv_land_area_m2']) ?>" min="0" step="0.5">
            </div>
            <div class="sv-meta-field">
                <label><?= esc_html__('Parking Spaces', 'sage') ?></label>
                <input type="number" name="_sv_parking" value="<?= esc_attr($m['_sv_parking']) ?>" min="0">
            </div>
        </div>
        <div class="sv-meta-grid" style="margin-top:12px;">
            <div class="sv-meta-field">
                <label><?= esc_html__('Bedrooms', 'sage') ?></label>
                <input type="number" name="_sv_bedrooms" value="<?= esc_attr($m['_sv_bedrooms']) ?>" min="0">
            </div>
            <div class="sv-meta-field">
                <label><?= esc_html__('Bathrooms', 'sage') ?></label>
                <input type="number" name="_sv_bathrooms" value="<?= esc_attr($m['_sv_bathrooms']) ?>" min="0" step="0.5">
            </div>
            <div class="sv-meta-field">
                <label><?= esc_html__('Half Baths', 'sage') ?></label>
                <input type="number" name="_sv_half_baths" value="<?= esc_attr($m['_sv_half_baths']) ?>" min="0">
            </div>
        </div>

        <div class="sv-meta-section-title"><?= esc_html__('Amenities & Features', 'sage') ?></div>
        <?php
        $amenities = self::amenitiesList();
        $selected  = (array) json_decode($m['_sv_amenities'] ?? '[]', true);
        ?>
        <div class="sv-amenities-grid">
            <?php foreach ($amenities as $key => $label): ?>
                <label>
                    <input type="checkbox" name="_sv_amenities[]" value="<?= esc_attr($key) ?>"
                        <?= in_array($key, $selected, true) ? 'checked' : '' ?>>
                    <?= esc_html($label) ?>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="sv-meta-section-title"><?= esc_html__('Media', 'sage') ?></div>
        <div class="sv-meta-field">
            <label><?= esc_html__('Video Tour URL (YouTube / Vimeo)', 'sage') ?></label>
            <input type="url" name="_sv_video_url" value="<?= esc_attr($m['_sv_video_url']) ?>" placeholder="https://www.youtube.com/watch?v=...">
        </div>
        <?php
    }

    public static function renderLocationBox(\WP_Post $post): void
    {
        $m = self::getMeta($post->ID);
        ?>
        <style>.sv-meta-2col { display:grid; grid-template-columns:1fr 1fr; gap:16px; }</style>
        <div class="sv-meta-2col">
            <div class="sv-meta-field">
                <label><?= esc_html__('Street Address', 'sage') ?></label>
                <input type="text" name="_sv_address" value="<?= esc_attr($m['_sv_address']) ?>" placeholder="Calle Principal 123, Colonia...">
            </div>
            <div class="sv-meta-field">
                <label><?= esc_html__('City / Municipality', 'sage') ?></label>
                <input type="text" name="_sv_city" value="<?= esc_attr($m['_sv_city']) ?>" placeholder="San Salvador">
            </div>
            <div class="sv-meta-field">
                <label><?= esc_html__('Google Maps Latitude', 'sage') ?></label>
                <input type="text" name="_sv_map_lat" value="<?= esc_attr($m['_sv_map_lat']) ?>" placeholder="13.6929">
            </div>
            <div class="sv-meta-field">
                <label><?= esc_html__('Google Maps Longitude', 'sage') ?></label>
                <input type="text" name="_sv_map_lng" value="<?= esc_attr($m['_sv_map_lng']) ?>" placeholder="-89.2182">
            </div>
        </div>
        <p style="margin-top:12px;color:#666;font-size:12px;">
            <?= esc_html__('Get coordinates from Google Maps: right-click on the location and copy lat/lng.', 'sage') ?>
        </p>
        <?php
    }

    public static function renderGalleryBox(\WP_Post $post): void
    {
        $m       = self::getMeta($post->ID);
        $gallery = array_filter((array) json_decode($m['_sv_gallery'] ?? '[]', true));
        ?>
        <style>
            #sv-gallery-preview { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:12px; min-height:60px; border:2px dashed #ddd; padding:8px; border-radius:4px; }
            #sv-gallery-preview img { width:80px; height:60px; object-fit:cover; border-radius:4px; cursor:pointer; border:2px solid transparent; }
            #sv-gallery-preview img:hover { border-color:#c00; }
        </style>
        <input type="hidden" name="_sv_gallery" id="sv-gallery-ids" value="<?= esc_attr(json_encode($gallery)) ?>">
        <div id="sv-gallery-preview">
            <?php foreach ($gallery as $id): ?>
                <?php if ($url = wp_get_attachment_image_url($id, 'thumbnail')): ?>
                    <img src="<?= esc_url($url) ?>" data-id="<?= intval($id) ?>" title="<?= esc_attr__('Click to remove', 'sage') ?>">
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <button type="button" id="sv-gallery-btn" class="button button-secondary">
            <?= esc_html__('Add / Edit Gallery Photos', 'sage') ?>
        </button>
        <script>
        (function($) {
            var frame;
            $('#sv-gallery-btn').on('click', function() {
                if (frame) { frame.open(); return; }
                frame = wp.media({ title: '<?= esc_js(__('Select Gallery Images', 'sage')) ?>', multiple: true });
                frame.on('select', function() {
                    var ids = frame.state().get('selection').map(function(a){ return a.id; });
                    $('#sv-gallery-ids').val(JSON.stringify(ids));
                    var html = '';
                    frame.state().get('selection').each(function(a) {
                        html += '<img src="' + a.attributes.sizes.thumbnail.url + '" data-id="' + a.id + '" title="Click to remove">';
                    });
                    $('#sv-gallery-preview').html(html);
                });
                frame.open();
            });
            $(document).on('click', '#sv-gallery-preview img', function() {
                var id = $(this).data('id');
                $(this).remove();
                var current = JSON.parse($('#sv-gallery-ids').val() || '[]');
                current = current.filter(function(i){ return i !== id; });
                $('#sv-gallery-ids').val(JSON.stringify(current));
            });
        })(jQuery);
        </script>
        <?php
    }

    public static function renderContactBox(\WP_Post $post): void
    {
        $m = self::getMeta($post->ID);
        ?>
        <div class="sv-meta-field" style="margin-bottom:12px;">
            <label style="font-weight:600;display:block;margin-bottom:4px;"><?= esc_html__('Agent WhatsApp Number', 'sage') ?></label>
            <input type="text" name="_sv_whatsapp" value="<?= esc_attr($m['_sv_whatsapp']) ?>"
                placeholder="+503 7000 0000" style="width:100%;padding:6px 10px;border:1px solid #ddd;border-radius:4px;">
            <p style="font-size:11px;color:#666;margin-top:4px;"><?= esc_html__('Include country code. e.g. +50370000000', 'sage') ?></p>
        </div>
        <div class="sv-meta-field">
            <label style="font-weight:600;display:block;margin-bottom:4px;"><?= esc_html__('Agent Name (optional)', 'sage') ?></label>
            <input type="text" name="_sv_agent_name" value="<?= esc_attr($m['_sv_agent_name']) ?>"
                placeholder="Ana López" style="width:100%;padding:6px 10px;border:1px solid #ddd;border-radius:4px;">
        </div>
        <?php
    }

    public static function renderFlagsBox(\WP_Post $post): void
    {
        $m = self::getMeta($post->ID);
        ?>
        <label style="display:flex;align-items:center;gap:10px;font-weight:600;cursor:pointer;margin-bottom:12px;">
            <input type="checkbox" name="_sv_featured" value="1" <?= checked($m['_sv_featured'], '1', false) ?> style="width:18px;height:18px;">
            <span style="color:#F0A500;">&#9733;</span> <?= esc_html__('Mark as Featured', 'sage') ?>
        </label>
        <label style="display:flex;align-items:center;gap:10px;font-weight:600;cursor:pointer;">
            <input type="checkbox" name="_sv_hot_deal" value="1" <?= checked($m['_sv_hot_deal'], '1', false) ?> style="width:18px;height:18px;">
            <span style="color:#e53e3e;">&#128293;</span> <?= esc_html__('Hot Deal', 'sage') ?>
        </label>
        <?php
    }

    // ─── Save ────────────────────────────────────────────────────────────────

    public static function saveMetaBoxes(int $postId, \WP_Post $post): void
    {
        if (
            ! isset($_POST['sv_property_meta_nonce']) ||
            ! wp_verify_nonce($_POST['sv_property_meta_nonce'], 'sv_property_meta_nonce') ||
            defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ||
            ! current_user_can('edit_post', $postId)
        ) {
            return;
        }

        $textFields = [
            '_sv_price', '_sv_price_label', '_sv_year_built',
            '_sv_area_m2', '_sv_land_area_m2', '_sv_parking',
            '_sv_bedrooms', '_sv_bathrooms', '_sv_half_baths',
            '_sv_video_url', '_sv_address', '_sv_city',
            '_sv_map_lat', '_sv_map_lng',
            '_sv_whatsapp', '_sv_agent_name',
        ];

        foreach ($textFields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($postId, $field, sanitize_text_field($_POST[$field]));
            }
        }

        // Checkboxes
        update_post_meta($postId, '_sv_featured', isset($_POST['_sv_featured']) ? '1' : '0');
        update_post_meta($postId, '_sv_hot_deal', isset($_POST['_sv_hot_deal']) ? '1' : '0');

        // Amenities (array)
        $amenities = isset($_POST['_sv_amenities']) ? array_map('sanitize_text_field', (array) $_POST['_sv_amenities']) : [];
        update_post_meta($postId, '_sv_amenities', json_encode($amenities));

        // Gallery (JSON)
        if (isset($_POST['_sv_gallery'])) {
            $gallery = json_decode(stripslashes($_POST['_sv_gallery']), true);
            $gallery = is_array($gallery) ? array_map('intval', $gallery) : [];
            update_post_meta($postId, '_sv_gallery', json_encode($gallery));
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public static function getMeta(int $postId): array
    {
        $fields = [
            '_sv_price', '_sv_price_label', '_sv_year_built',
            '_sv_area_m2', '_sv_land_area_m2', '_sv_parking',
            '_sv_bedrooms', '_sv_bathrooms', '_sv_half_baths',
            '_sv_video_url', '_sv_address', '_sv_city',
            '_sv_map_lat', '_sv_map_lng',
            '_sv_whatsapp', '_sv_agent_name',
            '_sv_featured', '_sv_hot_deal',
            '_sv_amenities', '_sv_gallery',
        ];

        $meta = [];
        foreach ($fields as $field) {
            $meta[$field] = get_post_meta($postId, $field, true) ?: '';
        }
        return $meta;
    }

    public static function amenitiesList(): array
    {
        return [
            'pool'            => __('Swimming Pool', 'sage'),
            'gym'             => __('Gym / Fitness', 'sage'),
            'security'        => __('24h Security', 'sage'),
            'gated'           => __('Gated Community', 'sage'),
            'air_conditioning'=> __('Air Conditioning', 'sage'),
            'garden'          => __('Garden / Yard', 'sage'),
            'terrace'         => __('Terrace / Balcony', 'sage'),
            'laundry'         => __('Laundry Room', 'sage'),
            'storage'         => __('Storage Room', 'sage'),
            'elevator'        => __('Elevator', 'sage'),
            'generator'       => __('Generator', 'sage'),
            'solar'           => __('Solar Panels', 'sage'),
            'fiber_internet'  => __('Fiber Internet', 'sage'),
            'cable_tv'        => __('Cable TV', 'sage'),
            'ocean_view'      => __('Ocean View', 'sage'),
            'mountain_view'   => __('Mountain View', 'sage'),
            'beachfront'      => __('Beachfront', 'sage'),
            'water_24h'       => __('24h Water Supply', 'sage'),
            'bbq'             => __('BBQ Area', 'sage'),
            'playground'      => __('Playground', 'sage'),
        ];
    }
}
