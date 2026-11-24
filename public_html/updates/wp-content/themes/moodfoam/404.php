<?php
/**
 * "Not found" page inside the news section.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
get_header();
?>
<main class="mf-main">
	<div class="mf-hero">
		<div class="container">
			<p class="mf-eyebrow">Error 404</p>
			<h1>Page Not Found</h1>
			<p class="mf-lead">That update may have been moved or removed.</p>
		</div>
	</div>
	<div class="container mf-empty">
		<p>
			<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">All news &amp; updates</a>
			<a class="btn" href="<?php echo esc_url( mf_url( 'index.html' ) ); ?>">Back to the homepage</a>
		</p>
	</div>
</main>
<?php get_footer(); ?>
