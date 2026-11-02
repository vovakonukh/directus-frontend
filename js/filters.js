// Фильтр и сортировка карточек каталога (разметка — includes/filters.php)
// config: { группа: { attr: 'data-атрибут обёртки', type: 'eq' | 'range' } }
// Сортировка: значение вида «атрибут_asc» / «атрибут_desc»; «popular» — исходный порядок с сервера
function projectsFilter(total, config, defaultSort) {
    const emptyFilters = () => Object.fromEntries(Object.keys(config).map(k => [k, []]));
    return {
        filters: emptyFilters(),
        sort: defaultSort,
        visibleCount: total,
        resetFilters() {
            this.filters = emptyFilters();
            this.sort = defaultSort;
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
                            return n >= min && n <= max;
                        });
                    }
                    return selected.includes(value);
                });
                item.classList.toggle('hidden', !ok);
                if (ok) visible++;
            });
            this.visibleCount = visible;
            document.getElementById('no-results').classList.toggle('hidden', visible > 0);
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
        }
    }
}
