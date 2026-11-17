<?php
/**
 * Template for 404 (page not found).
 *
 * @package Dock
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="container">
	<section class="error-404 flow">
		<header class="page-header">
			<h1 class="page-title"><?php esc_html_e( 'Page not found', 'dock' ); ?></h1>
		</header>
		<p><?php esc_html_e( 'The page you were looking for does not exist or has moved. Try a search.', 'dock' ); ?></p>
		<?php get_search_form(); ?>
	</section>
</div>

<?php
get_footer();
