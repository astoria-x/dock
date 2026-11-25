<?php
/**
 * Theme updates from GitHub.
 *
 * Dock is not on WordPress.org, so WordPress cannot see new versions on its own.
 * When WordPress runs its own update check (in wp-admin and cron, about twice a day,
 * never on front-end page views), this reads the Version line of style.css on the
 * main branch of the public Dock repository. If it is newer than the installed
 * version, Dock appears on Dashboard > Updates and Appearance > Themes like any other
 * theme, and its automatic-updates toggle works. The package is the main branch zip;
 * main is always installable (see CLAUDE.md).
 *
 * @package Dock
 * @since   1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Where Dock's updates come from.
 *
 * @since 1.2.0
 * @return array{repo: string, branch: string, slug: string}
 */
function dock_update_source() {
	/**
	 * Filter the GitHub repository and branch Dock updates from.
	 *
	 * @since 1.2.0
	 * @param array $source Keys: repo (owner/name), branch, slug (theme folder name).
	 */
	return (array) apply_filters(
		'dock_update_source',
		array(
			'repo'   => 'astoria-x/dock',
			'branch' => 'main',
			'slug'   => 'dock',
		)
	);
}

/**
 * Read the newest Dock version from GitHub, cached for twelve hours.
 *
 * @since 1.2.0
 * @return string Version number, or an empty string when it cannot be read.
 */
function dock_remote_version() {
	$cached = get_site_transient( 'dock_remote_version' );
	if ( false !== $cached ) {
		return (string) $cached;
	}

	$source   = dock_update_source();
	$response = wp_remote_get(
		sprintf( 'https://raw.githubusercontent.com/%1$s/%2$s/style.css', $source['repo'], rawurlencode( $source['branch'] ) ),
		array( 'timeout' => 10 )
	);

	$version = '';
	if ( ! is_wp_error( $response ) && 200 === (int) wp_remote_retrieve_response_code( $response ) ) {
		if ( preg_match( '/^[ \t\/*#@]*Version:\s*([0-9][0-9A-Za-z.\-]*)/mi', wp_remote_retrieve_body( $response ), $match ) ) {
			$version = $match[1];
		}
	}

	// Cache failures for one hour so an outage does not slow every admin page.
	set_site_transient( 'dock_remote_version', $version, $version ? 12 * HOUR_IN_SECONDS : HOUR_IN_SECONDS );

	return $version;
}

/**
 * Add Dock to WordPress's theme update data.
 *
 * @since 1.2.0
 * @param object|false $transient The update_themes site transient.
 * @return object|false
 */
function dock_check_for_update( $transient ) {
	if ( ! is_object( $transient ) ) {
		return $transient;
	}

	$source = dock_update_source();
	$theme  = wp_get_theme( $source['slug'] );
	if ( ! $theme->exists() ) {
		return $transient;
	}

	$installed = (string) $theme->get( 'Version' );
	$remote    = dock_remote_version();
	if ( '' === $remote ) {
		return $transient;
	}

	$item = array(
		'theme'        => $source['slug'],
		'new_version'  => $remote,
		'url'          => 'https://github.com/' . $source['repo'],
		'package'      => sprintf( 'https://github.com/%1$s/archive/refs/heads/%2$s.zip', $source['repo'], rawurlencode( $source['branch'] ) ),
		'requires'     => (string) $theme->get( 'RequiresWP' ),
		'requires_php' => (string) $theme->get( 'RequiresPHP' ),
	);

	if ( version_compare( $remote, $installed, '>' ) ) {
		$transient->response[ $source['slug'] ] = $item;
		unset( $transient->no_update[ $source['slug'] ] );
	} else {
		// Listing Dock as up to date lets the automatic-updates toggle show on the Themes screen.
		$item['new_version']                     = $installed;
		$transient->no_update[ $source['slug'] ] = $item;
		unset( $transient->response[ $source['slug'] ] );
	}

	return $transient;
}
add_filter( 'pre_set_site_transient_update_themes', 'dock_check_for_update' );

/**
 * Keep Dock out of WordPress.org's update check.
 *
 * A different theme on WordPress.org may use the folder name "dock". Without this,
 * WordPress could offer to replace Dock with it.
 *
 * @since 1.2.0
 * @param array  $args HTTP request arguments.
 * @param string $url  Request URL.
 * @return array
 */
function dock_exclude_from_wporg( $args, $url ) {
	if ( false === strpos( $url, '//api.wordpress.org/themes/update-check/' ) || empty( $args['body']['themes'] ) ) {
		return $args;
	}

	$themes = json_decode( $args['body']['themes'], true );
	if ( ! is_array( $themes ) ) {
		return $args;
	}

	$slug = dock_update_source()['slug'];
	unset( $themes['themes'][ $slug ] );

	$args['body']['themes'] = wp_json_encode( $themes );

	return $args;
}
add_filter( 'http_request_args', 'dock_exclude_from_wporg', 10, 2 );

/**
 * Rename the unpacked GitHub folder (dock-main) to the theme's folder (dock).
 *
 * @since 1.2.0
 * @param string|WP_Error $source        Unpacked package folder.
 * @param string          $remote_source Folder the package was unpacked into.
 * @param WP_Upgrader     $upgrader      Upgrader instance.
 * @param array           $hook_extra    Extra arguments, including the theme slug.
 * @return string|WP_Error
 */
function dock_fix_update_folder( $source, $remote_source, $upgrader, $hook_extra = array() ) {
	global $wp_filesystem;

	$slug = dock_update_source()['slug'];
	if ( is_wp_error( $source ) || empty( $hook_extra['theme'] ) || $slug !== $hook_extra['theme'] ) {
		return $source;
	}

	$wanted = trailingslashit( $remote_source ) . $slug . '/';
	if ( untrailingslashit( $source ) === untrailingslashit( $wanted ) ) {
		return $source;
	}

	if ( $wp_filesystem && $wp_filesystem->move( $source, $wanted, true ) ) {
		return $wanted;
	}

	return new WP_Error( 'dock_update_folder', __( 'Dock could not rename the downloaded update folder.', 'dock' ) );
}
add_filter( 'upgrader_source_selection', 'dock_fix_update_folder', 10, 4 );

/**
 * Check again straight away after Dock itself is updated.
 *
 * @since 1.2.0
 * @return void
 */
function dock_clear_update_cache() {
	delete_site_transient( 'dock_remote_version' );
}
add_action( 'upgrader_process_complete', 'dock_clear_update_cache' );
