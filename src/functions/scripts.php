<?php

function dream_child_enqueue_scripts() {
    wp_enqueue_script( 'child-script',
        get_stylesheet_directory_uri() . '/dist/scripts.min.js',
        array ( 'script' ),
        wp_get_theme()->get( 'Version' ),
    true
    );
}
if ( !is_admin() ) {
    add_action( 'wp_enqueue_scripts', 'dream_child_enqueue_scripts' );
}


function dream_child_enqueue_admin_scripts() {

	//Enqueue Scripts

	wp_enqueue_script( 'child_admin_script', get_stylesheet_directory_uri() . '/dist/admin.min.js', array ( 'admin_script' ), wp_get_theme()->get( 'Version' ), true);

}

if ( is_admin() ) {
	add_action( 'admin_enqueue_scripts', 'dream_child_enqueue_admin_scripts' );
}


/**
 * Child Block Scripts
 * @link https://jasonyingling.me/enqueueing-scripts-and-styles-for-gutenberg-blocks/
 */

function dream_enqueue_child_block_scripts() {
    $child_theme_blocks_path = dirname(__DIR__, 2) . '/src/templates/blocks';
    $child_theme_blocks = array_filter(scandir($child_theme_blocks_path), 'filter_block_dir');
    foreach ($child_theme_blocks as $block) {
        if ( file_exists( $child_theme_blocks_path . '/' . $block . '/script.js' ) ) {
            wp_enqueue_script('child_blocks_script_' . $block, get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block . '/script.js', array ( 'jquery', 'acf-input' ), wp_get_theme()->get( 'Version' ), 'all');
        }
    }
}
add_action( 'enqueue_block_assets', 'dream_enqueue_child_block_scripts' );

function dream_enqueue_child_block_admin_scripts() {
    $child_theme_blocks_path = dirname(__DIR__, 2) . '/src/templates/blocks';
    $child_theme_blocks = array_filter(scandir($child_theme_blocks_path), 'filter_block_dir');
    foreach ($child_theme_blocks as $block) {
        if ( file_exists( $child_theme_blocks_path . '/' . $block . '/index.js' ) ) {
            wp_enqueue_script('child_blocks_admin_script_' . $block, get_stylesheet_directory_uri() . '/src/templates/blocks/' . $block . '/index.js', array ( 'jquery', 'acf-input' ), wp_get_theme()->get( 'Version' ), 'all');
        }
    }
}
add_action( 'enqueue_block_editor_assets', 'dream_enqueue_child_block_admin_scripts' );
