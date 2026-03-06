<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class PropertySearchPage extends Composer
{
    protected static $views = [
        'page-property-search',
    ];

    public function with(): array
    {
        $query = $this->buildQuery();

        return [
            'propertyQuery'   => $query,
            'properties'      => FrontPage::hydrateProperties($query->posts),
            'propertyTypes'   => $this->getTerms('property_type'),
            'propertyStatus'  => $this->getTerms('property_status'),
            'locations'       => $this->getTerms('property_location'),
            'currentFilters'  => $this->getCurrentFilters(),
            'totalFound'      => (int) $query->found_posts,
            'archiveTitle'    => __('Properties in El Salvador', 'sage'),
            'termDescription' => '',
            'formAction'      => sv_property_search_url(),
            'whatsappGlobal'  => get_option('sv_whatsapp_global', ''),
        ];
    }

    private function buildQuery(): \WP_Query
    {
        $paged   = max(1, (int) get_query_var('paged') ?: (int) ($_GET['paged'] ?? 1));
        $filters = $this->getCurrentFilters();

        $args = [
            'post_type'      => 'property',
            'post_status'    => 'publish',
            'posts_per_page' => 12,
            'paged'          => $paged,
        ];

        if ($filters['keyword']) {
            $args['s'] = $filters['keyword'];
        }

        $taxQuery = [];
        if ($filters['type']) {
            $taxQuery[] = ['taxonomy' => 'property_type', 'field' => 'slug', 'terms' => $filters['type']];
        }
        if ($filters['status']) {
            $taxQuery[] = ['taxonomy' => 'property_status', 'field' => 'slug', 'terms' => $filters['status']];
        }
        if ($filters['location']) {
            $taxQuery[] = ['taxonomy' => 'property_location', 'field' => 'slug', 'terms' => $filters['location']];
        }
        if (count($taxQuery) > 1) {
            $taxQuery['relation'] = 'AND';
        }
        if ($taxQuery) {
            $args['tax_query'] = $taxQuery;
        }

        $metaQuery = [];
        if ($filters['min'] !== '') {
            $metaQuery[] = ['key' => '_sv_price', 'value' => (int) $filters['min'], 'compare' => '>=', 'type' => 'NUMERIC'];
        }
        if ($filters['max'] !== '') {
            $metaQuery[] = ['key' => '_sv_price', 'value' => (int) $filters['max'], 'compare' => '<=', 'type' => 'NUMERIC'];
        }
        if ($filters['beds'] !== '') {
            $metaQuery[] = ['key' => '_sv_bedrooms', 'value' => (int) $filters['beds'], 'compare' => '>=', 'type' => 'NUMERIC'];
        }
        if (count($metaQuery) > 1) {
            $metaQuery['relation'] = 'AND';
        }
        if ($metaQuery) {
            $args['meta_query'] = $metaQuery;
        }

        $orderby = sanitize_text_field($_GET['orderby'] ?? '');
        if ($orderby === 'meta_value_num') {
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_sv_price';
            $args['order']    = strtoupper(sanitize_text_field($_GET['order'] ?? 'ASC'));
        }

        return new \WP_Query($args);
    }

    private function getTerms(string $taxonomy): array
    {
        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
        return is_wp_error($terms) ? [] : $terms;
    }

    private function getCurrentFilters(): array
    {
        return [
            'type'     => sanitize_text_field($_GET['property_type'] ?? ''),
            'status'   => sanitize_text_field($_GET['property_status'] ?? ''),
            'location' => sanitize_text_field($_GET['location'] ?? ''),
            'min'      => sanitize_text_field($_GET['min_price'] ?? ''),
            'max'      => sanitize_text_field($_GET['max_price'] ?? ''),
            'beds'     => sanitize_text_field($_GET['bedrooms'] ?? ''),
            'keyword'  => sanitize_text_field($_GET['keyword'] ?? ''),
        ];
    }
}
