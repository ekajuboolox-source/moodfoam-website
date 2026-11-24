<?php
/**
 * Mood Foam News theme: setup, assets and SEO helpers.
 *
 * This theme only styles the WordPress news section (/updates/). The rest of
 * the website is static HTML. To keep both looking identical, this theme loads
 * the static site's own stylesheet and script instead of copying them.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Public address of the main (static) site. No trailing slash. */
define( 'MOODFOAM_ROOT', 'https://www.moodfoammattresses.com' );

/**
 * Cache-busting versions of the static site's files. Keep these equal to the
 * ?v= numbers used on the static pages (css/style.css?v=31, java/script.js?v=9).
 */
define( 'MOODFOAM_CSS_VER', '31' );
define( 'MOODFOAM_JS_VER', '9' );

/** Absolute URL of a file/page on the main site, e.g. mf_url( 'about.html' ). */
function mf_url( $path = '' ) {
	return MOODFOAM_ROOT . '/' . ltrim( $path, '/' );
}

/* ------------------------------------------------------------------ setup */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_image_size( 'mf-card', 800, 520, true );   // news list thumbnails
} );

/* Keep body classes minimal: WordPress's defaults ("blog", "home", "post"...)
   would collide with class names already used by the main stylesheet. */
add_filter( 'body_class', function () {
	return array( 'mf-body', is_singular() ? 'mf-single' : 'mf-archive' );
} );

/* A news section does not need comments, pings or emoji scripts. */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );

add_filter( 'excerpt_length', function () { return 26; } );
add_filter( 'excerpt_more', function () { return '&hellip;'; } );
add_filter( 'document_title_separator', function () { return '|'; } );

/* --------------------------------------------------------------- hardening */
/* WordPress publishes the login username of every author (the public
   "users" REST endpoint, /?author=1 and author archive pages). Hiding those
   makes password guessing harder. Logged-in users (the editors) are unaffected. */
add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[d]+)'] );
	}
	return $endpoints;
} );
add_action( 'template_redirect', function () {
	if ( is_author() ) { wp_safe_redirect( home_url( '/' ), 301 ); exit; }
} );
add_action( 'init', function () {
	if ( ! is_admin() && isset( $_GET['author'] ) ) { wp_safe_redirect( home_url( '/' ), 301 ); exit; }
} );

/* ----------------------------------------------------------------- assets */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'mf-fonts', 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap', array(), null );
	wp_enqueue_style( 'mf-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), null );
	wp_enqueue_style( 'mf-site', mf_url( 'css/style.css' ), array( 'mf-fonts' ), MOODFOAM_CSS_VER );
	wp_enqueue_style( 'mf-news', get_stylesheet_uri(), array( 'mf-site' ), wp_get_theme()->get( 'Version' ) );

	wp_enqueue_script( 'mf-site-js', mf_url( 'java/script.js' ), array(), MOODFOAM_JS_VER, true );
	// The site script builds search links relative to the site root; tell it where that is.
	wp_add_inline_script( 'mf-site-js', 'window.MOODFOAM_ROOT = ' . wp_json_encode( MOODFOAM_ROOT ) . ';', 'before' );
} );

/* -------------------------------------------------------------------- SEO */
/**
 * True if a dedicated SEO plugin (e.g. SiteSEO, Yoast, Rank Math) is active, in
 * which case it outputs the description / Open Graph tags itself and this
 * theme stays out of the way to avoid duplicates.
 */
function mf_seo_plugin_active() {
	return defined( 'SITESEO_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || class_exists( 'SiteSEO' );
}

/** Plain-text summary of the current post for descriptions. */
function mf_summary( $post = null ) {
	$post = get_post( $post );
	$text = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;
	$text = wp_strip_all_tags( strip_shortcodes( $text ) );
	return wp_trim_words( preg_replace( '/\s+/', ' ', $text ), 28, '...' );
}

/** Social share image for a post: featured image, else the site's share image. */
function mf_share_image( $post = null ) {
	$post = get_post( $post );
	if ( $post && has_post_thumbnail( $post ) ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post ), 'large' );
		if ( $src ) { return $src[0]; }
	}
	return mf_url( 'images/og-image.jpg' );
}

add_action( 'wp_head', function () {
	if ( mf_seo_plugin_active() ) { return; }

	$title = wp_get_document_title();
	$desc  = get_bloginfo( 'description' );
	$url   = home_url( '/' );
	$image = mf_url( 'images/og-image.jpg' );
	$type  = 'website';

	if ( is_singular( 'post' ) ) {
		$desc  = mf_summary();
		$url   = get_permalink();
		$image = mf_share_image();
		$type  = 'article';
	} elseif ( is_home() ) {
		$desc = 'Latest news, offers and updates from Mood Foam Mattresses, Uganda.';
	} elseif ( is_search() || is_404() ) {
		echo '<meta name="robots" content="noindex, follow">' . "\n";
		return;
	}

	echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta name="robots" content="index, follow, max-image-preview:large">' . "\n";
	echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:site_name" content="Mood Foam Mattresses">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	echo '<meta property="og:locale" content="en_GB">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta name="twitter:image" content="' . esc_url( $image ) . '">' . "\n";
}, 5 );

/** Article structured data (JSON-LD) for single posts. */
add_action( 'wp_head', function () {
	// SEO plugins already print Article/BlogPosting data; avoid duplicates.
	if ( ! is_singular( 'post' ) || mf_seo_plugin_active() ) { return; }
	$data = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BlogPosting',
		'headline'         => get_the_title(),
		'description'      => mf_summary(),
		'image'            => array( mf_share_image() ),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'mainEntityOfPage' => get_permalink(),
		'author'           => array( '@type' => 'Organization', 'name' => 'Mood Foam Mattresses' ),
		'publisher'        => array(
			'@type' => 'Organization',
			'name'  => 'Mood Foam Mattresses',
			'logo'  => array( '@type' => 'ImageObject', 'url' => mf_url( 'images/logo.png' ) ),
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}, 6 );

/* ---------------------------------------------------------------- helpers */
/** Card image: the post thumbnail, or a branded placeholder if none is set. */
function mf_card_image() {
	if ( has_post_thumbnail() ) {
		the_post_thumbnail( 'mf-card', array( 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ) );
	} else {
		echo '<span class="mf-card-placeholder"><img src="' . esc_url( mf_url( 'images/logo-icon.png' ) ) . '" alt="" width="180" height="134"></span>';
	}
}
