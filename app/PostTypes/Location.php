<?php

namespace App\PostTypes;

class Location
{
    public static function register(): void
    {
        register_post_type('location', [
            'labels' => [
                'name'               => __('Locations', 'sage'),
                'singular_name'      => __('Location', 'sage'),
                'add_new'            => __('Add New Location', 'sage'),
                'add_new_item'       => __('Add New Location', 'sage'),
                'edit_item'          => __('Edit Location', 'sage'),
                'new_item'           => __('New Location', 'sage'),
                'view_item'          => __('View Location', 'sage'),
                'search_items'       => __('Search Locations', 'sage'),
                'not_found'          => __('No locations found', 'sage'),
                'not_found_in_trash' => __('No locations found in Trash', 'sage'),
                'menu_name'          => __('Locations', 'sage'),
            ],
            'public'          => true,
            'hierarchical'    => true,
            'has_archive'     => false,
            'rewrite'         => ['slug' => 'locations', 'with_front' => false],
            'supports'        => ['title', 'editor', 'thumbnail', 'excerpt', 'page-attributes'],
            'menu_icon'       => 'dashicons-location',
            'menu_position'   => 6,
            'show_in_rest'    => true,
            'capability_type' => 'post',
        ]);
    }
}
