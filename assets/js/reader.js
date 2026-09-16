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

  /* 阅读进度 */
  const bar     = document.getElementById('rProgressBar');
  const txt     = document.getElementById('rProgressTxt');
  const article = document.querySelector('.r-article');

  if (bar && article) {
    function update() {
      const docTop    = article.getBoundingClientRect().top + window.scrollY;
      const docHeight = article.scrollHeight;
      const vh        = window.innerHeight;
      const scrolled  = Math.max(0, window.scrollY - docTop + vh * 0.3);
      const total     = Math.max(docHeight - vh * 0.5, 1);
      const p         = Math.min(1, scrolled / total);
      bar.style.width = (p * 100) + '%';
      if (txt) txt.textContent = '完成 ' + Math.round(p * 100) + '%';
    }
    window.addEventListener('scroll', update, { passive: true });
    window.addEventListener('resize', update, { passive: true });
    update();
  }

  /* TOC · 章节页 */
  const rToc = document.getElementById('rToc');
  if (rToc && article) {
    const headings = article.querySelectorAll('h2, h3');

    if (headings.length >= 2) {
      const track = document.createElement('span');
      track.className = 'r-toc-track';
      track.setAttribute('aria-hidden', 'true');
      rToc.appendChild(track);

      headings.forEach((h, i) => {
        if (!h.id) h.id = 'ch-section-' + i;
        const a = document.createElement('a');
        a.className = 'r-toc-item' + (h.tagName === 'H3' ? ' r-toc-item-h3' : '');
        a.href = '#' + h.id;
        const dot = document.createElement('span');
        dot.className = 'r-toc-dot';
        dot.setAttribute('aria-hidden', 'true');
        const text = document.createElement('span');
        text.className = 'r-toc-text';
        text.textContent = h.textContent.trim();
        a.appendChild(dot);
        a.appendChild(text);
        rToc.appendChild(a);
      });

      const items = Array.from(rToc.querySelectorAll('.r-toc-item'));

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

      function resolveActiveIndex() {
        if (!items.length) return -1;

        const docH  = document.documentElement.scrollHeight;
        const winH  = window.innerHeight;
        const navH  = getNavH();
        const navBottom = navH + 1;

        if ((window.scrollY + winH) >= (docH - 4)) return items.length - 1;

        let lastPassed = -1;
        for (let i = 0; i < items.length; i++) {
          const href = items[i].getAttribute('href');
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
        items.forEach((item, i) => {
          item.classList.toggle('active', i === idx);
        });
        if (animateContainer && idx >= 0) {
          scrollTocIntoView(rToc, items[idx]);
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
            if (displayedIdx >= 0) scrollTocIntoView(rToc, items[displayedIdx]);
            return;
          }
          if (displayedIdx < targetIdx) displayedIdx++;
          else                          displayedIdx--;
          applyActive(displayedIdx, false);
        }, stepMs);
      }

      function updateToc() {
        /* ★ 滚动超过 200px 才显示 TOC */
        rToc.classList.toggle('show', window.scrollY > 200);
        pushActive(resolveActiveIndex());
      }

      window.addEventListener('scroll', updateToc, { passive: true });
      window.addEventListener('resize', updateToc, { passive: true });
      updateToc();
    } else {
      rToc.remove();
    }
  }
})();