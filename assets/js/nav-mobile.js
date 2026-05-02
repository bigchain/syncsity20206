/* Mobile nav drawer for static (non-SPA) pages.
   Builds an accordion drawer from the desktop nav: plain links stay flat,
   .nav__has-menu wrappers become expandable sections with their sub-items
   pulled out of the (hidden-on-mobile) hover panel. */
(function () {
  'use strict';

  function el(tag, className, text) {
    var n = document.createElement(tag);
    if (className) n.className = className;
    if (text != null) n.textContent = text;
    return n;
  }

  /* Pull sub-items out of a .nav__menu hover panel and return them as
     plain {label, sub, href} records (sub = optional one-line description).
     Works for both card grids (Solutions/Transform/Resources) and list-style
     panels by inspecting any descendant <a>. Skips the trailing 'View All'
     footer link — that's added separately. */
  function extractSubItems(menuEl) {
    if (!menuEl) return [];
    var items = [];
    var anchors = menuEl.querySelectorAll('a[href]');
    Array.prototype.forEach.call(anchors, function (a) {
      // Skip footer view-all (lives inside .nav__menu__footer)
      if (a.closest('.nav__menu__footer')) return;
      var label = '';
      var titleEl = a.querySelector('h3, h4, .nav__menu__card__head h3');
      if (titleEl) label = (titleEl.textContent || '').trim().replace(/\s+/g, ' ');
      else label = (a.textContent || '').trim().split(/\s{2,}|\n/)[0].replace(/\s+/g, ' ');
      var subEl = a.querySelector('.nav__menu__card__tag, small');
      var sub = subEl ? (subEl.textContent || '').trim() : '';
      if (label) items.push({ label: label, sub: sub, href: a.getAttribute('href') });
    });
    var footer = menuEl.querySelector('.nav__menu__footer a');
    if (footer) {
      items.push({
        label: (footer.textContent || '').trim(),
        sub: '',
        href: footer.getAttribute('href'),
        isViewAll: true
      });
    }
    return items;
  }

  function buildSection(parentAnchor, subItems) {
    var section = el('div', 'nav__drawer__section');

    var head = el('button', 'nav__drawer__head');
    head.type = 'button';
    head.setAttribute('aria-expanded', 'false');
    var headLabel = el('span', 'nav__drawer__head__label', parentAnchor.textContent.trim());
    var headChev = el('span', 'nav__drawer__head__chev');
    headChev.setAttribute('aria-hidden', 'true');
    head.appendChild(headLabel);
    head.appendChild(headChev);

    var body = el('div', 'nav__drawer__body');
    body.setAttribute('aria-hidden', 'true');

    subItems.forEach(function (it) {
      var a = el('a', 'nav__drawer__sub' + (it.isViewAll ? ' nav__drawer__sub--all' : ''));
      a.href = it.href;
      a.appendChild(el('span', 'nav__drawer__sub__label', it.label));
      if (it.sub) a.appendChild(el('span', 'nav__drawer__sub__desc', it.sub));
      body.appendChild(a);
    });

    head.addEventListener('click', function () {
      var open = section.classList.toggle('is-open');
      head.setAttribute('aria-expanded', open ? 'true' : 'false');
      body.setAttribute('aria-hidden', open ? 'false' : 'true');
    });

    section.appendChild(head);
    section.appendChild(body);
    return section;
  }

  function buildDrawer(nav) {
    var links = nav.querySelector('.nav__links');
    var ctas  = nav.querySelector('.nav__cta');
    if (!links) return null;

    var drawer = el('div', 'nav__drawer');
    drawer.setAttribute('aria-hidden', 'true');
    /* Inline-style fallback so neither stale CSS nor cascade specificity
       can ever leave the drawer visible in flow. The CSS classes still
       apply for `.is-open` state — open() / close() update the inline
       style explicitly. */
    drawer.style.position      = 'fixed';
    drawer.style.inset         = '0';
    drawer.style.display       = 'none';
    drawer.style.visibility    = 'hidden';
    drawer.style.opacity       = '0';
    drawer.style.pointerEvents = 'none';
    drawer.style.zIndex        = '100';

    var inner = el('div', 'nav__drawer__inner');

    /* Walk only direct children of .nav__links so we don't pick up sub-anchors
       living inside the hover panels. */
    Array.prototype.forEach.call(links.children, function (child) {
      if (child.classList.contains('nav__has-menu')) {
        var parentAnchor = child.querySelector(':scope > a');
        var menuEl = child.querySelector(':scope > .nav__menu');
        if (!parentAnchor) return;
        var subItems = extractSubItems(menuEl);
        if (subItems.length === 0) {
          // No sub-items — just render the parent as a flat link
          var flat = parentAnchor.cloneNode(true);
          flat.classList.remove('has-chevron');
          flat.classList.add('nav__drawer__flat');
          inner.appendChild(flat);
        } else {
          inner.appendChild(buildSection(parentAnchor, subItems));
        }
      } else if (child.tagName === 'A') {
        var flat = child.cloneNode(true);
        flat.classList.remove('has-chevron');
        flat.classList.add('nav__drawer__flat');
        inner.appendChild(flat);
      }
    });

    if (ctas) {
      var ctaWrap = el('div', 'nav__drawer__ctas');
      Array.prototype.forEach.call(ctas.children, function (a) {
        if (a.tagName === 'A') ctaWrap.appendChild(a.cloneNode(true));
      });
      inner.appendChild(ctaWrap);
    }

    drawer.appendChild(inner);
    document.body.appendChild(drawer);
    return drawer;
  }

  function init() {
    var nav = document.querySelector('.nav');
    if (!nav) return;

    var burger = nav.querySelector('.nav__burger');
    if (!burger) {
      burger = el('button', 'nav__burger');
      burger.type = 'button';
      burger.setAttribute('aria-label', 'Open menu');
      burger.setAttribute('aria-expanded', 'false');
      burger.innerHTML = '<span></span>';
      var cta = nav.querySelector('.nav__cta');
      if (cta) cta.appendChild(burger);
      else nav.querySelector('.nav__inner').appendChild(burger);
    }

    var drawer = buildDrawer(nav);
    if (!drawer) return;

    function close() {
      burger.classList.remove('is-open');
      drawer.classList.remove('is-open');
      drawer.setAttribute('aria-hidden', 'true');
      burger.setAttribute('aria-expanded', 'false');
      burger.setAttribute('aria-label', 'Open menu');
      document.body.classList.remove('menu-open');
    }
    function open() {
      burger.classList.add('is-open');
      drawer.classList.add('is-open');
      drawer.setAttribute('aria-hidden', 'false');
      burger.setAttribute('aria-expanded', 'true');
      burger.setAttribute('aria-label', 'Close menu');
      document.body.classList.add('menu-open');
    }

    burger.addEventListener('click', function () {
      if (burger.classList.contains('is-open')) close(); else open();
    });
    drawer.addEventListener('click', function (e) {
      // Close when a leaf link is tapped (but not on accordion headers)
      var target = e.target.closest('a');
      if (target && !target.classList.contains('nav__drawer__head')) close();
      else if (e.target === drawer) close();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && burger.classList.contains('is-open')) close();
    });
    window.addEventListener('resize', function () {
      if (window.innerWidth >= 880 && burger.classList.contains('is-open')) close();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
