(function () {
  'use strict';

  const cfg = window.AI_TRACK || {};
  const ajaxUrl = cfg.ajaxUrl;
  const postId  = cfg.postId;
  if (!ajaxUrl || !postId) return;

  function getVisitorId() {
    let id = null;
    try { id = localStorage.getItem('ai_vid'); } catch (e) {}
    if (id) return id;
    try {
      if (window.crypto && crypto.randomUUID) {
        id = crypto.randomUUID().replace(/-/g, '');
      } else {
        id = 'v' + Date.now().toString(36) + Math.random().toString(36).slice(2, 12);
      }
      localStorage.setItem('ai_vid', id);
    } catch (e) {
      id = 'tmp' + Math.random().toString(36).slice(2, 12);
    }
    return id;
  }

  const visitorId = getVisitorId();

  function send(event, value) {
    const params = new URLSearchParams();
    params.append('action',     'ai_track');
    params.append('post_id',    String(postId));
    params.append('event',      event);
    params.append('visitor_id', visitorId);
    if (value !== undefined && value !== null && value !== '') {
      params.append('value', String(value));
    }

    if (navigator.sendBeacon) {
      navigator.sendBeacon(ajaxUrl, params);
    } else {
      fetch(ajaxUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' },
        body: params.toString(),
        keepalive: true,
      }).catch(function () {});
    }
  }

  /* 1. 浏览（PV · 后端 60 秒防抖） */
  send('view');

  /* 2. 阅读（UV · 停留 15 秒 或 滚动 25%） */
  let readSent = false;
  function markRead() {
    if (readSent) return;
    readSent = true;
    send('read');
  }

  setTimeout(markRead, 15000);

  function onScrollCheck() {
    const docH = document.documentElement.scrollHeight;
    const winH = window.innerHeight;
    const max  = docH - winH;
    if (max <= 0) { markRead(); return; }
    const p = (window.scrollY / max) * 100;
    if (p >= 25) markRead();
  }
  window.addEventListener('scroll', onScrollCheck, { passive: true });

  /* 3. 进度点 */
  const milestones = [25, 50, 75, 100];
  const reported   = new Set();

  function checkProgress() {
    const docH = document.documentElement.scrollHeight;
    const winH = window.innerHeight;
    const max  = docH - winH;

    if (max <= 0) {
      if (!reported.has(100)) { reported.add(100); send('progress', '100'); }
      return;
    }

    const p = Math.min(100, Math.max(0, Math.round((window.scrollY / max) * 100)));
    milestones.forEach(function (m) {
      if (p >= m && !reported.has(m)) {
        reported.add(m);
        send('progress', String(m));
      }
    });
  }

  let progressTimer = null;
  window.addEventListener('scroll', function () {
    if (progressTimer) return;
    progressTimer = setTimeout(function () {
      checkProgress();
      progressTimer = null;
    }, 300);
  }, { passive: true });

  checkProgress();

  /* 4. 停留时长（15 秒批量上报；单次封顶 300s，避免后端拒绝 / 长会话拆分） */
  const TIME_MAX = 300;

  let accumulated = 0;
  let lastTick    = Date.now();
  let visible     = !document.hidden;

  function recordTime() {
    const now = Date.now();
    const delta = Math.round((now - lastTick) / 1000);
    lastTick = now;
    if (delta > 0) accumulated += delta;
    return delta;
  }

  function sendTime(force) {
    if (accumulated < 1) return;
    if (force === 'hide') {
      /* 切后台：仅当达到批量门槛才上报，避免碎片 */
      if (accumulated < 15) return;
    }
    send('time', String(Math.min(accumulated, TIME_MAX)));
    accumulated = 0;
  }

  setInterval(function () {
    if (!visible) return;
    recordTime();
    if (accumulated >= 15) sendTime();
  }, 5000);

  document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
      if (!visible) return;
      visible = false;
      recordTime();
      sendTime('hide');
    } else {
      visible = true;
      lastTick = Date.now();
    }
  });

  window.addEventListener('pagehide', function () {
    if (visible) recordTime();
    if (accumulated >= 3) send('time', String(Math.min(accumulated, TIME_MAX)));
    accumulated = 0;
  });
})();