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
 * Conditionally loads only the block.php files for blocks used on the current page
 * Always loads all blocks for AJAX requests and archives
 *
 * Block overrides (without block.json) are handled by the parent theme's timberland_include_block_php_files()
 */
function timberland_child_include_block_php_files() {
    $blocks_path = dirname(__DIR__) . '/templates/blocks';

    if ( !is_dir($blocks_path) ) {
        return;
    }

    // Always load all blocks on AJAX requests (we don't know what will be rendered)
    // Also load all on admin requests
    if ( wp_doing_ajax() || is_admin() ) {
        $blocks = array_filter(scandir($blocks_path), 'timberland_child_filter_block_dir');
        foreach ($blocks as $block) {
            // Only load block.php for blocks that have their own block.json (child-specific blocks)
            if ( file_exists( $blocks_path . '/' . $block . '/block.json' ) ) {
                $block_php_file = $blocks_path . '/' . $block . '/block.php';
                if ( file_exists( $block_php_file ) ) {
                    require_once $block_php_file;
                }
            }
        }
        return;
    }

    // On singular posts, only load block.php for blocks actually used on the page
    if ( is_singular() ) {
        $post_id = get_the_ID();
        $blocks_metadata = timberland_child_get_blocks_metadata();
        $used_blocks = timberland_child_get_post_used_blocks($post_id, $blocks_metadata);

        // Only include block.php for blocks used on this page
        foreach ($used_blocks as $block_slug) {
            $block_php_file = $blocks_path . '/' . $block_slug . '/block.php';
            if ( file_exists( $block_php_file ) ) {
                require_once $block_php_file;
            }
        }
    } else {
        // On archives, search, etc., load all block.php files as we can't easily detect usage
        $blocks = array_filter(scandir($blocks_path), 'timberland_child_filter_block_dir');
        foreach ($blocks as $block) {
            // Only load block.php for blocks that have their own block.json (child-specific blocks)
            if ( file_exists( $blocks_path . '/' . $block . '/block.json' ) ) {
                $block_php_file = $blocks_path . '/' . $block . '/block.php';
                if ( file_exists( $block_php_file ) ) {
                    require_once $block_php_file;
                }
            }
        }
    }
}

// Hook to 'init' for AJAX and admin, 'wp' for frontend
// This ensures block.php files are loaded before AJAX handlers run
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