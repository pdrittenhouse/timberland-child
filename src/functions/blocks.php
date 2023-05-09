<?php

function dream_child_block_categories( $categories ) {

    $categories[] = array(
        'slug'  => 'timberland-child',
        'title' => __('Timberland Child', 'timberland-child'),
    );

    return $categories;
}

if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
    add_filter( 'block_categories_all', 'dream_child_block_categories' );
} else {
    add_filter( 'block_categories', 'dream_child_block_categories' );
}