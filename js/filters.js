// Фильтр и сортировка карточек каталога (разметка — includes/filters.php)
// config: { группа: { attr: 'data-атрибут обёртки', type: 'eq' | 'range' | 'all' } }
// 'all' — в атрибуте список через пробел, карточка подходит, если в нём есть все выбранные значения
// Сортировка: значение вида «атрибут_asc» / «атрибут_desc»; «popular» — исходный порядок с сервера
// nouns: формы слова для счётчика { nom: ['проект', 'проекта', 'проектов'], gen: ['проекта', 'проектов', 'проектов'] }
function pluralRu(n, forms) {
    const a = Math.abs(n) % 100, b = a % 10;
    if (a > 10 && a < 20) return forms[2];
    if (b > 1 && b < 5) return forms[1];
    if (b === 1) return forms[0];
    return forms[2];
}

function projectsFilter(total, config, defaultSort, nouns) {
    const emptyFilters = () => Object.fromEntries(Object.keys(config).map(k => [k, []]));
    return {
        filters: emptyFilters(),
        sort: defaultSort,
        visibleCount: total,
        panelOpen: false,
        // сколько значений выбрано во всех группах — для значка на кнопке
        get activeCount() {
            return Object.values(this.filters).reduce((sum, list) => sum + list.length, 0);
        },
        // «Показано 78 проектов» без фильтров, «Показано 22 из 78 проектов» с фильтрами
        get countText() {
            return this.activeCount
                ? `Показано ${this.visibleCount} из ${total} ${pluralRu(total, nouns.gen)}`
                : `Показано ${total} ${pluralRu(total, nouns.nom)}`;
        },
        resetFilters() {
            this.filters = emptyFilters();
            this.sort = defaultSort;
        },
        // кнопки-ссылки (подборки): клик включает/выключает значение вместо перехода
        toggle(key, value) {
            const list = this.filters[key];
            const i = list.indexOf(value);
            i === -1 ? list.push(value) : list.splice(i, 1);
        },
        init() {
            this.$watch('filters', () => this.applyFilters(), { deep: true });
            this.$watch('sort', () => this.applySort());
        },
        applyFilters() {
            const items = document.querySelectorAll('.filter_item');
            let visible = 0;
            items.forEach(item => {
                const ok = Object.entries(config).every(([key, { attr, type }]) => {
                    const selected = this.filters[key];
                    if (!selected.length) return true;
                    const value = item.dataset[attr] ?? '';
                    if (type === 'range') {
                        const n = parseFloat(value);
                        return selected.some(range => {
                            const [min, max] = range.split('-').map(Number);
                            // верхняя граница не включается: 70 м² — только в «от 70 до 100»
                            return n >= min && n < max;
                        });
                    }
                    if (type === 'all') {
                        const values = value.split(' ');
                        return selected.every(v => values.includes(v));
                    }
                    return selected.includes(value);
                });
                item.classList.toggle('hidden', !ok);
                if (ok) visible++;
            });
            this.visibleCount = visible;
            document.getElementById('no-results').classList.toggle('hidden', visible > 0);
            this.placeInserts();
        },
        applySort() {
            const list = document.querySelector('.projects__collection');
            const items = [...list.querySelectorAll('.filter_item')];
            const [attr, dir] = this.sort === 'popular' ? ['index', 'asc'] : this.sort.split('_');
            const sign = dir === 'desc' ? -1 : 1;
            // карточки без значения (0) — всегда в конце
            items.sort((a, b) => {
                const x = parseFloat(a.dataset[attr]) || 0;
                const y = parseFloat(b.dataset[attr]) || 0;
                if (!x !== !y) return !x ? 1 : -1;
                return (x - y) * sign;
            });
            items.forEach(item => list.appendChild(item));
            this.placeInserts();
        },
        // Блоки с data-insert-after="N" (например, includes/blocks/project_custom.php)
        // ставим после N-й видимой карточки; карточек меньше — в конец, ни одной — скрываем
        placeInserts() {
            const list = document.querySelector('.projects__collection');
            const visible = [...list.querySelectorAll('.filter_item:not(.hidden)')];
            list.querySelectorAll('[data-insert-after]').forEach(el => {
                const n = parseInt(el.dataset.insertAfter, 10);
                el.classList.toggle('hidden', !visible.length);
                const anchor = visible[Math.min(n, visible.length) - 1];
                if (anchor) anchor.after(el);
            });
        }
    }
}
