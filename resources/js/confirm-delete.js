// Konfirmasi sebelum menghapus data lewat form (dipakai attribute data-confirm)

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        event.preventDefault();
    }
});
