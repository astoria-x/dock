<?php
/**
 * Dock functions and definitions.
 *
 * Theme supports, menus, asset loading and front-end cleanup.
 * Child themes override or extend anything here through hooks, not by copying this file.
 *
 * @package Dock
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DOCK_VERSION', '1.1.1' );

/*
 * -----------------------------------------------------------------------------
 * Theme setup
 * -----------------------------------------------------------------------------
 */

if ( ! function_exists( 'dock_setup' ) ) {
	/**
	 * Register theme supports and menu locations.
	 *
	 * Wrapped in function_exists so a child theme can replace it entirely.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function dock_setup() {
		load_theme_textdomain( 'dock', get_template_directory() . '/languages' );

		// Let WordPress manage the document <title>.
		add_theme_support( 'title-tag' );

		// Featured images.
		add_theme_support( 'post-thumbnails' );

		// Valid HTML5 markup for core output.
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);

		// Menus (primary and footer).
		add_theme_support( 'menus' );
		register_nav_menus(
			array(
				'primary' => esc_html__( 'Primary menu', 'dock' ),
				'footer'  => esc_html__( 'Footer menu', 'dock' ),
			)
		);

		// Editor styles: the block editor uses the same tokens and base styles as the front end.
		add_theme_support( 'editor-styles' );
		add_editor_style(
			array(
				'assets/css/variables.css',
				'assets/css/base.css',
			)
		);

		// Embeds keep their aspect ratio on any screen.
		add_theme_support( 'responsive-embeds' );
	}
}
add_action( 'after_setup_theme', 'dock_setup' );

/*
 * -----------------------------------------------------------------------------
 * Front-end assets
 * -----------------------------------------------------------------------------
 */

/**
 * Enqueue the theme stylesheets in token, reset, base order.
 *
 * Uses get_template_directory_uri() so the files always come from Dock,
 * even when a child theme is active. No scripts are enqueued: Dock ships no JavaScript.
 *
 * @since 1.0.0
 * @return void
 */
function dock_enqueue_assets() {
	$uri = get_template_directory_uri() . '/assets/css/';

	wp_enqueue_style( 'dock-variables', $uri . 'variables.css', array(), DOCK_VERSION );
	wp_enqueue_style( 'dock-reset', $uri . 'reset.css', array( 'dock-variables' ), DOCK_VERSION );
	wp_enqueue_style( 'dock-base', $uri . 'base.css', array( 'dock-reset' ), DOCK_VERSION );
}
add_action( 'wp_enqueue_scripts', 'dock_enqueue_assets' );

/**
 * Match the 62.5% root font size inside the iframed block editor.
 *
 * The add_editor_style() call rewrites html and :root selectors to the editor wrapper,
 * so rem values would be measured against 16px instead of 10px. This rule only
 * matches the editor iframe (its body carries .editor-styles-wrapper), never wp-admin.
 *
 * @since 1.0.0
 * @return void
 */
function dock_editor_root_font_size() {
	if ( ! is_admin() ) {
		return;
	}

	wp_register_style( 'dock-editor-root', false, array(), DOCK_VERSION );
	wp_enqueue_style( 'dock-editor-root' );
	wp_add_inline_style( 'dock-editor-root', ':root:has(> body.editor-styles-wrapper){font-size:62.5%}' );
}
add_action( 'enqueue_block_assets', 'dock_editor_root_font_size' );

/*
 * -----------------------------------------------------------------------------
 * Editor: block editor everywhere
 * -----------------------------------------------------------------------------
 */

/**
 * Use the block editor for every post type that supports it.
 *
 * Some plugins switch the block editor off for their post types (WooCommerce does
 * for products), which sends editors to the classic editor. Dock edits all content
 * in the block editor: any post type with show_in_rest and the editor feature gets it.
 * Meta boxes such as WooCommerce's Product data still show, as panels under the content.
 * A child theme can keep the classic editor for a post type with the
 * dock_use_block_editor filter.
 *
 * @since 1.1.1
 * @param bool   $use_block_editor Whether the post type uses the block editor.
 * @param string $post_type        Post type name.
 * @return bool
 */
function dock_use_block_editor_for_post_type( $use_block_editor, $post_type ) {
	$object = get_post_type_object( $post_type );

	if ( $object && $object->show_in_rest && post_type_supports( $post_type, 'editor' ) ) {
		$use_block_editor = true;
	}

	/**
	 * Filter whether Dock turns the block editor on for a post type.
	 *
	 * @since 1.1.1
	 * @param bool   $use_block_editor Whether the post type uses the block editor.
	 * @param string $post_type        Post type name.
	 */
	return (bool) apply_filters( 'dock_use_block_editor', $use_block_editor, $post_type );
}
add_filter( 'use_block_editor_for_post_type', 'dock_use_block_editor_for_post_type', 100, 2 );

/**
 * Give the classic editor the same 10px root as the front end.
 *
 * The add_editor_style() call loads Dock's rem-based CSS into the classic (TinyMCE)
 * editor too, but its root stays at 16px, so every size would show 1.6 times too big. The rule is
 * added to TinyMCE only: a plain html rule in the editor stylesheets would also reach
 * the block editor, which rewrites html to .editor-styles-wrapper.
 *
 * @since 1.1.1
 * @param array $settings TinyMCE settings.
 * @return array
 */
function dock_classic_editor_root_font_size( $settings ) {
	$rule = 'html{font-size:62.5%}';

	$settings['content_style'] = empty( $settings['content_style'] )
		? $rule
		: $settings['content_style'] . ' ' . $rule;

	return $settings;
}
add_filter( 'tiny_mce_before_init', 'dock_classic_editor_root_font_size' );

/*
 * -----------------------------------------------------------------------------
 * Block styles: load only what a page uses
 * -----------------------------------------------------------------------------
 */

// Load each core block's CSS only when that block is on the page, instead of the full library.
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
add_filter( 'should_load_block_assets_on_demand', '__return_true' );

/**
 * Remove core CSS that a builder-free classic theme does not need on the front end.
 *
 * The classic-theme-styles handle adds default button and file-block styling for themes
 * without their own; Dock styles those itself.
 *
 * @since 1.0.0
 * @return void
 */
function dock_dequeue_core_styles() {
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'dock_dequeue_core_styles', 20 );

/*
 * -----------------------------------------------------------------------------
 * Cleanup
 * -----------------------------------------------------------------------------
 */

/**
 * Remove emoji detection scripts and styles everywhere.
 *
 * Every modern browser renders emoji natively.
 *
 * @since 1.0.0
 * @return void
 */
function dock_disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'admin_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

	add_filter( 'tiny_mce_plugins', 'dock_disable_emoji_tinymce' );
	add_filter( 'wp_resource_hints', 'dock_disable_emoji_dns_prefetch', 10, 2 );
}
add_action( 'init', 'dock_disable_emoji' );

