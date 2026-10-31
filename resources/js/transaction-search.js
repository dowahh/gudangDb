// Live search barang di form transaksi: fetch JSON lalu tampilkan daftar hasil

document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('item-search');
    const resultsList = document.getElementById('item-search-results');
    const hiddenItemId = document.getElementById('item_id');
    const selectedInfo = document.getElementById('item-selected-info');
    const stockInfo = document.getElementById('item-stock');
    const clearButton = document.getElementById('item-clear');

    if (!searchInput || !resultsList || !hiddenItemId) {
        return;
    }

    let timer = null;

    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        const query = searchInput.value.trim();

        // debounce: tunggu user berhenti mengetik 250ms sebelum fetch
        timer = setTimeout(() => {
            if (query.length < 1) {
                resultsList.hidden = true;
                resultsList.innerHTML = '';
                return;
            }

            fetch(`/items/search?q=${encodeURIComponent(query)}`)
                .then((response) => response.json())
                .then((items) => {
                    resultsList.innerHTML = '';

                    if (items.length === 0) {
                        resultsList.innerHTML = '<li class="empty-row">Barang tidak ditemukan.</li>';
                        resultsList.hidden = false;
                        return;
                    }

                    items.forEach((item) => {
                        const li = document.createElement('li');
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.textContent = `${item.name} (${item.sku}) — stok ${item.stock}`;

                        button.addEventListener('click', () => {
                            hiddenItemId.value = item.id;
                            searchInput.value = item.name;
                            selectedInfo.textContent = `Terpilih: ${item.name} (${item.sku})`;
                            if (stockInfo) {
                                stockInfo.textContent = `Stok tersedia: ${item.stock}`;
                                stockInfo.hidden = false;
                            }
                            resultsList.hidden = true;
                            resultsList.innerHTML = '';
                        });

                        li.appendChild(button);
                        resultsList.appendChild(li);
                    });

                    resultsList.hidden = false;
                });
        }, 250);
    });

    if (clearButton) {
        clearButton.addEventListener('click', () => {
            hiddenItemId.value = '';
            searchInput.value = '';
            selectedInfo.textContent = '';
            if (stockInfo) {
                stockInfo.textContent = '';
                stockInfo.hidden = true;
            }
            resultsList.hidden = true;
            resultsList.innerHTML = '';
        });
    }
});
