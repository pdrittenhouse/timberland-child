<?php
/**
 * Child Theme Block Registration
 *
 * Block.php loading is split between parent and child themes:
 * - Block OVERRIDES (without block.json): Loaded by parent theme's timberland_include_block_php_files()
 * - Child-SPECIFIC blocks (with block.json): Loaded by timberland_child_include_block_php_files() below
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

    if ( !is_dir($blocks_path) ) {
        return;
    }

    $blocks = array_filter(scandir($blocks_path), 'timberland_child_filter_block_dir');

    foreach ($blocks as $block) {
        if ( file_exists( $blocks_path . '/' . $block . '/block.json' ) ) {
            register_block_type($blocks_path . '/' . $block);
        }
    }
}

/**
 * Include block.php files for child-SPECIFIC blocks (those with their own block.json)
 * Block overrides are handled by the parent theme's timberland_include_block_php_files()
 */
function timberland_child_include_block_php_files() {
    $blocks_path = dirname(__DIR__) . '/templates/blocks';

    if ( !is_dir($blocks_path) ) {
        return;
    }

    $blocks = array_filter(scandir($blocks_path), 'timberland_child_filter_block_dir');

    foreach ($blocks as $block) {
        // Only load block.php for blocks that have their own block.json (child-specific blocks)
        // Blocks without block.json are overrides and handled by the parent theme
        if ( file_exists( $blocks_path . '/' . $block . '/block.json' ) ) {
            $block_php_file = $blocks_path . '/' . $block . '/block.php';
            if ( file_exists( $block_php_file ) ) {
                require_once $block_php_file;
            }
        }
    }
}

// Load child-specific block.php files
add_action( 'init', function() {
    if ( wp_doing_ajax() || is_admin() ) {
        timberland_child_include_block_php_files();
    }
}, 10 );

add_action( 'wp', function() {
    if ( ! wp_doing_ajax() && ! is_admin() ) {
        timberland_child_include_block_php_files();
    }
}, 10 );