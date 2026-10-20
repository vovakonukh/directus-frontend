// Фильтр карточек каталога (разметка — includes/filters.php)
function projectsFilter(total) {
    return {
        filters: { floors: [], area: [], bedrooms: [], wc: [] },
        visibleCount: total,
        resetFilters() {
            this.filters = { floors: [], area: [], bedrooms: [], wc: [] };
        },
        init() {
            this.$watch('filters', () => this.applyFilters(), { deep: true });
        },
        applyFilters() {
            const cards = document.querySelectorAll('.project_card');
            let visible = 0;
            cards.forEach(card => {
                const f = card.dataset.floors;
                const a = parseFloat(card.dataset.area);
                const b = card.dataset.bedrooms;
                const w = card.dataset.bathrooms;

                const okFloors = !this.filters.floors.length || this.filters.floors.includes(f);
                const okBed = !this.filters.bedrooms.length || this.filters.bedrooms.includes(b);
                const okWc = !this.filters.wc.length || this.filters.wc.includes(w);
                const okArea = !this.filters.area.length || this.filters.area.some(range => {
                    const [min, max] = range.split('-').map(Number);
                    return a >= min && a <= max;
                });

                if (okFloors && okArea && okBed && okWc) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            this.visibleCount = visible;
            document.getElementById('no-results').classList.toggle('hidden', visible > 0);
        }
    }
}
