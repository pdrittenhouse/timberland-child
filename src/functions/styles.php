<?php

function dream_child_enqueue_styles() {
    wp_enqueue_style( 'child_styles',
        get_stylesheet_directory_uri() . '/dist/styles.css',
        array( 'styles' ),
        wp_get_theme()->get( 'Version' ) // This only works if you have Version defined in the style header.
    );

	// Get cached child customizer CSS
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
		$cached_child_admin_customizer_css .= theme_get_customizer_alerts();
		$cached_child_admin_customizer_css .= theme_get_customizer_cards();
		$cached_child_admin_customizer_css .= theme_get_customizer_modals();
		$cached_child_admin_customizer_css .= theme_get_customizer_social_navs();
		$cached_child_admin_customizer_css .= theme_get_customizer_header();
		$cached_child_admin_customizer_css .= theme_get_customizer_header_branding();
		$cached_child_admin_customizer_css .= theme_get_customizer_navbar();
		$cached_child_admin_customizer_css .= theme_get_customizer_primary_nav();
		$cached_child_admin_customizer_css .= theme_get_customizer_secondary_nav();
		$cached_child_admin_customizer_css .= theme_get_customizer_header_cta();
		$cached_child_admin_customizer_css .= theme_get_customizer_header_cta_mobile();
		$cached_child_admin_customizer_css .= theme_get_customizer_header_search();
		$cached_child_admin_customizer_css .= theme_get_customizer_header_social_nav();
		$cached_child_admin_customizer_css .= theme_get_customizer_footer();
		$cached_child_admin_customizer_css .= theme_get_customizer_footer_branding();
		$cached_child_admin_customizer_css .= theme_get_customizer_footer_cta();
		$cached_child_admin_customizer_css .= theme_get_customizer_footer_nav();
		$cached_child_admin_customizer_css .= theme_get_customizer_utility_nav();
		$cached_child_admin_customizer_css .= theme_get_customizer_footer_social_nav();
		$cached_child_admin_customizer_css .= theme_get_customizer_footer_search();
		$cached_child_admin_customizer_css .= theme_get_customizer_footer_contact_info();
		$cached_child_admin_customizer_css .= theme_get_customizer_footer_disclaimer();
		$cached_child_admin_customizer_css .= theme_get_customizer_footer_attribution();
		$cached_child_admin_customizer_css .= theme_get_customizer_footer_copyright();

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
 * Child Block Styles
 * @link https://jasonyingling.me/enqueueing-scripts-and-styles-for-gutenberg-blocks/
 */

function dream_enqueue_child_block_styles() {
	if (get_post() === null) {
		return;
	}

	$post_id = get_the_ID();
	$child_blocks_metadata = dream_get_child_blocks_metadata();
	$used_blocks = dream_get_post_used_blocks($post_id, $child_blocks_metadata);
	$child_theme_blocks_path = dirname(__DIR__, 2) . '/src/templates/blocks';

	// Only enqueue styles for child theme blocks actually used on this page
	foreach ($used_blocks as $block_slug) {
		$style_path = $child_theme_blocks_path . '/' . $block_slug . '/style.css';

		if (file_exists($style_path)) {
			wp_enqueue_style(
				'child_blocks_style_' . $block_slug,
				get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block_slug . '/style.css',
				array(),
				wp_get_theme()->get('Version'),
				'all'
			);
		}
	}
}
add_action('enqueue_block_assets', 'dream_enqueue_child_block_styles');

//function dream_enqueue_child_block_styles() {
//    $child_theme_blocks_path = dirname(__DIR__, 2) . '/src/templates/blocks';
//    $child_theme_blocks = array_filter(scandir($child_theme_blocks_path), 'filter_block_dir');
//    foreach ($child_theme_blocks as $block) {
//      if ( file_exists( $child_theme_blocks_path . '/' . $block . '/style.css' ) ) {
//          wp_enqueue_style('child_blocks_style_' . $block, get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block . '/style.css', array(), wp_get_theme()->get( 'Version' ), 'all');
//      }
//    }
//}
//add_action( 'enqueue_block_assets', 'dream_enqueue_child_block_styles' );


// Admin editor: Load all child block admin styles (no caching needed - editor needs all blocks available)
function dream_enqueue_child_block_admin_styles() {
	$child_theme_blocks_path = dirname(__DIR__, 2) . '/src/templates/blocks';
	$child_theme_blocks = array_filter(scandir($child_theme_blocks_path), 'filter_block_dir');

	foreach ($child_theme_blocks as $block) {
		if (file_exists($child_theme_blocks_path . '/' . $block . '/index.css')) {
			wp_enqueue_style(
				'child_block_admin_style_' . $block,
				get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block . '/index.css',
				array(),
				wp_get_theme()->get('Version'),
				'all'
			);
		}
	}
}
add_action('enqueue_block_editor_assets', 'dream_enqueue_child_block_admin_styles');

//function dream_enqueue_child_block_admin_styles() {
//    $child_theme_blocks_path = dirname(__DIR__, 2) . '/src/templates/blocks';
//    $child_theme_blocks = array_filter(scandir($child_theme_blocks_path), 'filter_block_dir');
//    foreach ($child_theme_blocks as $block) {
//        if ( file_exists( $child_theme_blocks_path . '/' . $block . '/index.css' ) ) {
//            wp_enqueue_style('child_blocks_admin_style_' . $block, get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block . '/index.css', array(), wp_get_theme()->get( 'Version' ), 'all');
//        }
//    }
//}
//add_action( 'enqueue_block_editor_assets', 'dream_enqueue_child_block_admin_styles' );
