<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class PropertyArchive extends Composer
{
    protected static $views = ['archive-property'];

    public function with(): array
    {
        return [
            'properties'     => $this->getProperties(),
            'propertyTypes'  => $this->getTerms('property_type'),
            'propertyStatus' => $this->getTerms('property_status'),
            'locations'      => $this->getTerms('property_location'),
            'currentFilters' => $this->getCurrentFilters(),
            'totalFound'     => $this->getTotalFound(),
            'whatsappGlobal' => get_option('sv_whatsapp_global', ''),
        ];
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
