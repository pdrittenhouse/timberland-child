<?php

/**
 * Modify the document title for the search page
 * @link: https://wordpress.stackexchange.com/questions/225724/how-to-customize-search-result-page-title
 */
add_filter('document_title_parts', function ($title) {
    if (is_search())
        $title['title'] = sprintf(
            esc_html__('Search results page'),
            get_search_query()
        );

    return $title;
});
