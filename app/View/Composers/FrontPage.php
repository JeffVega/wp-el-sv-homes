<?php

namespace App\View\Composers;

use App\MetaBoxes\PropertyMeta;
use Roots\Acorn\View\Composer;

class FrontPage extends Composer
{
    protected static $views = ['front-page'];

    public function with(): array
    {
        return [
            'featuredProperties' => $this->getFeaturedProperties(),
            'latestProperties'   => $this->getLatestProperties(),
            'propertyCounts'     => $this->getPropertyCounts(),
            'heroTitle'          => get_option('sv_hero_title', 'Your Home in El Salvador'),
            'heroSubtitle'       => get_option('sv_hero_subtitle', 'Discover the best properties in the land of progress'),
            'heroImage'          => $this->getHeroImage(),
            'whatsappGlobal'     => get_option('sv_whatsapp_global', ''),
            'propertyTypes'      => $this->getPropertyTypes(),
            'locations'          => $this->getLocations(),
        ];
    }

    private function getHeroImage(): string
    {
        $url = get_option('sv_hero_image_url', '');

        return $url ?: 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=2000&q=80';
    }

    private function getFeaturedProperties(): array
    {
        $query = new \WP_Query([
            'post_type'      => 'property',
            'posts_per_page' => 6,
            'meta_query'     => [
                ['key' => '_sv_featured', 'value' => '1', 'compare' => '='],
            ],
            'post_status'    => 'publish',
        ]);

        return $this->hydrateProperties($query->posts);
    }

    private function getLatestProperties(): array
    {
        $query = new \WP_Query([
            'post_type'      => 'property',
            'posts_per_page' => 8,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'post_status'    => 'publish',
        ]);

        return $this->hydrateProperties($query->posts);
    }

    private function getPropertyCounts(): array
    {
        $total = wp_count_posts('property');
        return [
            'total'    => (int) ($total->publish ?? 0),
            'sale'     => $this->countByStatus('for-sale'),
            'rent'     => $this->countByStatus('for-rent'),
            'families' => (int) get_option('sv_families_helped', 500),
        ];
    }

    private function countByStatus(string $slug): int
    {
        $term = get_term_by('slug', $slug, 'property_status');
        if (! $term) {
            return 0;
        }
        $q = new \WP_Query([
            'post_type'      => 'property',
            'posts_per_page' => 1,
            'tax_query'      => [['taxonomy' => 'property_status', 'terms' => $term->term_id]],
            'fields'         => 'ids',
        ]);
        return (int) $q->found_posts;
    }

    private function getPropertyTypes(): array
    {
        $terms = get_terms(['taxonomy' => 'property_type', 'hide_empty' => false]);
        if (is_wp_error($terms)) {
            return [];
        }
        return array_map(fn($t) => [
            'name'  => $t->name,
            'slug'  => $t->slug,
            'count' => $t->count,
            'link'  => get_term_link($t),
        ], $terms);
    }

    private function getLocations(): array
    {
        $cptPosts = get_posts([
            'post_type'      => 'location',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        return array_map(function (\WP_Post $p) {
            $termSlug = get_post_meta($p->ID, '_sv_location_term_slug', true);
            $count    = 0;

            if ($termSlug) {
                $term = get_term_by('slug', $termSlug, 'property_location');
                if ($term && ! is_wp_error($term)) {
                    $count = (int) $term->count;
                }
            }

            return [
                'name'  => get_the_title($p),
                'slug'  => $p->post_name,
                'count' => $count,
                'link'  => get_permalink($p),
            ];
        }, $cptPosts);
    }

    public static function hydrateProperties(array $posts): array
    {
        return array_map(function (\WP_Post $p) {
            $meta    = PropertyMeta::getMeta($p->ID);
            $amenities = (array) json_decode($meta['_sv_amenities'] ?? '[]', true);
            $gallery   = array_filter((array) json_decode($meta['_sv_gallery'] ?? '[]', true));

            $types    = wp_get_post_terms($p->ID, 'property_type');
            $statuses = wp_get_post_terms($p->ID, 'property_status');
            $locs     = wp_get_post_terms($p->ID, 'property_location');

            return [
                'id'          => $p->ID,
                'title'       => get_the_title($p),
                'permalink'   => get_permalink($p),
                'excerpt'     => get_the_excerpt($p),
                'thumbnail'   => get_the_post_thumbnail_url($p, 'large') ?: '',
                'gallery'     => $gallery,
                'price'       => $meta['_sv_price'],
                'priceLabel'  => $meta['_sv_price_label'],
                'bedrooms'    => $meta['_sv_bedrooms'],
                'bathrooms'   => $meta['_sv_bathrooms'],
                'areaM2'      => $meta['_sv_area_m2'],
                'landAreaM2'  => $meta['_sv_land_area_m2'],
                'parking'     => $meta['_sv_parking'],
                'address'     => $meta['_sv_address'],
                'city'        => $meta['_sv_city'],
                'mapLat'      => $meta['_sv_map_lat'],
                'mapLng'      => $meta['_sv_map_lng'],
                'whatsapp'    => $meta['_sv_whatsapp'],
                'agentName'   => $meta['_sv_agent_name'],
                'featured'    => $meta['_sv_featured'] === '1',
                'hotDeal'     => $meta['_sv_hot_deal'] === '1',
                'amenities'   => $amenities,
                'yearBuilt'   => $meta['_sv_year_built'],
                'videoUrl'    => $meta['_sv_video_url'],
                'type'        => ! is_wp_error($types) && ! empty($types) ? $types[0]->name : '',
                'typeSlug'    => ! is_wp_error($types) && ! empty($types) ? $types[0]->slug : '',
                'status'      => ! is_wp_error($statuses) && ! empty($statuses) ? $statuses[0]->name : '',
                'statusSlug'  => ! is_wp_error($statuses) && ! empty($statuses) ? $statuses[0]->slug : '',
                'location'    => ! is_wp_error($locs) && ! empty($locs) ? $locs[0]->name : '',
                'date'        => get_the_date('', $p),
            ];
        }, $posts);
    }
}
