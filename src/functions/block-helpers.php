<?php

/**
 * Block Helper Functions - Child Theme
 *
 * Block detection, caching, and utility functions for child theme ACF blocks
 * Used by: blocks.php, scripts.php, styles.php
 */

/**
 * ========================================
 * Block Directory & Metadata Functions
 * ========================================
 */

/**
 * Filter block directory entries (child theme)
 * Used by: blocks.php, scripts.php, styles.php
 */
function timberland_child_filter_block_dir($file) {
	return !str_starts_with($file, '.') && !str_ends_with($file, '.twig');
}

/**
 * Block Detection & Metadata Functions
 * Uses hybrid cache functions from parent theme's cache.php (timberland_cache_get, timberland_cache_set, timberland_cache_delete)
 * Used by: scripts.php, styles.php
 */

/**
 * Get cached block metadata for CHILD theme only
 * Returns array of ['block-slug' => 'acf/block-name', ...]
 */
function timberland_child_get_blocks_metadata() {
	// Use hybrid cache: wp_cache (Memcached on WP Engine) + transient fallback
	$blocks_metadata = timberland_cache_get('child_blocks_metadata', 'timberland_child_blocks');

	// Temporary debug logging
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('timberland_child_get_blocks_metadata - Called, cache hit: ' . ($blocks_metadata !== false ? 'YES' : 'NO'));
	}

	if (false === $blocks_metadata) {
		$blocks_metadata = [];

		// Scan child theme blocks ONLY
		$blocks_path = get_stylesheet_directory() . '/src/templates/blocks';

		// Temporary debug logging
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('timberland_child_get_blocks_metadata - Scanning CHILD theme blocks from: ' . $blocks_path);
		}

		if (file_exists($blocks_path)) {
			$blocks = array_filter(scandir($blocks_path), 'timberland_child_filter_block_dir');

			foreach ($blocks as $block) {
				$block_json_path = $blocks_path . '/' . $block . '/block.json';

				if (file_exists($block_json_path)) {
					$block_json_content = file_get_contents($block_json_path);
					$block_data = json_decode($block_json_content, true);

					if (isset($block_data['name'])) {
						$blocks_metadata[$block] = $block_data['name'];
					}
				}
			}
		}

		// Temporary debug logging
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('timberland_child_get_blocks_metadata - Found ' . count($blocks_metadata) . ' child theme blocks');
			error_log('timberland_child_get_blocks_metadata - Blocks: ' . print_r($blocks_metadata, true));
		}

		// Cache for 1 week (hybrid: both wp_cache and transient)
		timberland_cache_set('child_blocks_metadata', $blocks_metadata, 'timberland_child_blocks', WEEK_IN_SECONDS);
	}

	return $blocks_metadata;
}

/**
 * Recursively extract all block names from parsed block tree (child theme)
 */
function timberland_child_extract_block_names_recursive($blocks, &$block_names = []) {
	foreach ($blocks as $block) {
		if (!empty($block['blockName'])) {
			$block_names[] = $block['blockName'];
		}

		// Recursively process inner blocks
		if (!empty($block['innerBlocks'])) {
			timberland_child_extract_block_names_recursive($block['innerBlocks'], $block_names);
		}
	}

	return $block_names;
}

/**
 * Get blocks used in a specific pattern (cached) - Child theme version
 */
function timberland_child_get_pattern_used_blocks($pattern_id, $blocks_metadata) {
	$cache_key = 'child_pattern_blocks_' . $pattern_id;

	// Use hybrid cache: wp_cache (Memcached on WP Engine) + transient fallback
	$used_blocks = timberland_cache_get($cache_key, 'timberland_child_blocks');

	if (false === $used_blocks) {
		$used_blocks = [];
		$pattern = get_post($pattern_id);

		if ($pattern) {
			$pattern_content = $pattern->post_content;

			// Parse blocks using WordPress parse_blocks() for reliable detection
			$parsed_blocks = parse_blocks($pattern_content);
			$block_names_in_content = timberland_child_extract_block_names_recursive($parsed_blocks);

			// Match against our block metadata
			foreach ($blocks_metadata as $block_slug => $block_name) {
				if (in_array($block_name, $block_names_in_content)) {
					$used_blocks[] = $block_slug;
				}
			}
		}

		// Cache for 1 day (hybrid: both wp_cache and transient)
		timberland_cache_set($cache_key, $used_blocks, 'timberland_child_blocks', DAY_IN_SECONDS);
	}

	return $used_blocks;
}

/**
 * Get all blocks used in a post (including blocks within patterns) - Child theme version
 * Uses unique cache keys to avoid collision with parent theme
 */
