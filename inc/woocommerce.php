<?php
/**
 * WooCommerce support.
 *
 * Loaded by functions.php only when WooCommerce is active. Declares theme support,
 * puts shop pages inside Dock's own layout (one <main>, no sidebar), and loads
 * shop CSS and cart scripts only on pages that need them. Shop colors come from Dock's role tokens,
 * so a child theme restyles the store by changing tokens, like the rest of the site.
 *
 * To change a WooCommerce template, copy it into the child theme's woocommerce/ folder.
 *
 * @package Dock
 * @since   1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'dock_woocommerce_setup' ) ) {
	/**
	 * Declare WooCommerce support and the product gallery features.
	 *
	 * The gallery features use WooCommerce's own scripts, which WooCommerce loads on
	 * single product pages only. A child theme can turn any of them off with
	 * remove_theme_support().
	 *
	 * @since 1.1.0
	 * @return void
	 */
	function dock_woocommerce_setup() {
		add_theme_support(
			'woocommerce',
			array(
				'product_grid' => array(
					'default_columns' => 3,
					'min_columns'     => 1,
					'max_columns'     => 4,
				),
			)
		);
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action( 'after_setup_theme', 'dock_woocommerce_setup' );

/**
 * Swap WooCommerce's layout wrappers for Dock's.
 *
 * WooCommerce's default wrapper prints a second <main> inside Dock's main#content and
 * calls a sidebar Dock does not have. Dock's wrapper is a plain container instead.
 *
 * @since 1.1.0
 * @return void
 */
function dock_woocommerce_wrappers() {
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

	add_action( 'woocommerce_before_main_content', 'dock_woocommerce_wrapper_start', 10 );
	add_action( 'woocommerce_after_main_content', 'dock_woocommerce_wrapper_end', 10 );
}
add_action( 'after_setup_theme', 'dock_woocommerce_wrappers' );

if ( ! function_exists( 'dock_woocommerce_wrapper_start' ) ) {
	/**
	 * Open the shop layout wrapper.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	function dock_woocommerce_wrapper_start() {
		echo '<div class="container flow dock-shop">';
	}
}

if ( ! function_exists( 'dock_woocommerce_wrapper_end' ) ) {
	/**
	 * Close the shop layout wrapper.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	function dock_woocommerce_wrapper_end() {
		echo '</div>';
	}
}

/**
 * Whether the current request shows WooCommerce output that needs the shop CSS.
 *
 * True on WooCommerce templates, cart, checkout and account pages, and on any single
 * post or page whose content uses a WooCommerce shortcode. WooCommerce blocks load
 * their own CSS per block and do not need this. Filter with dock_woocommerce_load_styles.
 *
 * @since 1.1.0
 * @return bool
 */
function dock_woocommerce_needs_styles() {
	$needs = is_woocommerce() || is_cart() || is_checkout() || is_account_page();

	if ( ! $needs && is_singular() ) {
		$content    = (string) get_post_field( 'post_content', get_queried_object_id() );
		$shortcodes = array( 'products', 'product', 'product_page', 'product_category', 'product_categories', 'add_to_cart', 'shop_messages' );

		foreach ( $shortcodes as $shortcode ) {
			if ( has_shortcode( $content, $shortcode ) ) {
				$needs = true;
				break;
			}
		}
	}

	/**
	 * Filter whether Dock and WooCommerce shop CSS loads on this request.
	 *
	 * @since 1.1.0
	 * @param bool $needs True when the page shows WooCommerce output.
	 */
	return (bool) apply_filters( 'dock_woocommerce_load_styles', $needs );
}

/**
 * Keep WooCommerce's stylesheets off pages that show no shop output.
 *
 * WooCommerce otherwise loads its three stylesheets on every page of the site.
 *
 * @since 1.1.0
 * @param array $styles WooCommerce stylesheets keyed by handle.
 * @return array
 */
function dock_woocommerce_limit_styles( $styles ) {
	return dock_woocommerce_needs_styles() ? $styles : array();
}
add_filter( 'woocommerce_enqueue_styles', 'dock_woocommerce_limit_styles' );

/**
 * Load Dock's shop CSS after WooCommerce's, on shop pages only.
 *
 * @since 1.1.0
 * @return void
 */
function dock_woocommerce_enqueue_assets() {
	if ( ! dock_woocommerce_needs_styles() ) {
		return;
	}

	$deps = array( 'dock-base' );
	if ( wp_style_is( 'woocommerce-general', 'registered' ) || wp_style_is( 'woocommerce-general', 'enqueued' ) ) {
		$deps[] = 'woocommerce-general';
	}

	wp_enqueue_style(
		'dock-woocommerce',
		get_template_directory_uri() . '/assets/css/woocommerce.css',
		$deps,
		DOCK_VERSION
	);
}
add_action( 'wp_enqueue_scripts', 'dock_woocommerce_enqueue_assets', 15 );

/**
 * Keep WooCommerce's shop scripts off pages that show no shop output.
 *
 * WooCommerce loads jQuery and its cart scripts on every page. Off shop pages Dock
 * dequeues them; order attribution (marketing source tracking) is left alone, and any
 * script another enqueued script depends on still loads. Filter the handles with
 * dock_woocommerce_shop_scripts.
 *
 * @since 1.1.0
 * @return void
 */
function dock_woocommerce_limit_scripts() {
	if ( dock_woocommerce_needs_styles() ) {
		return;
	}

	/**
	 * Filter the WooCommerce script handles Dock removes from non-shop pages.
	 *
	 * @since 1.1.0
	 * @param string[] $handles Script handles.
	 */
	$handles = (array) apply_filters(
		'dock_woocommerce_shop_scripts',
		array( 'wc-add-to-cart', 'woocommerce', 'wc-cart-fragments', 'jquery-blockui', 'wc-jquery-blockui', 'js-cookie', 'wc-js-cookie' )
	);

	foreach ( $handles as $handle ) {
		wp_dequeue_script( $handle );
	}
}
add_action( 'wp_enqueue_scripts', 'dock_woocommerce_limit_scripts', 100 );
