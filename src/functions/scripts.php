<?php

function timberland_child_enqueue_scripts() {
    wp_enqueue_script(
        'child-script',
        get_stylesheet_directory_uri() . '/dist/scripts.min.js',
        array('script'),
        wp_get_theme()->get('Version'),
        true
    );
}

if ( !is_admin() ) {
    add_action( 'wp_enqueue_scripts', 'timberland_child_enqueue_scripts', 9999 );
}

function timberland_child_enqueue_admin_scripts() {

	//Enqueue Scripts

	wp_enqueue_script( 'child_admin_script', get_stylesheet_directory_uri() . '/dist/admin.min.js', array ( 'admin_script' ), wp_get_theme()->get( 'Version' ), true);

}

if ( is_admin() ) {
	add_action( 'admin_enqueue_scripts', 'timberland_child_enqueue_admin_scripts' );
}


/**
 * Replace child theme block view.js scripts with minified versions in production
 * This runs on every page load, so it doesn't require clearing caches
 */
add_action('wp_enqueue_scripts', function() {
	global $wp_scripts;

	if (is_admin()) {
		return;
	}

	// Only in production
	if (!defined('WP_DEBUG') || WP_DEBUG) {
		return;
	}

	// Only proceed if parent theme minification function exists
	if (!function_exists('timberland_generate_minified_js')) {
		return;
	}

	// Get all registered scripts
	foreach ($wp_scripts->registered as $handle => $script) {
		// Check if this is an ACF block script
		if (strpos($handle, 'acf-') === 0 && isset($script->src)) {
			$src = $script->src;

			// Only handle child theme block scripts
			$child_blocks_path = get_stylesheet_directory_uri() . '/src/templates/blocks/';
			if (strpos($src, $child_blocks_path) === 0) {
				// Extract block name and file type
				$relative_path = str_replace($child_blocks_path, '', $src);
				$parts = explode('/', $relative_path);
				if (count($parts) >= 2) {
					$block_name = $parts[0];
					$file_name = $parts[count($parts) - 1];
					$file_type = pathinfo($file_name, PATHINFO_FILENAME);

					// Check if source file exists
					$source_file = get_stylesheet_directory() . '/src/templates/blocks/' . $block_name . '/' . $file_name;
					if (file_exists($source_file)) {
						// Generate minified version using parent theme function
						$min_path = timberland_generate_minified_js($source_file, $block_name, $file_type, true);
						if ($min_path) {
							// Update the script src to point to minified version
							$relative_min_path = str_replace(get_stylesheet_directory(), '', $min_path);
							$wp_scripts->registered[$handle]->src = get_stylesheet_directory_uri() . $relative_min_path;
						}
					}
				}
			}
		}
	}
}, 5); // Run early, before scripts are printed

/**
 * Manual enqueue for script.js files (ACF doesn't auto-enqueue the "script" property)
 * Also handles minification in production
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
	$blocks_metadata = timberland_child_get_blocks_metadata();
	$used_blocks = timberland_child_get_post_used_blocks($post_id, $blocks_metadata);
	$blocks_path = get_stylesheet_directory() . '/src/templates/blocks';

	// Only enqueue scripts for blocks actually used on this page
	foreach ($used_blocks as $block_slug) {
		$script_path = $blocks_path . '/' . $block_slug . '/script.js';

		if (file_exists($script_path) && filesize($script_path) > 0) { // Only enqueue non-empty files
			$enqueue_url = get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block_slug . '/script.js';

			// In production, use minified version from parent theme helper
			if (!defined('WP_DEBUG') || !WP_DEBUG) {
				// Use parent theme's minification function
				if (function_exists('timberland_generate_minified_js')) {
					$min_path = timberland_generate_minified_js($script_path, $block_slug, 'script', true);
					if ($min_path) {
						$enqueue_url = get_stylesheet_directory_uri() . str_replace(get_stylesheet_directory(), '', $min_path);
					}
				}
			}

			wp_enqueue_script(
				'child_block_script_' . $block_slug,
				$enqueue_url,
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
function timberland_enqueue_child_block_admin_scripts() {
	$blocks_path = get_stylesheet_directory() . '/src/templates/blocks';
	$blocks = array_filter(scandir($blocks_path), 'timberland_child_filter_block_dir'); // Helper function from block-helpers.php

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
add_action('enqueue_block_editor_assets', 'timberland_enqueue_child_block_admin_scripts');
