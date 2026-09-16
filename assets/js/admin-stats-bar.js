(function () {
  'use strict';

  var data = window.AI_STATS_BAR;
  if (!data) return;

  /* 数字格式化（≥10000 → X.X万） */
  function fmt(n) {
    n = parseInt(n, 10) || 0;
    if (n < 10000) return String(n);
    if (n < 1000000) {
      var v = Math.round(n / 1000) / 10;
      return (v % 1 === 0 ? v.toFixed(0) : v.toFixed(1)) + '万';
    }
    return Math.round(n / 10000) + '万';
  }

  /* 千分位 */
  function fmtGroup(n) {
    n = parseInt(n, 10) || 0;
    return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }

  function createBar() {
    var wrap = document.createElement('div');
    wrap.id = 'ai-stats-bar';
    wrap.setAttribute('aria-label', '内容统计');

    wrap.style.cssText = [
      'position: fixed',
      'right: 16px',
      'bottom: 0',
      'height: 32px',
      'display: flex',
      'align-items: center',
      'gap: 6px',
      'font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
      'font-size: 13px',
      'font-weight: 400',
      'line-height: 32px',
      'color: #1e1e1e',
      'user-select: none',
      'white-space: nowrap',
      'z-index: 100',
      'pointer-events: none',
    ].join(';');

    /* 数据项：label + value + 可选链接 */
    var items = [
      { label: '浏览',     value: fmt(data.views) },
      { label: '深度阅读', value: fmt(data.reads) },
      { label: '字数',     value: fmtGroup(data.words) },
      { label: '预计阅读', value: data.minutes + ' 分钟' },
      { label: '评论',     value: fmt(data.comments), url: data.commentsUrl || '' },
    ];

    items.forEach(function (item, i) {
      if (i > 0) {
        var sep = document.createElement('span');
        sep.textContent = '·';
        sep.style.opacity = '.5';
        sep.style.margin = '0 2px';
        sep.setAttribute('aria-hidden', 'true');
        wrap.appendChild(sep);
      }

      var span;
      if (item.url) {
        /* 可点击的评论链接 */
        span = document.createElement('a');
        span.href = item.url;
        span.style.cssText = [
          'color: inherit',
          'text-decoration: none',
          'border-bottom: 1px dashed currentColor',
          'pointer-events: auto',
          'cursor: pointer',
          'padding-bottom: 1px',
        ].join(';');
        span.addEventListener('mouseenter', function () {
          span.style.color = '#2271b1';
          span.style.borderBottomStyle = 'solid';
        });
        span.addEventListener('mouseleave', function () {
          span.style.color = '';
          span.style.borderBottomStyle = 'dashed';
        });
      } else {
        span = document.createElement('span');
      }

      span.textContent = item.label + ' ' + item.value;
      wrap.appendChild(span);
    });

    return wrap;
  }

  function inject() {
    if (document.getElementById('ai-stats-bar')) return;
    if (!document.querySelector('.interface-interface-skeleton__footer')) return;
    document.body.appendChild(createBar());
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inject);
  } else {
    inject();
  }

  setTimeout(inject, 800);
  setTimeout(inject, 2000);
})();