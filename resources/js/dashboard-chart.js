// Grafik dashboard: ambil data JSON lalu render Chart.js bar.
// Warna diambil dari CSS variable (masuk = ink, keluar = oranye).

import Chart from 'chart.js/auto';

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('transaction-chart');

    if (!canvas) {
        return;
    }

    const styles = getComputedStyle(document.documentElement);
    const ink = styles.getPropertyValue('--ink').trim() || '#141414';
    const aksen = styles.getPropertyValue('--accent').trim() || '#e85d04';
    const gridColor = '#d9d6cc';

    fetch(canvas.dataset.url)
        .then((response) => response.json())
        .then((data) => {
            const highest = Math.max(...data.masuk, ...data.keluar, 0);
            // skala Y: kelipatan 50 di atas nilai tertinggi, tick dibagi 4 (0, 50, 100, 150)
            const suggestedMax = highest <= 0 ? 50 : Math.ceil(highest / 50) * 50;
            const stepSize = suggestedMax / 4;

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Masuk',
                            data: data.masuk,
                            backgroundColor: ink,
                            borderColor: ink,
                            borderWidth: 0,
                            borderRadius: 0,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7,
                        },
                        {
                            label: 'Keluar',
                            data: data.keluar,
                            backgroundColor: aksen,
                            borderColor: aksen,
                            borderWidth: 0,
                            borderRadius: 0,
                            barPercentage: 0.6,
                            categoryPercentage: 0.7,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax,
                            max: suggestedMax,
                            ticks: {
                                stepSize,
                                maxTicksLimit: 5,
                                font: {
                                    family: "'IBM Plex Mono', ui-monospace, monospace",
                                    size: 11,
                                },
                                color: ink,
                            },
                            grid: {
                                color: gridColor,
                                lineWidth: 1,
                                drawBorder: false,
                            },
                            border: {
                                display: false,
                            },
                        },
                        x: {
                            ticks: {
                                autoSkip: false,
                                maxRotation: 0,
                                minRotation: 0,
                                font: {
                                    family: "'IBM Plex Mono', ui-monospace, monospace",
                                    size: 11,
                                },
                                color: ink,
                            },
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            border: {
                                color: ink,
                            },
                        },
                    },
                },
            });
        });
});
