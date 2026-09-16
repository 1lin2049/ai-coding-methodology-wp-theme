(function () {
  'use strict';

  /* 出现任一即可触发 */
  var PATTERNS = [/^字符数[:：]/, /^字数[:：]/, /^阅读时间[:：]/];

  /* 判断元素文本是否匹配 */
  function isStatNode(el) {
    if (!el || el.children.length > 0) return false;
    var text = (el.textContent || '').trim();
    for (var i = 0; i < PATTERNS.length; i++) {
      if (PATTERNS[i].test(text)) return true;
    }
    return false;
  }

  /* 找共同祖先 */
  function findCommonAncestor(elements) {
    if (!elements.length) return null;
    var common = elements[0].parentElement;
    while (common && common !== document.body) {
      var all = true;
      for (var i = 0; i < elements.length; i++) {
        if (!common.contains(elements[i])) { all = false; break; }
      }
      if (all) return common;
      common = common.parentElement;
    }
    return null;
  }

  function hide() {
    /* 已经在原生的父级上标记过 */
    if (document.body.getAttribute('data-ai-native-stats-hidden')) return;

    /* 遍历所有元素，找到所有含"字符数/字数/阅读时间"的叶子 */
    var all = document.querySelectorAll('*');
    var hits = [];

    for (var i = 0; i < all.length; i++) {
      if (isStatNode(all[i])) hits.push(all[i]);
    }

    /* 至少要命中两个，避免误伤 */
    if (hits.length < 2) return;

    /* 找共同祖先 */
    var ancestor = findCommonAncestor(hits);
    if (!ancestor) return;

    /* 直接隐藏共同祖先，但不要隐藏整个面板容器 */
    var target = ancestor;
    /* 往上一级：可能是 .components-panel__body 或列表容器 */
    var parent = target.parentElement;
    if (parent && parent.classList) {
      /* 如果父级有 panel-body 类名，用它 */
      if (parent.classList.contains('components-panel__body')
          || parent.classList.contains('components-panel')) {
        target = parent;
      }
    }

    target.style.display = 'none';
    document.body.setAttribute('data-ai-native-stats-hidden', '1');
  }

  function start() {
    hide();

    /* 监听侧边栏打开等变化 */
    var observer = new MutationObserver(function () {
      hide();
    });
    observer.observe(document.body, { childList: true, subtree: true });

    /* 兜底：3 秒内多次尝试 */
    setTimeout(hide, 500);
    setTimeout(hide, 1500);
    setTimeout(hide, 3000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();