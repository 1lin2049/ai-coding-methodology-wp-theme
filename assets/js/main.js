(function () {
  'use strict';

  /* THEME */
  const root   = document.documentElement;
  const toggle = document.getElementById('themeToggle');
  const meta   = document.querySelector('meta[name="theme-color"]');

  function getTheme() {
    const s = localStorage.getItem('theme');
    if (s === 'light' || s === 'dark') return s;
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches) return 'light';
    return 'dark';
  }
  function setTheme(t) {
    root.setAttribute('data-theme', t);
    localStorage.setItem('theme', t);
    if (meta) meta.setAttribute('content', t === 'dark' ? '#08080a' : '#fafafa');
  }
  setTheme(getTheme());
  if (toggle) toggle.addEventListener('click', () => {
    setTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
  });

  /* NAV BURGER · 移动端菜单 */
  const burger = document.getElementById('navBurger');
  const panel  = document.getElementById('navPanel');
  if (burger && panel) {
    const setPanel = (open) => {
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
      burger.setAttribute('aria-label', open ? '关闭菜单' : '打开菜单');
      panel.classList.toggle('open', open);
      panel.setAttribute('aria-hidden', open ? 'false' : 'true');
    };
    burger.addEventListener('click', () => {
      setPanel(burger.getAttribute('aria-expanded') !== 'true');
    });
    panel.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setPanel(false)));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setPanel(false); });
    window.addEventListener('resize', () => { if (window.innerWidth > 1024) setPanel(false); });
  }

  /* SIDE NAV · TOC 生成 */
  const sideNav = document.getElementById('sideNav');
  let navItems  = sideNav ? Array.from(sideNav.querySelectorAll('.side-dot, .toc-item')) : [];
  const navMode = sideNav ? sideNav.dataset.mode : 'anchors';

  if (sideNav && navMode === 'toc') {
    const article = document.querySelector('.entry-content')
                 || document.querySelector('main article')
                 || document.querySelector('article.section');

    const headings = article ? article.querySelectorAll('h2, h3') : [];

    if (headings.length >= 2) {
      const track = document.createElement('span');
      track.className = 'toc-track';
      track.setAttribute('aria-hidden', 'true');
      sideNav.appendChild(track);

      headings.forEach((h, i) => {
        if (!h.id) h.id = 'section-' + i;
        const a = document.createElement('a');
        a.className = 'toc-item' + (h.tagName === 'H3' ? ' toc-item-h3' : '');
        a.href = '#' + h.id;
        const dot = document.createElement('span');
        dot.className = 'toc-dot';
        dot.setAttribute('aria-hidden', 'true');
        const text = document.createElement('span');
        text.className = 'toc-text';
        text.textContent = h.textContent.trim();
        a.appendChild(dot);
        a.appendChild(text);
        sideNav.appendChild(a);
      });

      navItems = Array.from(sideNav.querySelectorAll('.toc-item'));
      /* ★ 初始不显示 show，交给 scroll 决定 */
    } else {
      sideNav.remove();
    }
  }

  /* 浮动操作 */
  const floatActions = document.getElementById('floatActions');
  if (floatActions) {
    floatActions.querySelectorAll('.float-action[data-action]').forEach(btn => {
      btn.addEventListener('click', async () => {
        const action = btn.dataset.action;
        if (action === 'top') window.scrollTo({ top: 0, behavior: 'smooth' });
        if (action === 'share') {
          const shareData = { title: document.title, url: location.href };
          const label = btn.querySelector('.float-action-label');
          const original = label ? label.textContent : '';
          try {
            if (navigator.share) await navigator.share(shareData);
            else if (navigator.clipboard) {
              await navigator.clipboard.writeText(location.href);
              if (label) {
                label.textContent = '已复制';
                setTimeout(() => { label.textContent = original; }, 1500);
              }
            }
          } catch (e) {}
        }
      });
    });
  }

  /* NAV / PROGRESS */
  const nav      = document.getElementById('nav');
  const progress = document.getElementById('progress');
  const homeIds  = ['top','problem','paradigm','framework','project','assets','data','author','pricing','preview','faq'];
  const sections = homeIds.map(id => document.getElementById(id)).filter(Boolean);

  function getNavH() {
    const v = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--nav-h'));
    return isNaN(v) ? 56 : v;
  }

  function scrollTocIntoView(container, activeEl) {
    if (!container || !activeEl) return;
    const cRect = container.getBoundingClientRect();
    const aRect = activeEl.getBoundingClientRect();
    const pad = 16;
    if (aRect.top < cRect.top + pad) {
      container.scrollTop += (aRect.top - cRect.top) - pad;
    } else if (aRect.bottom > cRect.bottom - pad) {
      container.scrollTop += (aRect.bottom - cRect.bottom) + pad;
    }
  }

  /* active 判定：视口顶部第一个可见标题 */
  function resolveActiveIndex() {
    if (!navItems.length) return -1;

    const docH  = document.documentElement.scrollHeight;
    const winH  = window.innerHeight;
    const navH  = getNavH();
    const navBottom = navH + 1;

    if ((window.scrollY + winH) >= (docH - 4)) return navItems.length - 1;

    let lastPassed = -1;
    for (let i = 0; i < navItems.length; i++) {
      const href = navItems[i].getAttribute('href');
      if (!href) continue;
      const el = document.getElementById(href.slice(1));
      if (!el) continue;

      const rect = el.getBoundingClientRect();
      if (rect.top > winH) break;
      if (rect.bottom > navBottom) return i;
      lastPassed = i;
    }
    return lastPassed;
  }

  let displayedIdx = -1;
  let targetIdx    = -1;
  let animTimer    = null;
  let initialized  = false;

  function applyActive(idx, animateContainer) {
    navItems.forEach((item, i) => {
      item.classList.toggle('active', i === idx);
    });
    if (animateContainer && idx >= 0 && sideNav) {
      scrollTocIntoView(sideNav, navItems[idx]);
    }
  }

  function pushActive(newTarget) {
    const prevDir = targetIdx > displayedIdx ? 1 : (targetIdx < displayedIdx ? -1 : 0);
    targetIdx = newTarget;
    const newDir  = targetIdx > displayedIdx ? 1 : (targetIdx < displayedIdx ? -1 : 0);

    if (!initialized) {
      displayedIdx = targetIdx;
      initialized  = true;
      applyActive(displayedIdx, true);
      return;
    }
    if (displayedIdx === targetIdx) return;
    if (animTimer && prevDir !== newDir) {
      clearInterval(animTimer);
      animTimer = null;
    }
    if (animTimer) return;

    const stepMs = newDir < 0 ? 100 : 140;
    animTimer = setInterval(() => {
      if (displayedIdx === targetIdx) {
        clearInterval(animTimer);
        animTimer = null;
        if (displayedIdx >= 0 && sideNav) scrollTocIntoView(sideNav, navItems[displayedIdx]);
        return;
      }
      if (displayedIdx < targetIdx) displayedIdx++;
      else                          displayedIdx--;
      applyActive(displayedIdx, false);
    }, stepMs);
  }

  function onScroll() {
    const y         = window.scrollY;
    const docHeight = document.documentElement.scrollHeight;
    const winHeight = window.innerHeight;

    if (nav) nav.classList.toggle('scrolled', y > 16);
    const h = docHeight - winHeight;
    if (progress) progress.style.width = (h > 0 ? Math.min(100, (y / h) * 100) : 0) + '%';
    if (floatActions) floatActions.classList.toggle('show', y > 300);

    if (sideNav && navItems.length) {
      if (navMode === 'toc') {
        /* ★ 滚动超过 200px 才显示 TOC */
        sideNav.classList.toggle('show', y > 200);
        pushActive(resolveActiveIndex());
      } else {
        sideNav.classList.toggle('show', y > 400);
        let cur = null;
        sections.forEach(s => { if (y >= s.offsetTop - 130) cur = '#' + s.id; });
        const idx = navItems.findIndex(it => it.getAttribute('href') === cur);
        displayedIdx = idx;
        initialized  = true;
        applyActive(idx, true);
      }
    }

    document.querySelectorAll('.navlinks a, .nav-panel a').forEach(a => {
      const href = a.getAttribute('href') || '';
      if (!href.includes('#')) return;
      const hash = '#' + href.split('#')[1];
      const current = sections.slice().reverse().find(s => y >= s.offsetTop - 130);
      a.classList.toggle('active', current && hash === '#' + current.id);
    });
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll, { passive: true });
  onScroll();

  /* REVEAL */
  const io = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('on'); io.unobserve(e.target); }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -6% 0px' });
  document.querySelectorAll('.reveal').forEach(el => io.observe(el));

  /* COUNTER */
  const counters = document.querySelectorAll('[data-count]');
  const cIO = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (!e.isIntersecting) return;
      const el = e.target;
      const target = parseInt(el.getAttribute('data-count'), 10);
      const start = performance.now();
      function tick(now) {
        const t = Math.min(1, (now - start) / 1600);
        el.textContent = Math.round(target * (1 - Math.pow(1 - t, 3)));
        if (t < 1) requestAnimationFrame(tick);
        else {
          el.textContent = target;
          el.classList.add('flash');
          setTimeout(() => el.classList.remove('flash'), 800);
        }
      }
      requestAnimationFrame(tick);
      cIO.unobserve(el);
    });
  }, { threshold: 0.4 });
  counters.forEach(el => cIO.observe(el));

  /* KEYBOARD */
  document.addEventListener('keydown', (e) => {
    if (e.target.matches('input, textarea')) return;
    if (e.key === 't' && !e.metaKey && !e.ctrlKey && !e.altKey) {
      setTheme(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    }
    if (e.key === 'Home') window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();