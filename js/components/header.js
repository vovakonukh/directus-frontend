async function loadHeader(selector = '#header') {
    const container = document.querySelector(selector);
    if (!container) return;
    
    const response = await fetch('/templates/header.html?v=' + Date.now());
    const html = await response.text();
    container.innerHTML = html;
    
    Alpine.initTree(container);
}

document.addEventListener('alpine:init', () => {
    Alpine.data('headerComponent', () => ({
        contacts: null,
        header: null,
        mobileMenuOpen: false,

        async init() {
            this.contacts = await fetchItems('contacts/1');
            this.header = await fetchItems('header');
        },

        getAsset(id) {
            return getAssetUrl(id);
        }
    }));
});

document.addEventListener('DOMContentLoaded', () => {
    loadHeader();
});