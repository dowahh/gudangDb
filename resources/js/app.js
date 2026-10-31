// Entry JavaScript: dimuat di semua halaman lewat @vite di layout.
// Setiap file no-op kalau elemen halaman itu tidak ada.

import './confirm-delete.js';
import './dashboard-chart.js';
import './transaction-search.js';
import './transaction-type.js';

// Menu mobile: buka/tutup sidebar off-canvas
const menuToggle = document.getElementById('menu-toggle');
const sidebar = document.getElementById('sidebar');
const sidebarBackdrop = document.getElementById('sidebar-backdrop');

function closeSidebar() {
    sidebar?.classList.remove('open');
    sidebarBackdrop?.classList.remove('open');
}

menuToggle?.addEventListener('click', () => {
    sidebar?.classList.toggle('open');
    sidebarBackdrop?.classList.toggle('open');
});

sidebarBackdrop?.addEventListener('click', closeSidebar);
