<?php

/**
 * Returns the URL of the Property Search page.
 *
 * Resolves the page by its slug so the URL is always correct regardless
 * of the site's domain or permalink structure. Falls back gracefully if
 * the page has not been created yet.
 *
 * @param  array<string,string> $params  Optional query string parameters.
 */
function sv_property_search_url(array $params = []): string
{
    static $base = null;

    if ($base === null) {
        $page = get_page_by_path('property-search');
        $base = $page ? (string) get_permalink($page) : home_url('/property-search/');
    }

    return $params ? add_query_arg($params, $base) : $base;
}
