(function () {
  'use strict';

  var autoExpanded = false;

  /* ═══════════════════════════════════════════════
     1. 隐藏摘要面板里的统计文字
        匹配（含千分位逗号 + 分/分钟）：
        - "7,579 字，阅读时间 40 分。"
        - "7,579 字，阅读时间 40 分。上次编辑 一天 前。"
        - "7897 字，阅读时间 40 分钟。"
        - "字数: 7,579"（展开态）
        - "字符数: 7,979"（展开态）
        - "阅读时间: 40 分钟"（展开态）
     ═══════════════════════════════════════════════ */
  var PATTERNS = [
    /* 折叠态主行：含"字"+"阅读时间"+"分"（数字允许千分位逗号） */
    /[\d,]+\s*字[，,\s]+阅读时间\s*[\d,]+\s*分/,
    /* 展开态 */
    /^字数[:：]\s*[\d,]+$/,
    /^字符数[:：]\s*[\d,]+$/,
    /^阅读时间[:：]\s*[\d,]+\s*分[钟]?$/,
    /* 附加行："上次编辑 ... 前。" */
    /^上次编辑\s+.*\s*前[。.]?$/,
  ];

  function matchesAny(text) {
    for (var i = 0; i < PATTERNS.length; i++) {
      if (PATTERNS[i].test(text)) return true;
    }
    return false;
  }

  function hideStats() {
    var all = document.querySelectorAll('p, span, div, small');
    for (var i = 0; i < all.length; i++) {
      var el = all[i];
      /* 只处理叶子节点 */
      if (el.children.length > 0) continue;

      var text = (el.textContent || '').trim();
      if (!text) continue;

      /* 长度限制，避免误伤长段落 */
      if (text.length > 120) continue;

      if (matchesAny(text)) {
        el.style.display = 'none';
      }
    }
  }

  /* ═══════════════════════════════════════════════
     2. 强化"编辑摘要"按钮
     ═══════════════════════════════════════════════ */
  function enhanceButton() {
    var buttons = document.querySelectorAll('button');
    for (var i = 0; i < buttons.length; i++) {
      var btn = buttons[i];
      var text = btn.textContent.trim().replace(/^✎\s*/, '').replace(/^\s*/, '');
      if (text === '编辑摘要' && !btn.dataset.aiEnhanced) {
        btn.dataset.aiEnhanced = '1';

        /* 加图标 */
        if (!btn.querySelector('.ai-excerpt-icon')) {
          var icon = document.createElement('span');
          icon.className = 'ai-excerpt-icon';
          icon.textContent = '✎';
          icon.style.cssText = 'font-size:13px;margin-right:4px;';
          btn.insertBefore(icon, btn.firstChild);
        }

        /* 强化样式 */
        btn.style.cssText += ';'
          + 'display:inline-flex;align-items:center;'
          + 'padding:5px 12px;'
          + 'border:1px solid #2271b1;'
          + 'border-radius:3px;'
          + 'background:#f0f6fc;'
          + 'color:#2271b1;'
          + 'font-weight:500;'
          + 'font-size:13px;'
          + 'line-height:1.4;'
          + 'cursor:pointer;'
          + 'transition:all .15s;';

        btn.addEventListener('mouseenter', function () {
          this.style.background = '#2271b1';
          this.style.color = '#fff';
        });
        btn.addEventListener('mouseleave', function () {
          this.style.background = '#f0f6fc';
          this.style.color = '#2271b1';
        });
      }
    }
  }

  /* ═══════════════════════════════════════════════
     3. 自动展开摘要面板（仅一次）
     ═══════════════════════════════════════════════ */
  function autoExpand() {
    if (autoExpanded) return;
    var buttons = document.querySelectorAll('button');
    for (var i = 0; i < buttons.length; i++) {
      var btn = buttons[i];
      var text = btn.textContent.trim().replace(/^✎\s*/, '').replace(/^\s*/, '');
      if (text === '编辑摘要') {
        autoExpanded = true;
        return;   /* 不自动点击，只标记。保持面板折叠，让用户自己点 */
      }
    }
  }

  /* ═══════════════════════════════════════════════
     初始化
     ═══════════════════════════════════════════════ */
  function init() {
    hideStats();
    enhanceButton();
    autoExpand();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  /* DOM 变化时重新执行（限流） */
  var scheduled = false;
  new MutationObserver(function () {
    if (scheduled) return;
    scheduled = true;
    setTimeout(function () {
      scheduled = false;
      hideStats();
      enhanceButton();
    }, 200);
  }).observe(document.body, { childList: true, subtree: true });

  /* 兜底多次尝试 */
  setTimeout(init, 500);
  setTimeout(init, 1500);
  setTimeout(init, 3000);
})();