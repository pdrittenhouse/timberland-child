<?php

function dream_child_enqueue_styles() {
    wp_enqueue_style( 'child_styles',
        get_stylesheet_directory_uri() . '/dist/styles.css',
        array( 'styles' ),
        wp_get_theme()->get( 'Version' ) // This only works if you have Version defined in the style header.
    );

	// Get cached child customizer CSS
	// NOTE: Uses parent theme's customizer functions with child theme's CSS variable overrides
	// This is NOT duplication - child CSS variables in _css-variables.scss override parent values
	$cached_child_customizer_css = get_transient( 'dream_child_customizer_css' );

	if ( false === $cached_child_customizer_css ) {
		// Generate all child customizer CSS
		$cached_child_customizer_css = '';
		$cached_child_customizer_css .= theme_get_customizer_colors();
		$cached_child_customizer_css .= theme_get_customizer_buttons();
		$cached_child_customizer_css .= theme_get_customizer_global();
		$cached_child_customizer_css .= theme_get_customizer_forms();
		$cached_child_customizer_css .= theme_get_customizer_alerts();
		$cached_child_customizer_css .= theme_get_customizer_cards();
		$cached_child_customizer_css .= theme_get_customizer_modals();
		$cached_child_customizer_css .= theme_get_customizer_social_navs();
		$cached_child_customizer_css .= theme_get_customizer_header();
		$cached_child_customizer_css .= theme_get_customizer_header_branding();
		$cached_child_customizer_css .= theme_get_customizer_navbar();
		$cached_child_customizer_css .= theme_get_customizer_primary_nav();
		$cached_child_customizer_css .= theme_get_customizer_secondary_nav();
		$cached_child_customizer_css .= theme_get_customizer_header_cta();
		$cached_child_customizer_css .= theme_get_customizer_header_cta_mobile();
		$cached_child_customizer_css .= theme_get_customizer_header_search();
		$cached_child_customizer_css .= theme_get_customizer_header_social_nav();
		$cached_child_customizer_css .= theme_get_customizer_footer();
		$cached_child_customizer_css .= theme_get_customizer_footer_branding();
		$cached_child_customizer_css .= theme_get_customizer_footer_cta();
		$cached_child_customizer_css .= theme_get_customizer_footer_nav();
		$cached_child_customizer_css .= theme_get_customizer_utility_nav();
		$cached_child_customizer_css .= theme_get_customizer_footer_social_nav();
		$cached_child_customizer_css .= theme_get_customizer_footer_search();
		$cached_child_customizer_css .= theme_get_customizer_footer_contact_info();
		$cached_child_customizer_css .= theme_get_customizer_footer_disclaimer();
		$cached_child_customizer_css .= theme_get_customizer_footer_attribution();
		$cached_child_customizer_css .= theme_get_customizer_footer_copyright();

		// Cache for 1 week (or until customizer is saved)
		set_transient( 'dream_child_customizer_css', $cached_child_customizer_css, WEEK_IN_SECONDS );
	}

	// Add the cached CSS
	wp_add_inline_style( 'child_styles', $cached_child_customizer_css );
}
if ( !is_admin() ) {
	add_action( 'wp_enqueue_scripts', 'dream_child_enqueue_styles' );
}


function dream_child_enqueue_admin_styles() {

	// Enqueue Styles
	wp_enqueue_style( 'child_admin_styles', get_stylesheet_directory_uri() . '/dist/admin.css', array('admin_styles'), wp_get_theme()->get( 'Version' ), 'all' );

	// Get cached admin child customizer CSS
	$cached_child_admin_customizer_css = get_transient( 'dream_child_admin_customizer_css' );

	if ( false === $cached_child_admin_customizer_css ) {
		// Generate all admin child customizer CSS
		$cached_child_admin_customizer_css = '';
		$cached_child_admin_customizer_css .= theme_get_customizer_colors();
		$cached_child_admin_customizer_css .= theme_get_customizer_buttons();
		$cached_child_admin_customizer_css .= theme_get_customizer_global();
		$cached_child_admin_customizer_css .= theme_get_customizer_forms();
		// $cached_child_admin_customizer_css .= theme_get_customizer_alerts();
		$cached_child_admin_customizer_css .= theme_get_customizer_cards();
		$cached_child_admin_customizer_css .= theme_get_customizer_modals();
		$cached_child_admin_customizer_css .= theme_get_customizer_social_navs();
		// $cached_child_admin_customizer_css .= theme_get_customizer_header();
		// $cached_child_admin_customizer_css .= theme_get_customizer_header_branding();
		$cached_child_admin_customizer_css .= theme_get_customizer_navbar();
		// $cached_child_admin_customizer_css .= theme_get_customizer_primary_nav();
		// $cached_child_admin_customizer_css .= theme_get_customizer_secondary_nav();
		// $cached_child_admin_customizer_css .= theme_get_customizer_header_cta();
		// $cached_child_admin_customizer_css .= theme_get_customizer_header_cta_mobile();
		// $cached_child_admin_customizer_css .= theme_get_customizer_header_search();
		// $cached_child_admin_customizer_css .= theme_get_customizer_header_social_nav();
		// $cached_child_admin_customizer_css .= theme_get_customizer_footer();
		// $cached_child_admin_customizer_css .= theme_get_customizer_footer_branding();
		// $cached_child_admin_customizer_css .= theme_get_customizer_footer_cta();
		// $cached_child_admin_customizer_css .= theme_get_customizer_footer_nav();
		// $cached_child_admin_customizer_css .= theme_get_customizer_utility_nav();
		// $cached_child_admin_customizer_css .= theme_get_customizer_footer_social_nav();
		// $cached_child_admin_customizer_css .= theme_get_customizer_footer_search();
		// $cached_child_admin_customizer_css .= theme_get_customizer_footer_contact_info();
		// $cached_child_admin_customizer_css .= theme_get_customizer_footer_disclaimer();
		// $cached_child_admin_customizer_css .= theme_get_customizer_footer_attribution();
		// $cached_child_admin_customizer_css .= theme_get_customizer_footer_copyright();

		// Cache for 1 week (or until customizer is saved)
		set_transient( 'dream_child_admin_customizer_css', $cached_child_admin_customizer_css, WEEK_IN_SECONDS );
	}

	// Add the cached CSS
	wp_add_inline_style( 'child_admin_styles', $cached_child_admin_customizer_css );

}

