<?php

/**
 * Cache Management - Child Theme
 *
 * Child theme-specific cache invalidation
 */

/**
 * Clear child theme block metadata cache when theme is switched
 */
add_action('switch_theme', function() {
	delete_transient('dream_child_blocks_metadata');
});
