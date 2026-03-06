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

        $similar = $this->getRecommendedNearby($post);

        return [
            'property'        => $property,
            'amenitiesList'   => $amenitiesList,
            'similarProps'    => $similar['props'],
            'similarLocation' => $similar['location'],
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

    private function getRecommendedNearby(\WP_Post $post): array
    {
        $locs         = wp_get_post_terms($post->ID, 'property_location');
        $locationName = (! is_wp_error($locs) && ! empty($locs)) ? $locs[0]->name : '';
        $locationIds  = (! is_wp_error($locs) && ! empty($locs))
            ? wp_list_pluck($locs, 'term_id')
            : [];

        $args = [
            'post_type'      => 'property',
            'posts_per_page' => 3,
            'post__not_in'   => [$post->ID],
            'post_status'    => 'publish',
            'orderby'        => 'rand',
            'meta_query'     => [
                ['key' => '_sv_recommended', 'value' => '1', 'compare' => '='],
            ],
        ];

        if (! empty($locationIds)) {
            $args['tax_query'] = [
                ['taxonomy' => 'property_location', 'terms' => $locationIds],
            ];
        }

        $q = new \WP_Query($args);

        return [
            'props'    => FrontPage::hydrateProperties($q->posts),
            'location' => $locationName,
        ];
    }
}
