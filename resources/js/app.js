// Entry JavaScript. File lain (dashboard.js, dst) di-include per halaman lewat @push.

// KEAMANAN UX: konfirmasi sebelum menghapus data lewat form
document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
        event.preventDefault();
    }
});
