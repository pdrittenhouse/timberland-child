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
