(function () {
  'use strict';

  /* ★ 支持 reader 章节页 + 主站文章/页面 */
  const isReader  = document.body.classList.contains('r-reader');
  const contentEl = document.querySelector(isReader ? '.r-article' : '.entry-content');
  if (!contentEl) return;

  /* ★ 动态获取/创建提示条 */
  let resumePrompt = document.getElementById('rResumePrompt');
  if (!resumePrompt) {
    resumePrompt = document.createElement('div');
    resumePrompt.id = 'rResumePrompt';
    resumePrompt.className = 'r-resume-prompt';
    resumePrompt.hidden = true;
    resumePrompt.setAttribute('role', 'status');
    resumePrompt.setAttribute('aria-live', 'polite');
    resumePrompt.innerHTML = `
      <span class="r-resume-text"></span>
      <button type="button" class="r-resume-continue">继续阅读</button>
      <button type="button" class="r-resume-restart">从头开始</button>
    `;
    document.body.appendChild(resumePrompt);
  }

  /* slug */
  const slugMeta = document.querySelector('meta[name="ai-chapter-slug"]');
  const slug = slugMeta
    ? slugMeta.content
    : (document.body.className.match(/postid-(\d+)/) || [])[1]
      || location.pathname.replace(/\/+$/, '').split('/').pop();
  if (!slug) return;

  const KEY = 'ai_read_pos_' + slug;
  const MIN_RESUME = 0.10;

  /* 恢复位置 */
  let saved = null;
  try {
    const raw = localStorage.getItem(KEY);
    if (raw) saved = JSON.parse(raw);
  } catch (e) { saved = null; }

  if (saved && saved.p >= MIN_RESUME && saved.p < 0.98) {
    const pct = Math.round(saved.p * 100);
    resumePrompt.hidden = false;
    resumePrompt.querySelector('.r-resume-text').textContent =
      '上次读到 ' + pct + '% · ' + (saved.t || '');
    resumePrompt.classList.add('show');

    const targetScroll = (document.documentElement.scrollHeight - window.innerHeight) * saved.p;

    resumePrompt.querySelector('.r-resume-continue').addEventListener('click', function (e) {
      e.preventDefault();
      window.scrollTo({ top: targetScroll, behavior: 'smooth' });
      hidePrompt();
    });

    resumePrompt.querySelector('.r-resume-restart').addEventListener('click', function (e) {
      e.preventDefault();
      try { localStorage.removeItem(KEY); } catch (err) {}
      window.scrollTo({ top: 0, behavior: 'smooth' });
      hidePrompt();
    });

    const autoHide = setTimeout(hidePrompt, 12000);

    function hidePrompt() {
      clearTimeout(autoHide);
      resumePrompt.classList.remove('show');
      setTimeout(() => { resumePrompt.hidden = true; }, 300);
    }

    let scrolled = false;
    window.addEventListener('scroll', function once() {
      if (scrolled) return;
      if (window.scrollY > 50) {
        scrolled = true;
        hidePrompt();
        window.removeEventListener('scroll', once);
      }
    }, { passive: true });
  }

  /* 保存位置 */
  let saveTimer = null;
  function save() {
    const docH = document.documentElement.scrollHeight;
    const winH = window.innerHeight;
    const max  = docH - winH;
    if (max <= 0) return;

    const p = Math.min(1, Math.max(0, window.scrollY / max));
    const t = new Date().toLocaleDateString('zh-CN', { month: 'numeric', day: 'numeric' });

    try {
      localStorage.setItem(KEY, JSON.stringify({ p: p, t: t }));
    } catch (e) {}
  }

  function scheduleSave() {
    if (saveTimer) return;
    saveTimer = setTimeout(() => {
      save();
      saveTimer = null;
    }, 800);
  }

  window.addEventListener('scroll', scheduleSave, { passive: true });
  window.addEventListener('beforeunload', save);
})();