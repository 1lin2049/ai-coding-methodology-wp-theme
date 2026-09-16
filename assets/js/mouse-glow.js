(function () {
  'use strict';

  /* 尊重"减少动效"偏好 */
  if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return;
  }

  /* 低端设备跳过 */
  var lowEnd = (navigator.hardwareConcurrency && navigator.hardwareConcurrency < 4)
            || (navigator.deviceMemory && navigator.deviceMemory < 4);
  if (lowEnd) return;

  /* 窄屏跳过 */
  if (window.innerWidth <= 1080) return;

  var glow = document.createElement('div');
  glow.className = 'mouse-glow';
  document.body.appendChild(glow);

  var targetX = window.innerWidth / 2;
  var targetY = window.innerHeight / 2;
  var curX = targetX;
  var curY = targetY;

  window.addEventListener('mousemove', function (e) {
    targetX = e.clientX;
    targetY = e.clientY;
  }, { passive: true });

  function loop() {
    curX += (targetX - curX) * 0.09;
    curY += (targetY - curY) * 0.09;
    glow.style.transform = 'translate(' + curX + 'px, ' + curY + 'px) translate(-50%, -50%)';
    requestAnimationFrame(loop);
  }
  loop();
})();