// Grafik dashboard: ambil data JSON lalu render Chart.js bar

import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('transaction-chart');

    if (!canvas) {
        return;
    }

    fetch(canvas.dataset.url)
        .then((response) => response.json())
        .then((data) => {
            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Masuk',
                            data: data.masuk,
                            backgroundColor: '#16a34a',
                        },
                        {
                            label: 'Keluar',
                            data: data.keluar,
                            backgroundColor: '#dc2626',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                            },
                        },
                    },
                },
            });
        });
});
