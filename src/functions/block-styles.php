<?php
/**
 * Block Styles
 */
function timberland_register_block_styles() {

  if ( function_exists( 'register_block_style' ) ) {

    /**
     * Image
    */
    // register_block_style(
    //   'core/image',
    //   array(
    //     'name'  => 'stylename',
    //     'label' => __('Style Label'),
    //   )
    // );

  }
}
add_action( 'after_setup_theme', 'timberland_register_block_styles' );
