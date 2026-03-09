<?php


// Arguments and permalink
// @link https://stackoverflow.com/questions/52427918/how-to-change-wordpress-default-posts-permalinks-programmatically


// Change default post arguments
add_filter('register_post_type_args', 'blog_change_post_args', 10, 2);
function blog_change_post_args($args, $post_type) {
   if ($post_type !== 'post') {
       return $args;
   }


   $args['rewrite'] = [
       'slug' => 'blog',
       'with_front' => true,
   ];


   return $args;
}


// Change default post permalink
add_filter('pre_post_link', 'blog_change_post_permalink', 10, 2);
function blog_change_post_permalink($permalink, $post) {
   if ($post->post_type !== 'post') {
       return $permalink;
   }


   return '/blog/%postname%/';
}


// Change default post type label
// @link: https://wpbeaches.com/change-the-wordpress-post-type-name-to-something-else/


// add_action( 'init', 'industy_insights_change_post_labels' );
// function industy_insights_change_post_labels() {
//     $get_post_type = get_post_type_object('post');
//     $labels = $get_post_type->labels;
//     $labels->name = 'Industry Insights';
//     $labels->singular_name = 'Industry Insight';
//     $labels->add_new = 'Add Industry Insight';
//     $labels->add_new_item = 'Add Industry Insight';
//     $labels->edit_item = 'Edit Industry Insight';
//     $labels->new_item = 'New Industry Insight';
//     $labels->view_item = 'View Industry Insight';
//     $labels->search_items = 'Search Industry Insight';
//     $labels->not_found = 'No Industry Insights found';
//     $labels->not_found_in_trash = 'No Industry Insights found in Trash';
//     $labels->all_items = 'All Industry Insights';
//     $labels->menu_name = 'Industry Insights';
//     $labels->name_admin_bar = 'Industry Insights';
//     $labels->more_items = 'More Industry Insights';
//     $labels->searching_form = 'industry insights';
//     $labels->searching_for = 'industry insights';
// }


