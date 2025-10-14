<?php

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
 * Block Scripts
 * @link https://jasonyingling.me/enqueueing-scripts-and-styles-for-gutenberg-blocks/
 */

/**
 * Block Scripts - Use the same detection from styles.php (blocks detected on 'wp' hook)
 * We don't re-detect here, we rely on the cache being populated by styles.php
 */
add_action('enqueue_block_assets', function() {
	// Only on frontend
	if (is_admin()) {
		return;
	}

	// Only on singular pages
	if (!is_singular()) {
		return;
	}

	$post_id = get_the_ID();
	$blocks_metadata = dream_child_get_blocks_metadata(); // Helper function from block-helpers.php
	$used_blocks = dream_child_get_post_used_blocks($post_id, $blocks_metadata); // Will use cached value from 'wp' hook
	$blocks_path = get_stylesheet_directory() . '/src/templates/blocks';

	// Only enqueue scripts for blocks actually used on this page
	foreach ($used_blocks as $block_slug) {
		$script_path = $blocks_path . '/' . $block_slug . '/script.js';

		if (file_exists($script_path)) {
			wp_enqueue_script(
				'child_block_script_' . $block_slug,
				get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block_slug . '/script.js',
				array('jquery', 'acf-input'),
				wp_get_theme()->get('Version'),
				true
			);
		}
	}
});


// Admin editor: Load block admin scripts (optimized - only load non-empty files)
// Note: block.json references assets, but WordPress won't auto-enqueue empty files
// So we handle enqueuing here with content check to skip empty/whitespace-only files
function dream_enqueue_child_block_admin_scripts() {
	$blocks_path = get_stylesheet_directory() . '/src/templates/blocks';
	$blocks = array_filter(scandir($blocks_path), 'dream_child_filter_block_dir'); // Helper function from block-helpers.php

	foreach ($blocks as $block) {
		$index_js_path = $blocks_path . '/' . $block . '/index.js';

		// Only enqueue if file exists AND has actual content (not just whitespace)
		if (file_exists($index_js_path)) {
			$content = file_get_contents($index_js_path);
			if (!empty(trim($content))) {
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
}
add_action('enqueue_block_editor_assets', 'dream_enqueue_child_block_admin_scripts');
