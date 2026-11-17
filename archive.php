<?php
/**
 * Template for archives: categories, tags, authors, dates and custom post type archives.
 *
 * @package Dock
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="container flow">
	<?php if ( have_posts() ) : ?>

		<header class="page-header flow">
			<?php
			the_archive_title( '<h1 class="page-title">', '</h1>' );
			the_archive_description( '<div class="archive-description">', '</div>' );
			?>
		</header>

		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry flow' ); ?>>
				<header class="entry-header">
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php if ( 'post' === get_post_type() ) : ?>
						<p class="entry-meta"><?php dock_posted_on(); ?></p>
					<?php endif; ?>
				</header>
				<div class="entry-summary flow">
					<?php the_excerpt(); ?>
				</div>
			</article>
			<?php
		endwhile;

		the_posts_pagination();
		?>

	<?php else : ?>

		<section class="no-results flow">
			<h1 class="page-title"><?php esc_html_e( 'Nothing found', 'dock' ); ?></h1>
			<p><?php esc_html_e( 'This archive is empty.', 'dock' ); ?></p>
		</section>

	<?php endif; ?>
</div>

<?php
get_footer();
