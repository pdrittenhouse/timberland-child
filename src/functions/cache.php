<?php

/**
 * Cache Management - Child Theme
 *
 * Child theme-specific cache management and invalidation
 */

/**
 * ========================================
 * Child Theme Cache Clearing Functions
 * ========================================
 */

/**
 * Clear child theme caches (blocks, patterns, template styles)
 */
function dream_child_clear_cache() {
	global $wpdb;

	// Clear child theme block metadata cache (hybrid: both wp_cache and transient)
	dream_cache_delete('child_blocks_metadata', 'dream_child_blocks');

	// Clear child theme hybrid cache transients (database)
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_dream_child_blocks_child_blocks_metadata%' OR option_name LIKE '_transient_timeout_dream_child_blocks_child_blocks_metadata%'");
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_dream_child_blocks_child_post_blocks_%' OR option_name LIKE '_transient_timeout_dream_child_blocks_child_post_blocks_%'");
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_dream_child_blocks_child_post_blocks_time_%' OR option_name LIKE '_transient_timeout_dream_child_blocks_child_post_blocks_time_%'");
	$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_dream_child_blocks_child_pattern_blocks_%' OR option_name LIKE '_transient_timeout_dream_child_blocks_child_pattern_blocks_%'");

	// Clear wp_cache for dream_child_blocks group (Memcached on WP Engine)
	wp_cache_flush_group('dream_child_blocks');

	// Note: Minified block JS files are cleared by parent theme's dream_clear_all_cache()

	return true;
}

/**
 * ========================================
 * Child Theme Block Detection Cache Invalidation Hooks
 * ========================================
 */

/**
 * Clear cache for a specific post (child theme version)
 */
function dream_child_clear_post_cache($post_id) {
	// Skip autosaves and revisions
	if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
		return;
	}

	// Debug logging
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('CHILD CACHE INVALIDATION - Post ID: ' . $post_id . ' | Post Type: ' . get_post_type($post_id));
	}

	// Clear the post blocks cache (hybrid: both wp_cache and transient)
	dream_cache_delete('child_post_blocks_' . $post_id, 'dream_child_blocks');
	dream_cache_delete('child_post_blocks_time_' . $post_id, 'dream_child_blocks');

	// Debug logging
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('CHILD CACHE INVALIDATION - Cleared hybrid cache for post: ' . $post_id);
	}

	// If this is a pattern (wp_block post type), clear pattern cache (hybrid)
	if (get_post_type($post_id) === 'wp_block') {
		dream_cache_delete('child_pattern_blocks_' . $post_id, 'dream_child_blocks');
	}
}

/**
 * REST API save handler for Gutenberg (most reliable) - Child theme version
 */
function dream_child_clear_post_cache_rest($post, $request, $creating) {
	$post_id = $post->ID;
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('✅ CHILD REST SAVE DETECTED - Post ID: ' . $post_id . ' | Type: ' . $post->post_type . ' | Creating: ' . ($creating ? 'YES' : 'NO'));
	}
	dream_child_clear_post_cache($post_id);
}

// Hook into REST API saves (Gutenberg block editor) - Child theme
add_action('rest_after_insert_post', 'dream_child_clear_post_cache_rest', 10, 3);
add_action('rest_after_insert_page', 'dream_child_clear_post_cache_rest', 10, 3);
add_action('rest_after_insert_wp_block', 'dream_child_clear_post_cache_rest', 10, 3);

// AJAX catch-all for child theme (same as parent)
add_action('admin_init', function() {
	// Only on AJAX requests
	if (defined('DOING_AJAX') && DOING_AJAX && !empty($_POST['post_id'])) {
		$post_id = intval($_POST['post_id']);

		// Only clear cache once per request
		static $cleared_posts = [];
		if (!in_array($post_id, $cleared_posts)) {
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('CHILD AJAX REQUEST DETECTED - Clearing cache for post: ' . $post_id);
			}
			dream_child_clear_post_cache($post_id);
			$cleared_posts[] = $post_id;
		}
	}
}, 999);

// Also hook into traditional saves (Classic Editor, Quick Edit, programmatic) - Child theme
add_action('save_post', 'dream_child_clear_post_cache', 99, 1);
add_action('post_updated', 'dream_child_clear_post_cache', 99, 1);
add_action('acf/save_post', 'dream_child_clear_post_cache', 99, 1);

// Clear post-specific block cache when post is trashed - Child theme
add_action('wp_trash_post', function($post_id) {
	// Clear hybrid cache (both wp_cache and transient)
	dream_cache_delete('child_post_blocks_' . $post_id, 'dream_child_blocks');
	dream_cache_delete('child_post_blocks_time_' . $post_id, 'dream_child_blocks');

	if (get_post_type($post_id) === 'wp_block') {
		dream_cache_delete('child_pattern_blocks_' . $post_id, 'dream_child_blocks');
	}
});

// Clear post-specific block cache when post is deleted - Child theme
add_action('delete_post', function($post_id) {
	// Clear hybrid cache (both wp_cache and transient)
	dream_cache_delete('child_post_blocks_' . $post_id, 'dream_child_blocks');
	dream_cache_delete('child_post_blocks_time_' . $post_id, 'dream_child_blocks');

	if (get_post_type($post_id) === 'wp_block') {
		dream_cache_delete('child_pattern_blocks_' . $post_id, 'dream_child_blocks');
	}
});

// Clear all child theme post block caches when menus are updated (blocks can be in menus)
add_action('wp_update_nav_menu', function() {
	global $wpdb;

	// Clear hybrid cache transients (database)
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_dream_child_blocks_child_post_blocks_%'");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_dream_child_blocks_child_post_blocks_%'");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_dream_child_blocks_child_post_blocks_time_%'");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_dream_child_blocks_child_post_blocks_time_%'");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_dream_child_blocks_child_pattern_blocks_%'");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_dream_child_blocks_child_pattern_blocks_%'");

	// Clear wp_cache for dream_child_blocks group (Memcached on WP Engine)
	wp_cache_flush_group('dream_child_blocks');
});

// Clear all child theme post block caches when widgets are updated (blocks can be in widgets)
add_action('update_option_sidebars_widgets', function() {
	global $wpdb;

	// Clear hybrid cache transients (database)
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_dream_child_blocks_child_post_blocks_%'");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_dream_child_blocks_child_post_blocks_%'");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_dream_child_blocks_child_post_blocks_time_%'");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_dream_child_blocks_child_post_blocks_time_%'");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_dream_child_blocks_child_pattern_blocks_%'");
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_dream_child_blocks_child_pattern_blocks_%'");

	// Clear wp_cache for dream_child_blocks group (Memcached on WP Engine)
	wp_cache_flush_group('dream_child_blocks');
});

// Clear child theme block metadata cache when theme is switched
add_action('switch_theme', function() {
	// Clear hybrid cache (both wp_cache and transient)
	dream_cache_delete('child_blocks_metadata', 'dream_child_blocks');
});
