document.addEventListener('DOMContentLoaded', () => {
    // Modal functionality
    const productModal = document.getElementById('productModal');
    const closeBtn = document.querySelector('.close-btn');
    const chooseOptionsBtns = document.querySelectorAll('.choose-options-btn');
    const sizeOptions = document.querySelectorAll('.size-option');

    // Open the modal when "Choose options" button is clicked.
    // Previously this just showed the modal with whatever image/title was
    // hardcoded in the HTML, regardless of which thumbnail the visitor
    // actually clicked - every "Choose option" on a page led to the same
    // product in the modal. Now it copies the clicked thumbnail's own
    // image/title into the modal first.
    const modalImage = document.getElementById('modalImage');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    chooseOptionsBtns.forEach((btn) => {
        btn.addEventListener('click', () => {
            const card = btn.closest('.thumbnail-card');
            if (card) {
                const cardImg = card.querySelector('img');
                const cardTitle = card.querySelector('.thumbnail-title');
                if (modalImage && cardImg) {
                    modalImage.src = cardImg.getAttribute('src');
                    modalImage.alt = cardImg.getAttribute('alt') || (cardTitle ? cardTitle.textContent.trim() : '');
                }
                if (modalTitle && cardTitle) {
                    modalTitle.textContent = cardTitle.textContent.trim();
                }
            }
            productModal.style.display = 'block';
        });
    });

    // Close the modal when the close button is clicked
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            productModal.style.display = 'none';
        });
    }

    // Close the modal if clicked outside the modal content
    window.addEventListener('click', (e) => {
        if (e.target === productModal) {
            productModal.style.display = 'none';
        }
    });

    // Handle size selection
    sizeOptions.forEach((button) => {
        button.addEventListener('click', () => {
            sizeOptions.forEach(btn => btn.classList.remove('selected'));
            button.classList.add('selected');
        });
    });

    // === Hamburger menu toggle (for mobile) ===
    const hamburger = document.getElementById('hamburger-menu');
    const navLinks = document.querySelector('.nav-links');

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', (e) => {
            e.stopPropagation();
            navLinks.classList.toggle('open');
        });
    }

    // Close nav when a link is clicked
    navLinks?.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navLinks.classList.remove('open');
      });
    });

    // Close nav when click outside
    document.addEventListener('click', (e) => {
      if (navLinks.classList.contains('open') && !navLinks.contains(e.target) && !hamburger.contains(e.target)) {
        navLinks.classList.remove('open');
      }
    });

    // === Dropdown menus (Standard/Premium Mattress, Beddings, Sofas, etc.) ===
    // Previously duplicated inline in every page's <head>; consolidated here.
    const dropdownButtons = document.querySelectorAll('.dropbtn');
    dropdownButtons.forEach(button => {
      button.addEventListener('click', function (e) {
        e.stopPropagation();
        const dropdown = this.parentElement;
        const dropdownContent = dropdown.querySelector('.dropdown-content');

        // Close other open dropdowns
        document.querySelectorAll('.dropdown-content').forEach(dc => {
          if (dc !== dropdownContent) {
            dc.style.display = 'none';
          }
        });

        // Toggle this dropdown
        dropdownContent.style.display =
          dropdownContent.style.display === 'block' ? 'none' : 'block';
      });
    });

    // Close all dropdowns on outside click
    document.addEventListener('click', function () {
      document.querySelectorAll('.dropdown-content').forEach(dc => {
        dc.style.display = 'none';
      });
    });

    // === Scroll reveal: staggered entrance animations for text/card
    // content, applied site-wide from here rather than per-page markup
    // so every page (not just the ones with bespoke JS) gets them.
    // Replaces what used to be three separate, near-identical
    // scroll-listener blocks (institutional gallery, goals row, blog
    // cards), each polling on every scroll event, with one shared
    // IntersectionObserver and a reusable .reveal-* class vocabulary
    // (see the CSS "Scroll reveal" block in style.css). ===
    const revealGroups = [
      // Whole-block fade-up, no stagger: one settled entrance per section.
      { selector: '.hero-text, .about-content, .contact-hero .container, section > .container > h2, .goals h2, .institutional h2, .blog h2, .products h2, h1.product-card2', anim: 'reveal-up', stagger: 0 },
      // Card/list groups: cascade in one after another.
      { selector: '.cards-container .card, .info-grid > div, .about-feature-card, .testimonials blockquote, .thumbnail-card, .goals-content, .goals-image, .institutional-content, .blog-preview article, .product-card', anim: 'reveal-up', stagger: 90 },
      { selector: '.institutional-gallery figure', anim: 'reveal-zoom', stagger: 80 },
      { selector: '.contact-detail-item', anim: 'reveal-left', stagger: 90 },
      { selector: '.contact-form-wrap', anim: 'reveal-right', stagger: 0 },
    ];

    const revealTargets = new Set();
    revealGroups.forEach(({ selector, anim, stagger }) => {
      document.querySelectorAll(selector).forEach((el, i) => {
        if (revealTargets.has(el)) return; // a selector overlap shouldn't double-tag an element
        revealTargets.add(el);
        el.classList.add(anim);
        if (stagger) el.style.transitionDelay = Math.min(i * stagger, 480) + 'ms';
      });
    });

    if (revealTargets.size) {
      if ('IntersectionObserver' in window) {
        const revealObserver = new IntersectionObserver((entries) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              entry.target.classList.add('is-visible');
              revealObserver.unobserve(entry.target);
            }
          });
        }, { threshold: 0.15, rootMargin: '0px 0px -60px 0px' });
        // Starting the observer on the very next tick can fire its first
        // callback before the browser has painted the opacity:0 starting
        // state even once - for anything already in view on load (the
        // homepage's category cards, hero-adjacent content), that means
        // "is-visible" lands before there's ever a hidden frame to
        // transition FROM, so it just appears instantly with no visible
        // fade. A double rAF guarantees at least one paint happens first,
        // so the entrance animation actually plays instead of being
        // invisible on exactly the content most people see first.
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            revealTargets.forEach(el => revealObserver.observe(el));
          });
        });
      } else {
        revealTargets.forEach(el => el.classList.add('is-visible'));
      }
    }

    // === Hero word-by-word intro (homepage only) ===
    // Splits the eyebrow/heading/description into individual words, flies
    // each one in from the left in sequence, holds the fully-assembled
    // text for a few seconds, then makes every word disappear together in
    // one instant (not word by word) and keeps it away for the rest of the
    // hero's 64s image-crossfade cycle (see .hero .img16's animation-delay
    // in style.css) before the whole sequence repeats - so the text never
    // comes back until every rotating image has had its turn.
    const heroText = document.querySelector('.hero .hero-text');
    if (heroText) {
      const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const wordTargets = [
        heroText.querySelector('.hero-eyebrow'),
        heroText.querySelector('h1'),
        heroText.querySelector('.hero-desc'),
      ].filter(Boolean);

      const words = [];
      wordTargets.forEach(el => {
        const text = el.textContent.trim();
        el.textContent = '';
        text.split(/\s+/).forEach((word, i) => {
          if (i > 0) el.appendChild(document.createTextNode(' '));
          const span = document.createElement('span');
          span.className = 'hero-word';
          span.textContent = word;
          el.appendChild(span);
          words.push(span);
        });
      });

      if (prefersReducedMotion || !words.length) {
        words.forEach(w => w.classList.add('in'));
      } else {
        const WORD_STEP_MS = 90;
        const HOLD_MS = 2500;
        const FULL_CYCLE_MS = 64000; // matches .hero img's 64s crossfade animation

        const runCycle = () => {
          words.forEach((w, i) => {
            setTimeout(() => w.classList.add('in'), i * WORD_STEP_MS);
          });
          const disappearAt = words.length * WORD_STEP_MS + HOLD_MS;
          setTimeout(() => {
            words.forEach(w => w.classList.remove('in'));
          }, disappearAt);
          setTimeout(runCycle, FULL_CYCLE_MS);
        };
        runCycle();
      }
    }

    // === Contact form (static site, no backend - hands off to WhatsApp) ===
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
      contactForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const name = document.getElementById('cf-name').value.trim();
        const phone = document.getElementById('cf-phone').value.trim();
        const message = document.getElementById('cf-message').value.trim();
        const text = `Hello Mood Foam, my name is ${name} (${phone}).\n\n${message}`;
        window.open('https://wa.me/256743053096?text=' + encodeURIComponent(text), '_blank', 'noopener');
      });
    }

    // === Gallery lightbox (showroom page) ===
    const lightbox = document.getElementById('lightbox');
    if (lightbox) {
      const lightboxImg = document.getElementById('lightbox-img');
      const lightboxCaption = document.getElementById('lightbox-caption');
      const closeBtn = lightbox.querySelector('.lightbox-close');

      const openLightboxFor = (card) => {
        const full = card.getAttribute('data-full');
        const caption = card.getAttribute('data-caption');
        lightboxImg.src = full;
        lightboxImg.alt = caption;
        lightboxCaption.textContent = caption;
        lightbox.classList.add('open');
      };
      document.querySelectorAll('.gallery-card').forEach(card => {
        card.addEventListener('click', (e) => {
          if (e.target.closest('a')) return; // let "shop this category" links work normally
          openLightboxFor(card);
        });
        // Keyboard support (cards are focusable via tabindex/role="button")
        card.addEventListener('keydown', (e) => {
          if (e.target.closest('a')) return;
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            openLightboxFor(card);
          }
        });
      });

      const closeLightbox = () => lightbox.classList.remove('open');
      closeBtn.addEventListener('click', closeLightbox);
      lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) closeLightbox();
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
      });
    }
});
