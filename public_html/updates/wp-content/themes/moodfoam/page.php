<?php
/**
 * A normal WordPress page (e.g. a standalone announcement).
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
the_post();
?>
<main class="mf-main">
	<div class="mf-hero mf-hero--post">
		<div class="container">
			<p class="mf-eyebrow"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">&larr; News &amp; updates</a></p>
			<h1><?php the_title(); ?></h1>
		</div>
	</div>
	<article class="container mf-article">
		<div class="mf-content"><?php the_content(); ?></div>
	</article>
</main>
<?php get_footer(); ?>
