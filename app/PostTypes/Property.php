<?php

namespace App\PostTypes;

class Property
{
    public static function register(): void
    {
        self::registerPostType();
        self::registerTaxonomies();
    }

    private static function registerPostType(): void
    {
        register_post_type('property', [
            'labels' => [
                'name'               => __('Properties', 'sage'),
                'singular_name'      => __('Property', 'sage'),
                'add_new'            => __('Add New Property', 'sage'),
                'add_new_item'       => __('Add New Property', 'sage'),
                'edit_item'          => __('Edit Property', 'sage'),
                'new_item'           => __('New Property', 'sage'),
                'view_item'          => __('View Property', 'sage'),
                'search_items'       => __('Search Properties', 'sage'),
                'not_found'          => __('No properties found', 'sage'),
                'not_found_in_trash' => __('No properties found in Trash', 'sage'),
                'menu_name'          => __('Properties', 'sage'),
            ],
            'public'             => true,
            'has_archive'        => 'properties',
            'rewrite'            => ['slug' => 'property', 'with_front' => false],
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt'],
            'menu_icon'          => 'dashicons-building',
            'menu_position'      => 5,
            'show_in_rest'       => true,
            'capability_type'    => 'post',
        ]);
    }

    private static function registerTaxonomies(): void
    {
        // Property Type
        register_taxonomy('property_type', 'property', [
            'labels' => [
                'name'          => __('Property Types', 'sage'),
                'singular_name' => __('Property Type', 'sage'),
                'add_new_item'  => __('Add New Type', 'sage'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'property-type'],
        ]);

        // Property Status
        register_taxonomy('property_status', 'property', [
            'labels' => [
                'name'          => __('Listing Status', 'sage'),
                'singular_name' => __('Status', 'sage'),
                'add_new_item'  => __('Add New Status', 'sage'),
            ],
            'hierarchical'      => true,
            'public'            => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'property-status'],
        ]);

        // Location / Department — public for admin UI and filtering, but
        // archive URLs are disabled to avoid competing with the Location CPT pages.
        register_taxonomy('property_location', 'property', [
            'labels' => [
                'name'          => __('Locations', 'sage'),
                'singular_name' => __('Location', 'sage'),
                'add_new_item'  => __('Add New Location', 'sage'),
            ],
            'hierarchical'       => true,
            'public'             => true,
            'publicly_queryable' => false,
            'show_in_rest'       => true,
            'rewrite'            => false,
        ]);
    }

    /**
     * El Salvador departments for seeding the location taxonomy.
     */
    public static function departments(): array
    {
        return [
            'Ahuachapán', 'Santa Ana', 'Sonsonate',
            'Chalatenango', 'La Libertad', 'San Salvador',
            'Cuscatlán', 'La Paz', 'Cabañas',
            'San Vicente', 'Usulután', 'San Miguel',
            'Morazán', 'La Unión',
        ];
    }
}
