<?php
/**
 * Site header: identical markup to the static pages (top contact bar, logo,
 * navigation with dropdowns, search overlay). Generated from pvc.html; links
 * are made absolute with mf_url() because this theme lives in /updates/.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/x-icon" href="<?php echo esc_url( mf_url( 'favicon_io/favicon.ico' ) ); ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( mf_url( 'favicon_io/favicon-32x32.png' ) ); ?>">
<link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( mf_url( 'favicon_io/apple-touch-icon.png' ) ); ?>">
<link rel="manifest" href="<?php echo esc_url( mf_url( 'favicon_io/site.webmanifest' ) ); ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="top-info-bar">
  <div class="top-info-bar-inner">
    <a href="tel:+256764513341"><i class="fas fa-phone-alt"></i> 0764 513341</a>
    <a href="https://wa.me/256743053096" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> WhatsApp Us</a>
    <a href="mailto:busujjuindustries@gmail.com"><i class="fas fa-envelope"></i> busujjuindustries@gmail.com</a>
    <span><i class="fas fa-clock"></i> Mon&ndash;Sun, 8:00&ndash;18:00</span>
  </div>
</div>
 <header class="header">
  <div class="nav-bar">
    <div class="hamburger" id="hamburger-menu">
      <span></span>
      <span></span>
      <span></span>
    </div>
    <a href="<?php echo esc_url( mf_url( 'index.html' ) ); ?>" class="logo">
      <img src="<?php echo esc_url( mf_url( 'images/logo-header.png' ) ); ?>" alt="MOOD FOAM logo" class="logo-img"  width="525" height="142"/>
    </a>
    <div class="nav-search-wrap">
    <nav>
<ul class="nav-links">
  <li><a href="<?php echo esc_url( mf_url( 'index.html' ) ); ?>">HOME</a></li>
  <li><a href="<?php echo esc_url( mf_url( 'about.html' ) ); ?>">ABOUT</a></li>

  <!-- Standard Mattress Dropdown -->
  <li class="dropdown">
    <button class="dropbtn">Standard Mattress</button>
    <div class="dropdown-content">
      <a href="<?php echo esc_url( mf_url( 'delux-quilted-mattress.html' ) ); ?>">Delux Mattress</a>
      <a href="<?php echo esc_url( mf_url( 'tape-edge.html' ) ); ?>">Tape Edge Mattress</a>
      <a href="<?php echo esc_url( mf_url( 'open-end.html' ) ); ?>">Open End Mattress</a>
      <a href="<?php echo esc_url( mf_url( 'pvc.html' ) ); ?>">PVC Mattress</a>
    </div>
  </li>

  <!-- Premium Mattress Dropdown -->
  <li class="dropdown">
    <button class="dropbtn">Premium Mattress</button>
    <div class="dropdown-content">
      <a href="<?php echo esc_url( mf_url( 'Rebonded.html' ) ); ?>">Rebonded Mattress</a>
      <a href="<?php echo esc_url( mf_url( 'Orthopedic.html' ) ); ?>">Orthopedic Mattress</a>
    </div>
  </li>

        <li><a href="<?php echo esc_url( mf_url( 'beds.html' ) ); ?>">Beds</a></li>
  <!-- Beddings Dropdown -->
  <li class="dropdown">
    <button class="dropbtn">Beddings</button>
    <div class="dropdown-content">
      <a href="<?php echo esc_url( mf_url( 'pillow.html' ) ); ?>">Pillows</a>
      <a href="<?php echo esc_url( mf_url( 'topper.html' ) ); ?>">Toppers</a>
      <a href="<?php echo esc_url( mf_url( 'mattres-protectors.html' ) ); ?>">Mattress Protectors</a>
      <a href="<?php echo esc_url( mf_url( 'leather.html' ) ); ?>">Leather Cover</a>
    </div>
  </li>

  <!-- Sofas Dropdown -->
  <li class="dropdown">
    <button class="dropbtn">Sofas</button>
    <div class="dropdown-content">
      <a href="<?php echo esc_url( mf_url( 'sofas.html' ) ); ?>">Sofas</a>
      <a href="<?php echo esc_url( mf_url( 'cushions.html' ) ); ?>">Cushions & Foam Sheets</a>
    </div>
  </li>

        <li><a href="<?php echo esc_url( mf_url( 'spring.html' ) ); ?>">Spring Mattress</a></li>
  <li><a href="<?php echo esc_url( mf_url( 'updates/' ) ); ?>">NEWS</a></li>
  <li><a href="<?php echo esc_url( mf_url( 'contact.html' ) ); ?>">CONTACT</a></li>
</ul>
</nav>
      <button class="search-toggle" id="search-toggle" aria-label="Search the site">
        <i class="fas fa-search"></i>
      </button>
    </div>

  </div>
</header>
<div class="search-overlay" id="search-overlay">
  <div class="search-panel">
    <div class="search-input-row">
      <i class="fas fa-search"></i>
      <input type="text" id="search-input" placeholder="Search mattresses, beds, sofas, pillows..." autocomplete="off">
      <button class="search-close" id="search-close" aria-label="Close search"><i class="fas fa-times"></i></button>
    </div>
    <ul class="search-results" id="search-results"></ul>
  </div>
</div>
