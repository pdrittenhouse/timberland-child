<?php

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
 * Register child theme blocks
 */
add_action( 'init', 'timberland_child_register_blocks', 5 );
function timberland_child_register_blocks() {
  $blocks_path = dirname(__DIR__) . '/templates/blocks';

  // Return early if blocks directory doesn't exist
  if ( !is_dir($blocks_path) ) {
    return;
  }

  $blocks = array_filter(scandir($blocks_path), 'filter_block_dir'); // Helper function from block-helpers.php

  foreach ($blocks as $block) {
    if ( file_exists( $blocks_path . '/' . $block . '/block.json' ) ) {
      register_block_type($blocks_path . '/' . $block);
    }
  }
}

/**
 * Include block-specific PHP files from child theme blocks
 * Conditionally loads only the block.php files for blocks used on the current page
 * Always loads all blocks for AJAX requests and archives
 */
function timberland_child_include_block_php_files() {
  $blocks_path = dirname(__DIR__) . '/templates/blocks';

  if ( !is_dir($blocks_path) ) {
    return;
  }

  // Always load all blocks on AJAX requests (we don't know what will be rendered)
  // Also load all on admin requests
  if ( wp_doing_ajax() || is_admin() ) {
    $blocks = array_filter(scandir($blocks_path), 'filter_block_dir');
    foreach ($blocks as $block) {
      $block_php_file = $blocks_path . '/' . $block . '/block.php';
      if ( file_exists( $block_php_file ) ) {
        require_once $block_php_file;
      }
    }
    return;
  }

  // On singular posts, only load block.php for blocks actually used on the page
  if ( is_singular() ) {
    $post_id = get_the_ID();

    // Build child theme blocks metadata
    $child_blocks_metadata = [];
    $blocks = array_filter(scandir($blocks_path), 'filter_block_dir');

    foreach ($blocks as $block) {
      $block_json_path = $blocks_path . '/' . $block . '/block.json';
      if (file_exists($block_json_path)) {
        $block_json_content = file_get_contents($block_json_path);
        $block_data = json_decode($block_json_content, true);
        if (isset($block_data['name'])) {
          $child_blocks_metadata[$block] = $block_data['name'];
        }
      }
    }

    // Get blocks used on this post (use child theme function for child theme blocks)
    $used_blocks = timberland_child_get_post_used_blocks($post_id, $child_blocks_metadata);

    // Only include block.php for blocks used on this page
    foreach ($used_blocks as $block_slug) {
      $block_php_file = $blocks_path . '/' . $block_slug . '/block.php';
      if ( file_exists( $block_php_file ) ) {
        require_once $block_php_file;
      }
    }
  } else {
    // On archives, search, etc., load all block.php files as we can't easily detect usage
    $blocks = array_filter(scandir($blocks_path), 'filter_block_dir');
    foreach ($blocks as $block) {
      $block_php_file = $blocks_path . '/' . $block . '/block.php';
      if ( file_exists( $block_php_file ) ) {
        require_once $block_php_file;
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