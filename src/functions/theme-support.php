<?php

// Remove dashicons from front-end
function remove_dashicons() {
    if ( is_user_logged_in() ) {
        wp_dequeue_style( 'dashicons' );
    }
}
add_action( 'wp_enqueue_scripts', 'remove_dashicons', 20 );
