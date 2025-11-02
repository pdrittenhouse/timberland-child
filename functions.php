<?php

/**
 * Child Theme Text Domain Configuration
 * Override by defining TIMBERLAND_CHILD_TEXT_DOMAIN before this line
 * Example: define('TIMBERLAND_CHILD_TEXT_DOMAIN', 'my-custom-domain');
 */
if (!defined('TIMBERLAND_CHILD_TEXT_DOMAIN')) {
    define('TIMBERLAND_CHILD_TEXT_DOMAIN', 'timberland-child');
}

/**
 * Load child theme textdomain for translations
 */
function timberland_child_load_textdomain() {
    load_theme_textdomain(TIMBERLAND_CHILD_TEXT_DOMAIN, get_stylesheet_directory() . '/src/languages');
}
add_action('after_setup_theme', 'timberland_child_load_textdomain');

/**
 * Include settings from /src/functions
 */
$timberland_child_includes = array(
    "cache.php",
    "block-helpers.php",
    "scripts.php",
    "styles.php",
    "blocks.php",
    "redirects.php",
    "rewrites.php",
    "routes.php",
    "search.php",
    "theme-support.php",
);

foreach($timberland_child_includes as $inc){
    include_once(get_stylesheet_directory() . "/src/functions/$inc");
}

