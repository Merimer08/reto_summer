const { labels, weights, target } = window.chartData;

const yearSelect = document.getElementById('filterYear');
const monthSelect = document.getElementById('filterMonth');

const ctx = document.getElementById('chart');

let chart;

function movingAverage(data, windowSize = 7) {
    const result = [];

    for (let i = 0; i < data.length; i++) {
        if (i < windowSize - 1) {
            result.push(null);
            continue;
        }

        let sum = 0;
        for (let j = 0; j < windowSize; j++) {
            sum += data[i - j];
        }

        result.push(sum / windowSize);
    }

    return result;
}

// ---------- generar años disponibles ----------
const years = [...new Set(labels.map(d => d.substring(0,4)))];

years.forEach(y => {
    const opt = document.createElement('option');
    opt.value = y;
    opt.textContent = y;
    yearSelect.appendChild(opt);
});

// ---------- filtro ----------
function getFilteredData() {
    const year = yearSelect.value;
    const month = monthSelect.value;

    const filteredLabels = [];
    const filteredWeights = [];

    labels.forEach((date, i) => {

        const y = date.substring(0,4);
        const m = date.substring(5,7);

        if ((year === '' || year === y) &&
            (month === '' || month === m)) {

            filteredLabels.push(date);
            filteredWeights.push(weights[i]);
        }
    });

    return { filteredLabels, filteredWeights };
}

// ---------- crear gráfica ----------
function createChart(labelsData, weightsData) {

    const targetData =
        target === null ? [] : labelsData.map(() => target);
    const averageData = movingAverage(weightsData, 7);

    if (chart) chart.destroy();

    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, 'rgba(250,204,21,0.35)');
    gradient.addColorStop(1, 'rgba(250,204,21,0)');

    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labelsData,
            datasets: [
                {
                    label: 'Peso',
                    data: weightsData,
                    tension: 0.25,
                    borderWidth: 2,
                    pointRadius: 0,
                    borderColor: '#64748b',

                    segment: target === null ? {} : {
                        borderColor: (c) => {
                            const y = c.p1.parsed.y;
                            return y > target
                                ? 'rgba(239,68,68,0.5)'
                                : 'rgba(34,197,94,0.5)';
                        }
                    }
                },
                {
                    label: 'Media 7 días',
                    data: averageData,
                    borderColor: '#facc15',
                    backgroundColor: gradient,
                    fill: true,
                    borderWidth: 3,
                    tension: 0.35,
                    pointRadius: 0,
                    hoverBorderWidth: 4
                },
                {
                    label: 'Objetivo',
                    data: targetData,
                    borderColor: 'rgba(34,197,94,0.7)',
                    borderWidth: 2,
                    pointRadius: 0,
                    borderDash: [8,6],
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,

            interaction: {
                mode: 'index',
                intersect: false
            },

            plugins: {
                legend: {
                    labels: {
                        color: '#cbd5f5'
                    }
                },

                tooltip: {
                    enabled: true,
                    position: 'nearest',
                    backgroundColor: '#111418',
                    borderColor: 'rgba(255,255,255,0.05)',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                    titleColor: '#94a3b8',
                    bodyColor: '#e9eef6',
                    callbacks: {
                        label: (ctx) => `${ctx.parsed.y} kg`
                    }
                },

                zoom: {
                    pan: {
                        enabled: true,
                        mode: 'x'
                    },
                    zoom: {
                        wheel: {
                            enabled: true
                        },
                        pinch: {
                            enabled: true
                        },
                        mode: 'x'
                    }
                }
            },

            scales: {
                x: {
                    min: 0,
                    grid: {
                        color: 'rgba(255,255,255,0.04)'
                    },
                    ticks: {
                        color: '#94a3b8'
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(255,255,255,0.05)'
                    },
                    ticks: {
                        color: '#94a3b8'
                    }
                }
            }
        }
    });

    if (!ctx.dataset.zoomReset) {
        ctx.dataset.zoomReset = '1';
        ctx.addEventListener('dblclick', () => {
            if (chart) chart.resetZoom();
        });
    }
}

// ---------- eventos ----------
yearSelect.addEventListener('change', updateChart);
monthSelect.addEventListener('change', updateChart);

function updateChart() {
    const { filteredLabels, filteredWeights } = getFilteredData();
    createChart(filteredLabels, filteredWeights);
}

// inicial
updateChart();
