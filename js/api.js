const API_URL = 'http://188.225.86.133:8055';

async function fetchItems(collection, params = {}) {
    const query = new URLSearchParams(params).toString();
    const url = `${API_URL}/items/${collection}${query ? '?' + query : ''}`;
    
    const response = await fetch(url);
    const data = await response.json();
    return data.data;
}

function getAssetUrl(fileId) {
    return `${API_URL}/assets/${fileId}`;
}