<?php

//function dream_child_enqueue_scripts() {
//    wp_enqueue_script( 'child-script',
//        get_stylesheet_directory_uri() . '/dist/scripts.min.js',
//        array ( 'script' ),
//        wp_get_theme()->get( 'Version' ),
//    true
//    );
//}
//if ( !is_admin() ) {
//    add_action( 'wp_enqueue_scripts', 'dream_child_enqueue_scripts' );
//}

function dream_child_enqueue_scripts() {
    wp_enqueue_script(
        'child-script',
        get_stylesheet_directory_uri() . '/dist/scripts.min.js',
        array('script'),
        wp_get_theme()->get('Version'),
        true
    );
}
if ( !is_admin() ) {
    add_action( 'wp_enqueue_scripts', 'dream_child_enqueue_scripts', 9999 );
}

function dream_child_enqueue_admin_scripts() {

	//Enqueue Scripts

	wp_enqueue_script( 'child_admin_script', get_stylesheet_directory_uri() . '/dist/admin.min.js', array ( 'admin_script' ), wp_get_theme()->get( 'Version' ), true);

}

if ( is_admin() ) {
	add_action( 'admin_enqueue_scripts', 'dream_child_enqueue_admin_scripts' );
}


/**
 * Block Detection Caching Helper for Child Theme
 */

/**
 * Get cached block metadata for child theme
 * Returns array of ['block-slug' => 'acf/block-name', ...]
 */
function dream_get_child_blocks_metadata() {
	$blocks_metadata = get_transient('dream_child_blocks_metadata');

	if (false === $blocks_metadata) {
		$blocks_metadata = [];
		$child_theme_blocks_path = dirname(__DIR__, 2) . '/src/templates/blocks';

		if (file_exists($child_theme_blocks_path)) {
			$child_theme_blocks = array_filter(scandir($child_theme_blocks_path), 'filter_block_dir');

			foreach ($child_theme_blocks as $block) {
				$block_json_path = $child_theme_blocks_path . '/' . $block . '/block.json';

				if (file_exists($block_json_path)) {
					$block_json_content = file_get_contents($block_json_path);
					$block_data = json_decode($block_json_content, true);

					if (isset($block_data['name'])) {
						$blocks_metadata[$block] = $block_data['name'];
					}
				}
			}
		}

		// Cache for 1 week
		set_transient('dream_child_blocks_metadata', $blocks_metadata, WEEK_IN_SECONDS);
	}

	return $blocks_metadata;
}

/**
 * Child Block Scripts
 * @link https://jasonyingling.me/enqueueing-scripts-and-styles-for-gutenberg-blocks/
 */

function dream_enqueue_child_block_scripts() {
	if (get_post() === null) {
		return;
	}

	$post_id = get_the_ID();
	$child_blocks_metadata = dream_get_child_blocks_metadata();
	$used_blocks = dream_get_post_used_blocks($post_id, $child_blocks_metadata);
	$child_theme_blocks_path = dirname(__DIR__, 2) . '/src/templates/blocks';

	// Only enqueue scripts for child theme blocks actually used on this page
	foreach ($used_blocks as $block_slug) {
		$script_path = $child_theme_blocks_path . '/' . $block_slug . '/script.js';

		if (file_exists($script_path)) {
			wp_enqueue_script(
				'child_blocks_script_' . $block_slug,
				get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block_slug . '/script.js',
				array('jquery', 'acf-input'),
				wp_get_theme()->get('Version'),
				true
			);
		}
	}
}
add_action('enqueue_block_assets', 'dream_enqueue_child_block_scripts');

//function dream_enqueue_child_block_scripts() {
//    $child_theme_blocks_path = dirname(__DIR__, 2) . '/src/templates/blocks';
//    $child_theme_blocks = array_filter(scandir($child_theme_blocks_path), 'filter_block_dir');
//    foreach ($child_theme_blocks as $block) {
//        if ( file_exists( $child_theme_blocks_path . '/' . $block . '/script.js' ) ) {
//            wp_enqueue_script('child_blocks_script_' . $block, get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block . '/script.js', array ( 'jquery', 'acf-input' ), wp_get_theme()->get( 'Version' ), 'all');
//        }
//    }
//}
//add_action( 'enqueue_block_assets', 'dream_enqueue_child_block_scripts' );


// Admin editor: Load all child block admin scripts (no caching needed - editor needs all blocks available)
function dream_enqueue_child_block_admin_scripts() {
	$child_theme_blocks_path = dirname(__DIR__, 2) . '/src/templates/blocks';
	$child_theme_blocks = array_filter(scandir($child_theme_blocks_path), 'filter_block_dir');

	foreach ($child_theme_blocks as $block) {
		if (file_exists($child_theme_blocks_path . '/' . $block . '/index.js')) {
			wp_enqueue_script(
				'child_block_admin_script_' . $block,
				get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block . '/index.js',
				array('jquery', 'acf-input'),
				wp_get_theme()->get('Version'),
				true
			);
		}
	}
}
add_action('enqueue_block_editor_assets', 'dream_enqueue_child_block_admin_scripts');

//function dream_enqueue_child_block_admin_scripts() {
//    $child_theme_blocks_path = dirname(__DIR__, 2) . '/src/templates/blocks';
//    $child_theme_blocks = array_filter(scandir($child_theme_blocks_path), 'filter_block_dir');
//    foreach ($child_theme_blocks as $block) {
//        if ( file_exists( $child_theme_blocks_path . '/' . $block . '/index.js' ) ) {
//            wp_enqueue_script('child_blocks_admin_script_' . $block, get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block . '/index.js', array ( 'jquery', 'acf-input' ), wp_get_theme()->get( 'Version' ), 'all');
//        }
//    }
//}
//add_action( 'enqueue_block_editor_assets', 'dream_enqueue_child_block_admin_scripts' );
