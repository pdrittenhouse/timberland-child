<?php

/**
 * Include settings from /src/functions
 */
$dream_child_includes = array(
    "scripts.php",
    "styles.php",
    "blocks.php"
);

foreach($dream_child_includes as $inc){
    include_once(get_stylesheet_directory() . "/src/functions/$inc");
}

