(function () {
  'use strict';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ═══════════════════════════════════════════════
     1. 终端逐行显现
     ═══════════════════════════════════════════════ */
  function revealTerminal() {
    var term = document.querySelector('.hero-term');
    if (!term) return;
    if (term.classList.contains('revealed')) return;

    var lines = term.querySelectorAll('.line');
    if (!lines.length) return;

    var step = Math.min(0.06, 1.2 / lines.length);
    for (var i = 0; i < lines.length; i++) {
      lines[i].style.transitionDelay = (i * step) + 's';
    }

    requestAnimationFrame(function () {
      term.classList.add('revealed');
    });
  }

  /* ═══════════════════════════════════════════════
     2. 终端等待光标
     ═══════════════════════════════════════════════ */
  function addWaitingCursor() {
    var term = document.querySelector('.hero-term');
    if (!term) return;
    var body = term.querySelector('.term-body');
    if (!body) return;
    if (body.querySelector('.cursor-final')) return;

    var line = document.createElement('div');
    line.className = 'line cursor-final';
    line.innerHTML = '<span class="p">$</span><span class="cursor-line"></span>';
    line.style.opacity = '0';
    line.style.transition = 'opacity .5s cubic-bezier(.16,1,.3,1)';
    line.style.transitionDelay = '1.4s';
    body.appendChild(line);

    setTimeout(function () { line.style.opacity = '1'; }, 50);
  }

  /* ═══════════════════════════════════════════════
     3. section-tag 命令脉冲
     ═══════════════════════════════════════════════ */
  function initSectionTagPulse() {
    var tags = document.querySelectorAll('.section-tag');
    if (!tags.length) return;

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) {
        if (!e.isIntersecting) return;
        var prompt = e.target.querySelector('.prompt');
        if (prompt) {
          prompt.classList.add('pulse-once');
          setTimeout(function () {
            prompt.classList.remove('pulse-once');
          }, 900);
        }
        io.unobserve(e.target);
      });
    }, { threshold: 0.6, rootMargin: '0px 0px -10% 0px' });

    tags.forEach(function (t) { io.observe(t); });
  }

  /* ═══════════════════════════════════════════════
     4. 初始化
     ★ 立即执行，不依赖 DOMContentLoaded
     ★ reduced-motion 时强制显示终端
     ★ 1.5s 兜底：如果还没显示，强制显示
     ═══════════════════════════════════════════════ */
  function init() {
    if (reduced) {
      var term = document.querySelector('.hero-term');
      if (term) term.classList.add('revealed');
      return;
    }

    revealTerminal();
    addWaitingCursor();
    initSectionTagPulse();
  }

  /* 立即尝试执行；如果 DOM 还没解析完，等 DOMContentLoaded */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  /* ★ 兜底：1.5 秒后如果终端还没显示，强制显示 */
  setTimeout(function () {
    var term = document.querySelector('.hero-term');
    if (term && !term.classList.contains('revealed')) {
      term.classList.add('revealed');
    }
  }, 1500);

  /* ★ 二次兜底：窗口 load 后如果还没显示，再试一次 */
  window.addEventListener('load', function () {
    var term = document.querySelector('.hero-term');
    if (term && !term.classList.contains('revealed')) {
      term.classList.add('revealed');
    }
  });
})();