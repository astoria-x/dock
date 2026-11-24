<?php
/**
 * Template for single posts.
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
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry flow' ); ?>>
			<header class="entry-header flow">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				<?php if ( 'post' === get_post_type() ) : ?>
					<p class="entry-meta"><?php dock_posted_on(); ?></p>
				<?php endif; ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="entry-thumbnail">
					<?php the_post_thumbnail( 'large' ); ?>
				</figure>
			<?php endif; ?>

			<div class="entry-content flow">
				<?php
				the_content();

				wp_link_pages(
					array(
						'before' => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'dock' ) . '">',
						'after'  => '</nav>',
					)
				);
				?>
			</div>
		</article>

		<?php
		the_post_navigation(
			array(
				'prev_text' => '<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'dock' ) . '</span> %title',
				'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next post:', 'dock' ) . '</span> %title',
			)
		);
	endwhile;
	?>
</div>

<?php
get_footer();
