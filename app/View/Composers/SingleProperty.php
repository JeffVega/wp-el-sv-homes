<?php

namespace App\View\Composers;

use App\MetaBoxes\PropertyMeta;
use Roots\Acorn\View\Composer;

class SingleProperty extends Composer
{
    protected static $views = ['single-property'];

    public function with(): array
    {
        global $post;

        if (! $post || $post->post_type !== 'property') {
            return [];
        }

        $property = FrontPage::hydrateProperties([$post])[0];
        $amenitiesList = PropertyMeta::amenitiesList();

        return [
            'property'        => $property,
            'amenitiesList'   => $amenitiesList,
            'similarProps'    => $this->getSimilarProperties($post),
            'whatsappGlobal'  => get_option('sv_whatsapp_global', ''),
            'galleryImages'   => $this->buildGallery($property),
        ];
    }

    private function buildGallery(array $p): array
    {
        $images = [];

        // Thumbnail first
        if ($p['thumbnail']) {
            $images[] = ['url' => $p['thumbnail'], 'alt' => $p['title']];
        }

        // Then gallery images
        foreach ($p['gallery'] as $id) {
            $url = wp_get_attachment_image_url($id, 'large');
            if ($url && $url !== $p['thumbnail']) {
                $images[] = [
                    'url' => $url,
                    'alt' => get_post_meta($id, '_wp_attachment_image_alt', true) ?: $p['title'],
                ];
            }
        }

        return $images;
    }

    private function getSimilarProperties(\WP_Post $post): array
    {
        $types = wp_get_post_terms($post->ID, 'property_type', ['fields' => 'ids']);

        $args = [
            'post_type'      => 'property',
            'posts_per_page' => 3,
            'post__not_in'   => [$post->ID],
            'post_status'    => 'publish',
            'orderby'        => 'rand',
        ];

        if (! is_wp_error($types) && ! empty($types)) {
            $args['tax_query'] = [
                ['taxonomy' => 'property_type', 'terms' => $types],
            ];
        }

        $q = new \WP_Query($args);
        return FrontPage::hydrateProperties($q->posts);
    }
}
