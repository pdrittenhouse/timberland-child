<?php

// // Rename existing sidebars
// function update_sidebar_names($sidebar)
// {
//     global $wp_registered_sidebars;

//     if ('Primary' === $sidebar['name']) {
//         $id = $sidebar['id'];
//         $sidebar['name'] = 'New Primary Name';
//         $wp_registered_sidebars[$id] = $sidebar;
//     }

//     if ('Secondary' === $sidebar['name']) {
//         $id = $sidebar['id'];
//         $sidebar['name'] = 'New Secondary Name';
//         $wp_registered_sidebars[$id] = $sidebar;
//     }

//     if ('Tertiary' === $sidebar['name']) {
//         $id = $sidebar['id'];
//         $sidebar['name'] = 'New Tertiary Name';
//         $wp_registered_sidebars[$id] = $sidebar;
//     }

//     return;
// }
// add_action('register_sidebar', 'update_sidebar_names');

// // Register new sidebars
// function child_widgets_init()
// {
//     register_sidebar(array(
//         'name'          => 'Widget Area Name',
//         'id'            => 'widget_area_name',
//         'before_widget' => '<div class="new-widget-area">',
//         'after_widget'  => '</div>',
//         'before_title'  => '<h2>',
//         'after_title'   => '</h2>',
//     ));
// }
// add_action('widgets_init', 'child_widgets_init');
