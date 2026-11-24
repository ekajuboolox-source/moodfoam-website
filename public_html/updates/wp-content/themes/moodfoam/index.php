<?php
/**
 * News list: used for the main /updates/ page, categories, tags, dates and search.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();

if ( is_search() ) {
	/* translators: %s: search words */
	$heading = sprintf( 'Search results for &ldquo;%s&rdquo;', esc_html( get_search_query() ) );
} elseif ( is_home() ) {
	$heading = 'News &amp; Updates';
} else {
	$heading = wp_kses_post( get_the_archive_title() );
}
?>
<main class="mf-main">
	<div class="mf-hero">
		<div class="container">
			<p class="mf-eyebrow">Mood Foam Mattresses</p>
			<h1><?php echo $heading; // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
			<?php if ( is_home() ) : ?>
				<p class="mf-lead">Offers, new arrivals and the latest from our Mityana factory.</p>
			<?php endif; ?>
		</div>
	</div>

	<div class="container mf-list">
		<?php if ( have_posts() ) : ?>
			<div class="mf-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<article class="mf-item">
						<a class="mf-item-img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php mf_card_image(); ?></a>
						<div class="mf-item-body">
							<time class="mf-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
							<h2 class="mf-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p class="mf-item-excerpt"><?php echo esc_html( wp_trim_words( mf_summary(), 26, '...' ) ); ?></p>
							<a class="mf-more" href="<?php the_permalink(); ?>">Read more <i class="fas fa-arrow-right"></i></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>

			<?php
			$links = paginate_links( array(
				'type'      => 'list',
				'prev_text' => '&larr; Newer',
				'next_text' => 'Older &rarr;',
			) );
			if ( $links ) : ?>
				<nav class="mf-pagination" aria-label="More news"><?php echo $links; // phpcs:ignore WordPress.Security.EscapeOutput ?></nav>
			<?php endif; ?>

		<?php else : ?>
			<div class="mf-empty">
				<h2>Nothing posted yet</h2>
				<p>New offers and updates will appear here soon. In the meantime, browse our products or get in touch.</p>
				<p><a class="btn" href="<?php echo esc_url( mf_url( 'showroom.html' ) ); ?>">Visit the showroom</a></p>
			</div>
		<?php endif; ?>
	</div>
</main>
<?php get_footer(); ?>
