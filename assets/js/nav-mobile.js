/* Mobile nav drawer for static (non-SPA) pages.
   Wires the .nav__burger button to .nav__drawer.
   The drawer markup is appended once on first run, mirroring nav__links + ctas. */
(function () {
  'use strict';

  function buildDrawer(nav) {
    var links = nav.querySelector('.nav__links');
    var ctas  = nav.querySelector('.nav__cta');
    if (!links) return null;

    var drawer = document.createElement('div');
    drawer.className = 'nav__drawer';
    drawer.setAttribute('aria-hidden', 'true');

    var inner = document.createElement('div');
    inner.className = 'nav__drawer__inner';

    Array.prototype.forEach.call(links.querySelectorAll('a'), function (a) {
      var clone = a.cloneNode(true);
      clone.classList.remove('has-chevron');
      inner.appendChild(clone);
    });

    if (ctas) {
      var ctaWrap = document.createElement('div');
      ctaWrap.className = 'nav__drawer__ctas';
      Array.prototype.forEach.call(ctas.querySelectorAll('a'), function (a) {
        ctaWrap.appendChild(a.cloneNode(true));
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
      burger = document.createElement('button');
      burger.type = 'button';
      burger.className = 'nav__burger';
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
      if (burger.classList.contains('is-open')) close();
      else open();
    });

    drawer.addEventListener('click', function (e) {
      if (e.target.tagName === 'A') close();
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