if ( is_admin() ) {
	add_action( 'admin_enqueue_scripts', 'dream_child_enqueue_admin_styles' );
}

// Clear child customizer CSS cache when customizer is saved
add_action( 'customize_save_after', function() {
	delete_transient( 'dream_child_customizer_css' );
	delete_transient( 'dream_child_admin_customizer_css' );
});


// Block Editor Styles
function dream_child_acf_block_editor_style()
{
	wp_enqueue_style('child_block_css', get_stylesheet_directory_uri() . '/dist/editor.css', array('block_css'), wp_get_theme()->get( 'Version' ), 'all' );
}

if (is_admin()) {
	add_action('enqueue_block_assets', 'dream_child_acf_block_editor_style');
}


/**
 * Block Styles
 * @link https://jasonyingling.me/enqueueing-scripts-and-styles-for-gutenberg-blocks/
 */

/**
 * Block Styles - Detect blocks early when post content is guaranteed to be loaded
 * @link https://jasonyingling.me/enqueueing-scripts-and-styles-for-gutenberg-blocks/
 */

// Detect blocks on 'wp' hook (after main query is set, before template loads)
add_action('wp', function() {
	// Only run on singular pages where we have a single post
	if (!is_singular()) {
		return;
	}

	$post_id = get_the_ID();
	$blocks_metadata = dream_child_get_blocks_metadata(); // Helper function from block-helpers.php

	// Detect blocks NOW (when post content is definitely loaded)
	$used_blocks = dream_child_get_post_used_blocks($post_id, $blocks_metadata);

	// Temporary debug
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('wp hook (child) - Post ID: ' . $post_id);
		error_log('wp hook (child) - Post content length: ' . strlen(get_post($post_id)->post_content));
		error_log('wp hook (child) - Detected blocks: ' . print_r($used_blocks, true));
	}

	// Then hook into enqueue_block_assets to actually enqueue the styles
	add_action('enqueue_block_assets', function() use ($used_blocks) {
		$blocks_path = get_stylesheet_directory() . '/src/templates/blocks';

		// Only enqueue styles for blocks actually used on this page
		foreach ($used_blocks as $block_slug) {
			$style_path = $blocks_path . '/' . $block_slug . '/style.css';

			if (file_exists($style_path)) {
				wp_enqueue_style(
					'child_blocks_css_' . $block_slug,
					get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block_slug . '/style.css',
					array(),
					wp_get_theme()->get('Version'),
					'all'
				);
			}
		}
	});
});


// Admin editor: Load block admin styles (optimized - only load non-empty files)
// Note: block.json references assets, but WordPress won't auto-enqueue empty files
// So we handle enqueuing here with content check to skip empty/whitespace-only files
function dream_enqueue_child_block_admin_styles() {
	$blocks_path = get_stylesheet_directory() . '/src/templates/blocks';
	$blocks = array_filter(scandir($blocks_path), 'dream_child_filter_block_dir'); // Helper function from block-helpers.php

	foreach ($blocks as $block) {
		$index_css_path = $blocks_path . '/' . $block . '/index.css';

		// Only enqueue if file exists AND has actual content (not just whitespace)
		if (file_exists($index_css_path)) {
			$content = file_get_contents($index_css_path);
			if (!empty(trim($content))) {
				wp_enqueue_style(
					'child_blocks_css_' . $block,
					get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block . '/index.css',
					array(),
					wp_get_theme()->get('Version'),
					'all'
				);
			}
		}
	}
}
add_action('enqueue_block_editor_assets', 'dream_enqueue_child_block_admin_styles');
