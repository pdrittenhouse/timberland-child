<?php
/**
 * Child Theme Block Registration
 *
 * Note: Child theme block.php files for block OVERRIDES (blocks without their own block.json)
 * are loaded by the parent theme's timberland_include_block_php_files() function.
 * This file only handles:
 * - Registering the child theme block category
 * - Registering NEW child theme blocks (those with their own block.json)
 */

/**
 * Add child theme block category
 */
function timberland_child_block_categories( $categories ) {
    $categories[] = array(
        'slug'  => 'timberland-child',
        'title' => __('Timberland Child', 'timberland-child'),
    );

    return $categories;
}

if ( version_compare( get_bloginfo( 'version' ), '5.8', '>=' ) ) {
    add_filter( 'block_categories_all', 'timberland_child_block_categories' );
} else {
    add_filter( 'block_categories', 'timberland_child_block_categories' );
}

/**
 * Register NEW child theme blocks (those with their own block.json)
 * Block overrides (without block.json) don't need registration - they use the parent's block
 */
add_action( 'init', 'timberland_child_register_blocks', 5 );
function timberland_child_register_blocks() {
    $blocks_path = dirname(__DIR__) . '/templates/blocks';

    // Return early if blocks directory doesn't exist
    if ( !is_dir($blocks_path) ) {
        return;
    }

    $blocks = array_filter(scandir($blocks_path), 'filter_block_dir');

    foreach ($blocks as $block) {
        if ( file_exists( $blocks_path . '/' . $block . '/block.json' ) ) {
            register_block_type($blocks_path . '/' . $block);
        }
    }
}