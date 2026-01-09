<?php

/**
 * Include block layouts from /src/functions/block-layouts
 */
// $timberland_block_layouts = array(
//     "post.php"
// );

// foreach ($timberland_block_layouts as $inc) {
//     include_once(get_stylesheet_directory() . "/src/functions/block-layouts/$inc");
// }

// post.php example:
// function add_post_block_template($args, $post_type)
// {
//     // Only modify the 'post' post type
//     if ('post' === $post_type) {
//         // Define block template based on the provided content
//         $args['template'] = array(
//             // First section with video
//             array('acf/section', array(), array(
//                 array('acf/row', array(), array(
//                     array('acf/column', array(
//                         'col_width_0_width' => '12',
//                         'col_width_1_width' => '10',
//                         'col_offset_0_offset' => '1'
//                     ), array(
//                         array('acf/video', array(
//                             'format' => 'youtube',
//                             'source' => 'https://www.youtube.com/embed/C0DPdy98e4c?si=ihhmn5wKzFP0aZf6'
//                         ))
//                     ))
//                 ))
//             )),

//             // Spacer
//             array('core/spacer', array(
//                 'height' => '60px'
//             )),

//             // Second section with content columns
//             array('acf/section', array(), array(
//                 array('acf/row', array(), array(
//                     array('acf/column', array(
//                         'col_width_0_width' => '12',
//                         'col_width_1_width' => '10',
//                         'col_offset_0_offset' => '1'
//                     ), array(
//                         array('acf/row', array(), array(
//                             array('acf/column', array(
//                                 'col_width_0_width' => '12',
//                                 'col_width_1_width' => '8'
//                             ), array(
//                                 array('core/paragraph', array(
//                                     'content' => 'Add content here'
//                                 ))
//                             )),
//                             array('acf/column', array(
//                                 'col_width_0_width' => '12',
//                                 'col_width_1_width' => '4'
//                             ), array(
//                                 array('core/block', array(
//                                     'ref' => 2319
//                                 ))
//                             ))
//                         ))
//                     ))
//                 ))
//             )),

//             // Final spacer
//             array('core/spacer', array(
//                 'height' => '60px'
//             ))
//         );
//     }

//     return $args;
// }
// add_filter('register_post_type_args', 'add_post_block_template', 20, 2);

/**
 * Auto-prefill posts with default block templates from HTML files, scoped by post type.
 *
 * File naming: src/functions/block-layouts/{post_type}.html
 */
add_filter('default_content', function ($content, $post) {

    if (empty($post) || empty($post->post_type)) {
        return $content;
    }

    // Only apply when creating a new post (avoid overwriting existing content).
    if (! empty($content)) {
        return $content;
    }

    $post_type = $post->post_type;

    $slug_variants = [
        $post_type,
        str_replace('_', '-', $post_type),
    ];

    $dir = get_stylesheet_directory() . '/src/templates/block-layouts';

    if (! is_dir($dir)) {
        return $content;
    }

    foreach ($slug_variants as $slug) {
        $file = trailingslashit($dir) . $slug . '.html';

        if (file_exists($file)) {
            $html = file_get_contents($file);
            if ($html) {
                return $html;
            }
        }
    }

    return $content;
}, 10, 2);
