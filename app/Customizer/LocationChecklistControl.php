<?php

namespace App\Customizer;

use WP_Customize_Control;

/**
 * Customizer control: checkboxes for which location (CPT) pages to show on the homepage.
 * Setting value is a comma-separated list of post IDs.
 */
class LocationChecklistControl extends WP_Customize_Control
{
    public $type = 'sv_location_checklist';

    public function render_content(): void
    {
        $saved = is_string($this->value()) ? $this->value() : '';
        $ids   = array_filter(array_map('absint', explode(',', $saved)));
        $all   = get_posts([
            'post_type'      => 'location',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        ?>
        <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
        <?php if (! empty($this->description)) : ?>
            <span class="description customize-control-description"><?php echo esc_html($this->description); ?></span>
        <?php endif; ?>

        <input type="hidden" <?php $this->link(); ?> value="<?php echo esc_attr($saved); ?>" id="<?php echo esc_attr($this->id); ?>-input">

        <div class="sv-location-checklist" style="max-height:280px; overflow-y:auto; border:1px solid #ddd; padding:10px; margin-top:6px;">
            <?php if (empty($all)) : ?>
                <p style="margin:0;"><?php esc_html_e('No location pages found. Create location posts first.', 'sage'); ?></p>
            <?php else : ?>
                <ul style="list-style:none; margin:0; padding:0;">
                    <?php foreach ($all as $post) :
                        $checked = in_array((int) $post->ID, $ids, true);
                        ?>
                        <li style="margin:4px 0;">
                            <label style="display:flex; align-items:center; gap:8px;">
                                <input type="checkbox" value="<?php echo esc_attr($post->ID); ?>" <?php checked($checked); ?> class="sv-location-checkbox">
                                <span><?php echo esc_html($post->post_title); ?></span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <script>
        (function() {
            var input = document.getElementById('<?php echo esc_js($this->id); ?>-input');
            if (!input) return;
            var container = input.closest('.customize-control');
            if (!container) return;
            var checkboxes = container.querySelectorAll('.sv-location-checkbox');
            function update() {
                var ids = [];
                checkboxes.forEach(function(cb) { if (cb.checked) ids.push(cb.value); });
                input.value = ids.join(',');
                input.dispatchEvent(new Event('change'));
            }
            checkboxes.forEach(function(cb) { cb.addEventListener('change', update); });
        })();
        </script>
        <?php
    }
}
