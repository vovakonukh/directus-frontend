// Избранное без аккаунтов: ID проектов и домов хранятся в cookie fav_projects / fav_finished
// (например «12.45.78»). Cookie, а не localStorage — чтобы PHP мог отрисовать страницу /favorites.
// Подключается до Alpine на всех страницах.
(function () {
    var MAX_AGE = 60 * 60 * 24 * 365; // год

    function read(name) {
        var m = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
        return m ? m[1].split('.').map(Number).filter(Boolean) : [];
    }

    function write(name, ids) {
        document.cookie = name + '=' + ids.join('.') + '; path=/; max-age=' + MAX_AGE + '; SameSite=Lax';
    }

    document.addEventListener('alpine:init', function () {
        Alpine.store('fav', {
            projects: read('fav_projects'),
            finished: read('fav_finished'),

            has(type, id) {
                return this[type].includes(id);
            },

            toggle(type, id) {
                if (this.has(type, id)) {
                    this[type] = this[type].filter(function (x) { return x !== id; });
                } else {
                    this[type] = this[type].concat(id);
                    if (window.ym) ym(62605987, 'reachGoal', 'favorite-add');
                }
                write('fav_' + type, this[type]);
            },

            get count() {
                return this.projects.length + this.finished.length;
            }
        });
    });
})();
