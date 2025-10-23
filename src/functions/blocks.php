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

/**
 * Register child theme blocks
 */
add_action( 'init', 'dream_child_register_blocks', 5 );
function dream_child_register_blocks() {
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
 */
add_action( 'init', 'dream_child_include_block_php_files', 10 );
function dream_child_include_block_php_files() {
  $blocks_path = dirname(__DIR__) . '/templates/blocks';

  if ( !is_dir($blocks_path) ) {
    return;
  }

  $blocks = array_filter(scandir($blocks_path), 'filter_block_dir'); // Helper function from block-helpers.php

  foreach ($blocks as $block) {
    $block_php_file = $blocks_path . '/' . $block . '/block.php';
    if ( file_exists( $block_php_file ) ) {
      require_once $block_php_file;
    }
  }
}