function timberland_child_get_post_used_blocks($post_id, $blocks_metadata) {
	$cache_key = 'child_post_blocks_' . $post_id;
	$cache_time_key = 'child_post_blocks_time_' . $post_id;

	// Use hybrid cache: wp_cache (Memcached on WP Engine) + transient fallback
	$used_blocks = timberland_cache_get($cache_key, 'timberland_child_blocks');
	$cache_time = timberland_cache_get($cache_time_key, 'timberland_child_blocks');

	// Get post modified time from database (always current)
	$post = get_post($post_id);
	$post_modified_time = $post ? strtotime($post->post_modified_gmt) : 0;

	// Cache is stale if the post was modified at or after the cache creation time
	// We use >= because saves can happen within the same second as cache creation
	$cache_is_stale = ($cache_time && $post_modified_time >= $cache_time);

	// Temporary debug logging
	if (defined('WP_DEBUG') && WP_DEBUG) {
		error_log('timberland_child_get_post_used_blocks - Post ID: ' . $post_id);
		error_log('timberland_child_get_post_used_blocks - Cache hit: ' . ($used_blocks !== false ? 'YES' : 'NO'));
		error_log('timberland_child_get_post_used_blocks - Post modified GMT: ' . date('Y-m-d H:i:s', $post_modified_time) . ' (' . $post_modified_time . ')');
		error_log('timberland_child_get_post_used_blocks - Cache created: ' . ($cache_time ? date('Y-m-d H:i:s', $cache_time) . ' (' . $cache_time . ')' : 'NONE'));
		error_log('timberland_child_get_post_used_blocks - Cache is stale: ' . ($cache_is_stale ? 'YES' : 'NO'));
		if ($used_blocks !== false) {
			error_log('timberland_child_get_post_used_blocks - Cached blocks: ' . print_r($used_blocks, true));
		}
	}

	// Rebuild cache if it doesn't exist OR if post was modified at/after cache was created
	if (false === $used_blocks || $cache_is_stale) {
		$used_blocks = [];
		$post = get_post($post_id);

		// Temporary debug logging
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('timberland_child_get_post_used_blocks - Cache MISS, rescanning post');
			error_log('timberland_child_get_post_used_blocks - Post object: ' . ($post ? 'Found' : 'NULL'));
		}

		if ($post) {
			$post_content = $post->post_content;

			// Temporary debug logging
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('timberland_child_get_post_used_blocks - Post content length: ' . strlen($post_content));
				error_log('timberland_child_get_post_used_blocks - Post content first 500 chars: ' . substr($post_content, 0, 500));
				error_log('timberland_child_get_post_used_blocks - Blocks metadata count: ' . count($blocks_metadata));
				error_log('timberland_child_get_post_used_blocks - Blocks metadata: ' . print_r($blocks_metadata, true));
			}

			// Parse blocks using WordPress parse_blocks() for reliable detection
			$parsed_blocks = parse_blocks($post_content);
			$block_names_in_content = timberland_child_extract_block_names_recursive($parsed_blocks);

			// Temporary debug logging
			if (defined('WP_DEBUG') && WP_DEBUG) {
				error_log('timberland_child_get_post_used_blocks - Parsed block names: ' . print_r($block_names_in_content, true));
			}

			// Match against our block metadata
			foreach ($blocks_metadata as $block_slug => $block_name) {
				$found = in_array($block_name, $block_names_in_content);

				// Temporary debug logging
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('timberland_child_get_post_used_blocks - Checking block: ' . $block_slug . ' (' . $block_name . ') - Result: ' . ($found ? 'FOUND' : 'NOT FOUND'));
				}

				if ($found) {
					$used_blocks[] = $block_slug;
				}
			}

			// Check for referenced patterns and get their blocks
			if (preg_match_all('/<!-- wp:block {"ref":(\d+)} \/-->/', $post_content, $matches)) {
				// Temporary debug logging
				if (defined('WP_DEBUG') && WP_DEBUG) {
					error_log('timberland_child_get_post_used_blocks - Found pattern references: ' . print_r($matches[1], true));
				}

				foreach ($matches[1] as $pattern_id) {
					$pattern_blocks = timberland_child_get_pattern_used_blocks($pattern_id, $blocks_metadata);
					$used_blocks = array_merge($used_blocks, $pattern_blocks);
				}
			}

			// Remove duplicates
			$used_blocks = array_unique($used_blocks);
		}

		// Temporary debug logging
		if (defined('WP_DEBUG') && WP_DEBUG) {
			error_log('timberland_child_get_post_used_blocks - Detected blocks: ' . print_r($used_blocks, true));
			error_log('timberland_child_get_post_used_blocks - Setting cache key: ' . $cache_key);
			error_log('timberland_child_get_post_used_blocks - Storing post modified time as cache time: ' . $post_modified_time);
		}

		// Cache for 1 day and store the POST'S modified time (not current time)
		// This way, if the post is saved again, its modified time will be newer than our cached time
		// Use hybrid cache: both wp_cache and transient
		timberland_cache_set($cache_key, $used_blocks, 'timberland_child_blocks', DAY_IN_SECONDS);
		timberland_cache_set($cache_time_key, $post_modified_time, 'timberland_child_blocks', DAY_IN_SECONDS);
	}

	return $used_blocks;
}
