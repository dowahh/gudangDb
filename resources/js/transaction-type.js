// Tombol MASUK/KELUAR di form transaksi: mengganti dropdown jenis.
// Nilai disimpan di input tersembunyi #type, lalu field supplier
// hanya ditampilkan untuk transaksi masuk.

document.addEventListener('DOMContentLoaded', () => {
    const typeInput = document.getElementById('type');
    const toggle = document.getElementById('tipe-toggle');
    const supplierField = document.getElementById('supplier-field');

    if (!typeInput || !toggle) {
        return;
    }

    const buttons = toggle.querySelectorAll('.tipe-btn');

    function syncType() {
        const type = typeInput.value;

        buttons.forEach((button) => {
            button.classList.toggle('is-active', button.dataset.type === type);
        });

        if (supplierField) {
            supplierField.hidden = type === 'keluar';
        }
    }

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            typeInput.value = button.dataset.type;
            syncType();
        });
    });

    // sinkron dengan nilai awal (old input saat validasi gagal)
    syncType();
});
