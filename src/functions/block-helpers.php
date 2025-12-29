<?php
/**
 * Block Helper Functions - Child Theme
 *
 * Note: Most block helper functions are now in the parent theme.
 * Child theme block.php files are loaded by the parent theme's
 * timberland_include_block_php_files() function, which handles both
 * parent and child theme blocks.
 */

/**
 * Filter block directory entries (child theme version)
 * Excludes hidden files (starting with .) and .twig files
 *
 * Used by: scripts.php, styles.php
 *
 * @param string $file The filename to check
 * @return bool True if file should be included
 */
function timberland_child_filter_block_dir($file) {
	return !str_starts_with($file, '.') && !str_ends_with($file, '.twig');
}