/**
 * Remove the emoji plugin from the classic editor.
 *
 * @since 1.0.0
 * @param array $plugins TinyMCE plugins.
 * @return array
 */
function dock_disable_emoji_tinymce( $plugins ) {
	return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
}

/**
 * Remove the emoji CDN from DNS prefetch hints.
 *
 * @since 1.0.0
 * @param array  $urls          URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array
 */
function dock_disable_emoji_dns_prefetch( $urls, $relation_type ) {
	if ( 'dns-prefetch' !== $relation_type ) {
		return $urls;
	}

	return array_filter(
		$urls,
		static function ( $url ) {
			$href = is_array( $url ) ? ( $url['href'] ?? '' ) : (string) $url;
			return false === strpos( $href, 'https://s.w.org/images/core/emoji/' );
		}
	);
}

/**
 * Remove head tags that leak information or serve legacy clients only.
 *
 * REST API discovery and oEmbed links stay: SEO and integration plugins rely on them.
 *
 * @since 1.0.0
 * @return void
 */
function dock_clean_head() {
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
add_action( 'after_setup_theme', 'dock_clean_head' );

/*
 * -----------------------------------------------------------------------------
 * Template helpers
 * -----------------------------------------------------------------------------
 */

if ( ! function_exists( 'dock_posted_on' ) ) {
	/**
	 * Print the post date as a <time> element.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function dock_posted_on() {
		printf(
			'<time class="entry-date" datetime="%1$s">%2$s</time>',
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() )
		);
	}
}

/*
 * -----------------------------------------------------------------------------
 * Plugin support
 * -----------------------------------------------------------------------------
 */

// WooCommerce: theme support, Dock layout and shop CSS. Loads only when WooCommerce is active.
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}
