(function () {
  'use strict';

  if (!document.body.classList.contains('r-preview-index')) return;

  var cards = document.querySelectorAll('.r-card[data-chapter-id]');
  if (!cards.length) return;

  var total     = cards.length;
  var readCount = 0;

  /* ═══════════════════════════════════════════════
     1. 读取已读状态
     ═══════════════════════════════════════════════ */
  cards.forEach(function (card) {
    var id = card.getAttribute('data-chapter-id');
    if (!id) return;

    var statusEl = card.querySelector('.r-card-status');
    try {
      if (localStorage.getItem('ai_read_chapter_' + id) === '1') {
        card.classList.add('is-read');
        if (statusEl) {
          statusEl.textContent = '✓';
          statusEl.setAttribute('data-status', 'read');
        }
        readCount++;
      }
    } catch (e) {}
  });

  /* ═══════════════════════════════════════════════
     2. 更新进度条
     ═══════════════════════════════════════════════ */
  var fillEl  = document.getElementById('previewProgressFill');
  var countEl = document.getElementById('previewProgressCount');

  function update() {
    var pct = total > 0 ? Math.round((readCount / total) * 100) : 0;
    if (fillEl)  fillEl.style.width = pct + '%';
    if (countEl) countEl.textContent = readCount + ' / ' + total + '（' + pct + '%）';
  }

  update();

  /* ═══════════════════════════════════════════════
     3. 点击卡片时预写标记
     ═══════════════════════════════════════════════ */
  cards.forEach(function (card) {
    card.addEventListener('click', function () {
      var id = card.getAttribute('data-chapter-id');
      if (!id) return;
      try {
        localStorage.setItem('ai_read_chapter_' + id, '1');
      } catch (e) {}
    });
  });
})();