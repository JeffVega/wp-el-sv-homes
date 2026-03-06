<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Location extends Composer
{
    protected static $views = ['single-location'];

    public function with(): array
    {
        $termSlug  = get_post_meta(get_the_ID(), '_sv_location_term_slug', true) ?: '';
        $ptypeSlug = get_query_var('sv_ptype', '');
        $filters   = $this->getCurrentFilters();
        $query     = $this->buildPropertyQuery($termSlug, $ptypeSlug, $filters);

        $activePropertyType = $ptypeSlug
            ? get_term_by('slug', $ptypeSlug, 'property_type')
            : null;

        $formAction = (string) get_permalink();
        if ($activePropertyType) {
            $formAction = trailingslashit($formAction) . $activePropertyType->slug . '/';
        }

        return [
            'termSlug'           => $termSlug,
            'activePropertyType' => $activePropertyType ?: null,
            'properties'         => FrontPage::hydrateProperties($query->posts),
            'totalFound'         => (int) $query->found_posts,
            'maxPages'           => (int) $query->max_num_pages,
            'propertyTypes'      => $this->getTerms('property_type'),
            'propertyStatus'     => $this->getTerms('property_status'),
            'currentFilters'     => $filters,
            'formAction'         => $formAction,
            'whatsappGlobal'     => get_option('sv_whatsapp_global', ''),
        ];
    }

    private function buildPropertyQuery(string $termSlug, string $ptypeSlug, array $filters): \WP_Query
    {
        if (! $termSlug) {
            return new \WP_Query(['post_type' => 'property', 'posts_per_page' => 0]);
        }

        $rawPaged = get_query_var('paged') ?: get_query_var('page');
        $paged    = max(1, (int) $rawPaged);

        $args = [
            'post_type'      => 'property',
            'posts_per_page' => 12,
            'paged'          => $paged,
            'tax_query'      => [
                ['taxonomy' => 'property_location', 'field' => 'slug', 'terms' => $termSlug],
            ],
        ];

        if ($ptypeSlug) {
            $args['tax_query'][]         = ['taxonomy' => 'property_type', 'field' => 'slug', 'terms' => $ptypeSlug];
            $args['tax_query']['relation'] = 'AND';
        }

        // Orderby
        $orderby = sanitize_key($_GET['orderby'] ?? '');
        if ($orderby === 'meta_value_num') {
            $args['orderby']  = 'meta_value_num';
            $args['meta_key'] = '_sv_price';
            $args['order']    = strtoupper(sanitize_key($_GET['order'] ?? 'ASC')) === 'DESC' ? 'DESC' : 'ASC';
        }

        // Meta filters
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
        if (! empty($metaQuery)) {
            $metaQuery['relation'] = 'AND';
            $args['meta_query']    = $metaQuery;
        }

        // Additional taxonomy filters from query params
        if (! $ptypeSlug && $filters['type']) {
            $args['tax_query'][]         = ['taxonomy' => 'property_type', 'field' => 'slug', 'terms' => $filters['type']];
            $args['tax_query']['relation'] = 'AND';
        }
        if ($filters['status']) {
            $args['tax_query'][]         = ['taxonomy' => 'property_status', 'field' => 'slug', 'terms' => $filters['status']];
            $args['tax_query']['relation'] = 'AND';
        }

        if ($filters['keyword']) {
            $args['s'] = $filters['keyword'];
        }

        return new \WP_Query($args);
    }

    private function getCurrentFilters(): array
    {
        return [
            'type'    => sanitize_text_field($_GET['property_type'] ?? ''),
            'status'  => sanitize_text_field($_GET['property_status'] ?? ''),
            'min'     => sanitize_text_field($_GET['min_price'] ?? ''),
            'max'     => sanitize_text_field($_GET['max_price'] ?? ''),
            'beds'    => sanitize_text_field($_GET['bedrooms'] ?? ''),
            'keyword' => sanitize_text_field($_GET['keyword'] ?? ''),
        ];
    }

    private function getTerms(string $taxonomy): array
    {
        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
        return is_wp_error($terms) ? [] : $terms;
    }
}
