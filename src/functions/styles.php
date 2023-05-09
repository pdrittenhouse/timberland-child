<?php

function dream_child_enqueue_styles() {
    wp_enqueue_style( 'child_styles',
        get_stylesheet_directory_uri() . '/dist/styles.css',
        array( 'styles' ),
        wp_get_theme()->get( 'Version' ) // This only works if you have Version defined in the style header.
    );

	//Customizer Styles
	wp_add_inline_style( 'child_styles', theme_get_customizer_colors() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_buttons() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_global() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_forms() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_alerts() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_cards() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_modals() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_social_navs() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_header() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_header_branding() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_navbar() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_primary_nav() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_secondary_nav() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_header_cta() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_header_cta_mobile() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_header_search() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_header_social_nav() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_footer() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_footer_branding() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_footer_cta() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_footer_nav() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_utility_nav() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_footer_social_nav() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_footer_search() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_footer_contact_info() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_footer_disclaimer() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_footer_attribution() );
	wp_add_inline_style( 'child_styles', theme_get_customizer_footer_copyright() );
}
if ( !is_admin() ) {
	add_action( 'wp_enqueue_scripts', 'dream_child_enqueue_styles' );
}


function dream_child_enqueue_admin_styles() {

	// Enqueue Styles
	wp_enqueue_style( 'child_admin_styles', get_stylesheet_directory_uri() . '/dist/admin.css', array('admin_styles'), wp_get_theme()->get( 'Version' ), 'all' );

	//Customizer Styles
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_colors() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_buttons() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_global() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_forms() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_alerts() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_cards() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_modals() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_social_navs() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_header() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_header_branding() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_navbar() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_primary_nav() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_secondary_nav() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_header_cta() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_header_cta_mobile() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_header_search() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_header_social_nav() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_footer() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_footer_branding() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_footer_cta() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_footer_nav() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_utility_nav() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_footer_social_nav() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_footer_search() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_footer_contact_info() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_footer_disclaimer() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_footer_attribution() );
	wp_add_inline_style( 'child_admin_styles', theme_get_customizer_footer_copyright() );

}

if ( is_admin() ) {
	add_action( 'admin_enqueue_scripts', 'dream_child_enqueue_admin_styles' );
}


// Block Editor Styles
function dream_child_acf_block_editor_style()
{
	wp_enqueue_style('child_block_css', get_stylesheet_directory_uri() . '/dist/editor.css', array('block_css'), wp_get_theme()->get( 'Version' ), 'all' );
}

if (is_admin()) {
	add_action('enqueue_block_assets', 'dream_child_acf_block_editor_style');
}