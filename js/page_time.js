// Время на странице → параметры визита Яндекс Метрики (page_time → раздел → slug → секунды)
// Значение может прийти несколькими частями (при каждом скрытии вкладки),
// поэтому в отчёте смотреть сумму параметра и делить на просмотры страницы
(function () {
  var COUNTER = 62605987;
  var MAX_SEC = 1800;    // больше 30 минут за раз не засчитываем (забытые вкладки)
  var IDLE_MS = 120000;  // нет активности 2 минуты — счёт на паузе

  // Какие страницы считаем
  function getPage(path) {
    path = path.replace(/\/+$/, '') || '/';
    var m;
    if ((m = path.match(/^\/projects\/([^\/]+)$/))) return ['projects', m[1]];
    if ((m = path.match(/^\/finished\/([^\/]+)$/))) return ['finished', m[1]];
    if ((m = path.match(/^\/services\/([^\/]+)$/))) return ['services', m[1]];
    if (path === '/ipoteka') return ['ipoteka', 'ipoteka'];
    return null;
  }

  var page = getPage(location.pathname);
  if (!page) return;

  var active = 0, startedAt = null, lastActivity = Date.now();

  function pause() {
    if (startedAt !== null) { active += Date.now() - startedAt; startedAt = null; }
  }
  function resume() {
    if (startedAt === null && !document.hidden) startedAt = Date.now();
  }

  // Отправка накопленного времени в Метрику
  function flush() {
    pause();
    var sec = Math.min(Math.round(active / 1000), MAX_SEC);
    if (sec >= 1 && typeof ym === 'function') {
      var p = { page_time: {} };
      p.page_time[page[0]] = {};
      p.page_time[page[0]][page[1]] = sec;
      ym(COUNTER, 'params', p);
    }
    active = 0;
  }

  // Активность пользователя
  ['mousemove', 'scroll', 'keydown', 'touchstart', 'click'].forEach(function (e) {
    window.addEventListener(e, function () { lastActivity = Date.now(); resume(); }, { passive: true });
  });

  // Пауза при бездействии
  setInterval(function () {
    if (startedAt !== null && Date.now() - lastActivity > IDLE_MS) pause();
  }, 5000);

  // Вкладка скрыта — отправляем, вернулись — продолжаем
  document.addEventListener('visibilitychange', function () {
    if (document.hidden) { flush(); }
    else { lastActivity = Date.now(); resume(); }
  });
  window.addEventListener('pagehide', flush);

  resume();
})();
