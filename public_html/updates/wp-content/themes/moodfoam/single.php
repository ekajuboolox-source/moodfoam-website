<?php
/**
 * A single news post.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
the_post();

$share_text = rawurlencode( get_the_title() . ' - ' . get_permalink() );
$prev       = get_previous_post();

// A tall (portrait) cover, e.g. a poster, sits beside the text on wide screens
// instead of pushing the whole article far down the page.
$portrait = false;
if ( has_post_thumbnail() ) {
	$meta = wp_get_attachment_metadata( get_post_thumbnail_id() );
	if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
		$portrait = $meta['height'] > $meta['width'] * 1.15;
	}
}
$next       = get_next_post();
?>
<main class="mf-main">
	<div class="mf-hero mf-hero--post">
		<div class="container">
			<p class="mf-eyebrow"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">&larr; All news &amp; updates</a></p>
			<h1><?php the_title(); ?></h1>
			<p class="mf-meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
				<?php
				$cats = get_the_category();
				if ( $cats && 'uncategorized' !== $cats[0]->slug ) { echo ' &middot; ' . esc_html( $cats[0]->name ); }
				?>
			</p>
		</div>
	</div>

	<article class="container mf-article<?php echo $portrait ? ' mf-article--portrait' : ''; ?>">
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="mf-featured"><?php the_post_thumbnail( 'large', array( 'alt' => esc_attr( get_the_title() ) ) ); ?></figure>
		<?php endif; ?>

		<div class="mf-article-body">
		<div class="mf-content"><?php the_content(); ?></div>

		<div class="mf-share">
			<span>Share this:</span>
			<a class="mf-share-btn mf-share-wa" href="https://wa.me/?text=<?php echo $share_text; // phpcs:ignore WordPress.Security.EscapeOutput ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp</a>
			<a class="mf-share-btn mf-share-fb" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo rawurlencode( get_permalink() ); ?>" target="_blank" rel="noopener noreferrer"><i class="fab fa-facebook-f"></i> Facebook</a>
		</div>

		<?php if ( $prev || $next ) : ?>
			<nav class="mf-prevnext" aria-label="More news">
				<?php if ( $prev ) : ?><a class="mf-prev" href="<?php echo esc_url( get_permalink( $prev ) ); ?>"><small>&larr; Previous</small><?php echo esc_html( get_the_title( $prev ) ); ?></a><?php endif; ?>
				<?php if ( $next ) : ?><a class="mf-next" href="<?php echo esc_url( get_permalink( $next ) ); ?>"><small>Next &rarr;</small><?php echo esc_html( get_the_title( $next ) ); ?></a><?php endif; ?>
			</nav>
		<?php endif; ?>

		<div class="mf-cta">
			<h2>Interested in our products?</h2>
			<p>Call or message us on WhatsApp for prices, sizes and delivery anywhere in Uganda.</p>
			<a class="btn" href="https://wa.me/256743053096" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> Chat on WhatsApp</a>
		</div>
		</div>
	</article>
</main>
<?php get_footer(); ?>
