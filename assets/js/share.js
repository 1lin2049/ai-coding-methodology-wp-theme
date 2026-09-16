(function () {
  'use strict';

  /* ═══════════════════════════════════════════════
     复制工具 · 优先 clipboard API，降级 execCommand
     ═══════════════════════════════════════════════ */
  async function copyText(text) {
    /* 1. 现代 API（需要 HTTPS + 页面聚焦） */
    if (navigator.clipboard && window.isSecureContext) {
      try {
        await navigator.clipboard.writeText(text);
        return true;
      } catch (e) { /* 继续降级 */ }
    }

    /* 2. 降级：execCommand */
    try {
      const ta = document.createElement('textarea');
      ta.value = text;
      ta.setAttribute('readonly', '');
      ta.style.position = 'fixed';
      ta.style.top = '-1000px';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.select();
      ta.setSelectionRange(0, text.length);
      const ok = document.execCommand('copy');
      document.body.removeChild(ta);
      return ok;
    } catch (e) {
      return false;
    }
  }

  /* ═══════════════════════════════════════════════
     1. 代码块复制按钮
     ═══════════════════════════════════════════════ */
  function initCodeCopy() {
    /* 主站：.entry-content pre；reader：.r-article pre */
    const blocks = document.querySelectorAll('.r-article pre, .entry-content pre');
    if (!blocks.length) return;

    blocks.forEach(pre => {
      if (pre.parentElement && pre.parentElement.classList.contains('code-wrap')) return;

      const wrap = document.createElement('div');
      wrap.className = 'code-wrap';
      pre.parentNode.insertBefore(wrap, pre);
      wrap.appendChild(pre);

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'code-copy';
      btn.setAttribute('aria-label', '复制代码');
      btn.innerHTML = '<span class="code-copy-label">复制</span>';

      btn.addEventListener('click', async (e) => {
        e.preventDefault();
        e.stopPropagation();

        const code = pre.querySelector('code')
          ? pre.querySelector('code').innerText
          : pre.innerText;

        const ok = await copyText(code);
        const label = btn.querySelector('.code-copy-label');

        if (ok) {
          btn.classList.add('copied');
          label.textContent = '已复制';
        } else {
          label.textContent = '失败';
        }
        setTimeout(() => {
          btn.classList.remove('copied');
          label.textContent = '复制';
        }, 1500);
      });

      wrap.appendChild(btn);
    });
  }

  /* ═══════════════════════════════════════════════
     2. 选中文本浮出「复制引用」
     ═══════════════════════════════════════════════ */
  function initSelectionQuote() {
    /* 主站：.entry-content；reader：.r-article */
    const containers = document.querySelectorAll('.r-article, .entry-content');
    if (!containers.length) return;

    const bubble = document.createElement('div');
    bubble.className = 'quote-bubble';
    bubble.hidden = true;
    bubble.innerHTML = '<span>复制引用</span>';
    document.body.appendChild(bubble);

    let selectionText = '';
    let lastRangeRect = null;

    function hide() {
      bubble.hidden = true;
      bubble.classList.remove('show');
    }

    function isInArticle(node) {
      for (let i = 0; i < containers.length; i++) {
        if (containers[i].contains(node)) return true;
      }
      return false;
    }

    document.addEventListener('selectionchange', () => {
      const sel = window.getSelection();
      if (!sel || sel.isCollapsed || sel.rangeCount === 0) {
        hide();
        return;
      }

      const text = sel.toString().trim();
      if (text.length < 5) {
        hide();
        return;
      }

      const range = sel.getRangeAt(0);
      if (!isInArticle(range.commonAncestorContainer)) {
        hide();
        return;
      }

      selectionText = text;
      lastRangeRect = range.getBoundingClientRect();

      /* ★ position: fixed 用视口坐标 */
      const top  = Math.max(60, lastRangeRect.top - 48);
      const left = lastRangeRect.left + lastRangeRect.width / 2;

      bubble.style.top  = top + 'px';
      bubble.style.left = left + 'px';
      bubble.hidden = false;
      requestAnimationFrame(() => bubble.classList.add('show'));
    });

    /* 点击气泡：复制 */
    bubble.addEventListener('mousedown', async (e) => {
      e.preventDefault();
      e.stopPropagation();

      const text = selectionText;
      if (!text) return;

      const quote = '> ' + text.split('\n').join('\n> ')
                  + '\n\n—— 摘自《' + document.title + '》'
                  + '\n' + location.href;

      const ok = await copyText(quote);
      const span = bubble.querySelector('span');

      if (ok) {
        span.textContent = '已复制';
      } else {
        span.textContent = '复制失败';
      }
      setTimeout(() => {
        span.textContent = '复制引用';
        hide();
      }, 900);
    });

    /* 点击其他地方隐藏 */
    document.addEventListener('mousedown', (e) => {
      if (!bubble.contains(e.target)) hide();
    });

    /* 滚动时隐藏（位置会错） */
    window.addEventListener('scroll', () => {
      if (!bubble.hidden) hide();
    }, { passive: true });
  }

  /* ═══════════════════════════════════════════════
     3. 分享按钮 · 章节底部 + 博客文章底部
     ═══════════════════════════════════════════════ */
  function initShareButton() {
    /* ★ 章节页：.r-end-actions；博客文章：.post-share */
    const boxes = document.querySelectorAll('.r-end-actions, .post-share');
    if (!boxes.length) return;

    boxes.forEach(box => {
      if (box.querySelector('.btn-share, .r-end-btn-share, .post-share-btn')) return;

      const isChapter = box.classList.contains('r-end-actions');

      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = isChapter
        ? 'r-end-btn r-end-btn-ghost r-end-btn-share'
        : 'btn btn-ghost post-share-btn';
      btn.innerHTML = isChapter
        ? '<span>分享本章</span>'
        : '<span class="prompt">$</span><span class="label">share --this</span>';

      btn.addEventListener('click', async (e) => {
        e.preventDefault();

        const labelEl = btn.querySelector('.label') || btn.querySelector('span:last-child');
        const original = labelEl ? labelEl.textContent : '';

        const data = {
          title: document.title,
          text: document.querySelector('.r-article-lead, .entry-content p')?.innerText || '',
          url: location.href,
        };

        /* 1. 优先 Web Share API */
        if (navigator.share) {
          try {
            await navigator.share(data);
            return;
          } catch (err) {
            /* 用户取消 → 静默退出 */
            if (err && err.name === 'AbortError') return;
            /* 其他错误 → 降级 */
          }
        }

        /* 2. 降级：复制链接 */
        const ok = await copyText(location.href);
        if (labelEl) {
          labelEl.textContent = ok ? '链接已复制' : '复制失败';
          setTimeout(() => { labelEl.textContent = original; }, 1500);
        }
      });

      box.appendChild(btn);
    });
  }

  /* ═══════════════════════════════════════════════
     初始化
     ═══════════════════════════════════════════════ */
  function init() {
    initCodeCopy();
    initSelectionQuote();
    initShareButton();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();