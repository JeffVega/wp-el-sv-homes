<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class PropertyArchive extends Composer
{
    protected static $views = [
        'archive-property',
        'taxonomy-property_location',
        'taxonomy-property_type',
        'taxonomy-property_status',
    ];

    public function with(): array
    {
        return [
            'properties'     => $this->getProperties(),
            'propertyTypes'  => $this->getTerms('property_type'),
            'propertyStatus' => $this->getTerms('property_status'),
            'locations'      => $this->getTerms('property_location'),
            'currentFilters' => $this->getCurrentFilters(),
            'totalFound'     => $this->getTotalFound(),
            'archiveTitle'   => $this->getArchiveTitle(),
            'termDescription' => $this->getTermDescription(),
            'formAction'      => $this->getFormAction(),
            'whatsappGlobal' => get_option('sv_whatsapp_global', ''),
        ];
    }

    private function getArchiveTitle(): string
    {
        if (is_tax('property_location')) {
            $term = get_queried_object();
            return $term ? sprintf(__('Properties in %s', 'sage'), $term->name)
                         : __('Properties in El Salvador', 'sage');
        }
        if (is_tax('property_type')) {
            $term = get_queried_object();
            return $term ? sprintf(__('%s Properties in El Salvador', 'sage'), $term->name)
                         : __('Properties in El Salvador', 'sage');
        }
        if (is_tax('property_status')) {
            $term = get_queried_object();
            return $term ? sprintf(__('Properties %s in El Salvador', 'sage'), $term->name)
                         : __('Properties in El Salvador', 'sage');
        }
        return __('Properties in El Salvador', 'sage');
    }

    private function getTermDescription(): string
    {
        if (is_tax(['property_location', 'property_type', 'property_status'])) {
            $term = get_queried_object();
            if ($term && !empty($term->description)) {
                return wp_kses_post($term->description);
            }
        }
        return '';
    }

    private function getFormAction(): string
    {
        if (is_tax(['property_location', 'property_type', 'property_status'])) {
            $term = get_queried_object();
            if ($term) {
                $link = get_term_link($term);
                if (!is_wp_error($link)) return $link;
            }
        }
        return sv_property_search_url();
    }

    private function getProperties(): array
    {
        global $wp_query;
        $posts = $wp_query->posts ?? [];
        return FrontPage::hydrateProperties($posts);
    }

    private function getTotalFound(): int
    {
        global $wp_query;
        return (int) ($wp_query->found_posts ?? 0);
    }

    private function getTerms(string $taxonomy): array
    {
        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
        if (is_wp_error($terms)) {
            return [];
        }
        return $terms;
    }

    private function getCurrentFilters(): array
    {
        $filters = [
            'type'     => sanitize_text_field($_GET['property_type'] ?? ''),
            'status'   => sanitize_text_field($_GET['property_status'] ?? ''),
            'location' => sanitize_text_field($_GET['location'] ?? ''),
            'min'      => sanitize_text_field($_GET['min_price'] ?? ''),
            'max'      => sanitize_text_field($_GET['max_price'] ?? ''),
            'beds'     => sanitize_text_field($_GET['bedrooms'] ?? ''),
            'keyword'  => sanitize_text_field($_GET['keyword'] ?? ''),
        ];
        // Pre-fill filters to reflect the current taxonomy page.
        if (is_tax('property_location')) {
            $term = get_queried_object();
            if ($term && isset($term->slug)) {
                $filters['location'] = $term->slug;
            }
        }
        if (is_tax('property_type')) {
            $term = get_queried_object();
            if ($term && isset($term->slug)) {
                $filters['type'] = $term->slug;
            }
        }
        if (is_tax('property_status')) {
            $term = get_queried_object();
            if ($term && isset($term->slug)) {
                $filters['status'] = $term->slug;
            }
        }
        return $filters;
    }
}
