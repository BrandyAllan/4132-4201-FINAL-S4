document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js n’est pas chargé.');
        return;
    }

    if (
        typeof window.dashboardData === 'undefined'
        || !window.dashboardData
    ) {
        console.error(
            'Les données du tableau de bord sont absentes.'
        );
        return;
    }

    const labels = Array.isArray(
        window.dashboardData.labels
    )
        ? window.dashboardData.labels
        : [];

    const retraits = Array.isArray(
        window.dashboardData.retraits
    )
        ? window.dashboardData.retraits.map(Number)
        : [];

    const transferts = Array.isArray(
        window.dashboardData.transferts
    )
        ? window.dashboardData.transferts.map(Number)
        : [];

    const formatMontant = function (montant) {
        return new Intl.NumberFormat('fr-FR').format(
            Number(montant) || 0
        ) + ' Ar';
    };

    /*
     * Graphique des retraits
     */
    const withdrawalCanvas = document.getElementById(
        'withdrawalChart'
    );

    if (withdrawalCanvas) {
        new Chart(withdrawalCanvas, {
            type: 'line',

            data: {
                labels: labels,

                datasets: [
                    {
                        label: 'Gains sur retraits',
                        data: retraits,
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    intersect: false,
                    mode: 'index'
                },

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return formatMontant(
                                    context.raw
                                );
                            }
                        }
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true,

                        ticks: {
                            callback: function (value) {
                                return new Intl.NumberFormat(
                                    'fr-FR',
                                    {
                                        notation: 'compact'
                                    }
                                ).format(value) + ' Ar';
                            }
                        }
                    },

                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    /*
     * Graphique des transferts
     */
    const transferCanvas = document.getElementById(
        'transferChart'
    );

    if (transferCanvas) {
        new Chart(transferCanvas, {
            type: 'line',

            data: {
                labels: labels,

                datasets: [
                    {
                        label: 'Gains sur transferts',
                        data: transferts,
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 6
                    }
                ]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                interaction: {
                    intersect: false,
                    mode: 'index'
                },

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return formatMontant(
                                    context.raw
                                );
                            }
                        }
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true,

                        ticks: {
                            callback: function (value) {
                                return new Intl.NumberFormat(
                                    'fr-FR',
                                    {
                                        notation: 'compact'
                                    }
                                ).format(value) + ' Ar';
                            }
                        }
                    },

                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    const commissionCtx =
        document.getElementById('commissionChart');

    new Chart(commissionCtx, {

        type: 'line',

        data: {

            labels: commissionLabels,

            datasets: [{

                label: 'Commissions',

                data: commissionData,

                borderColor: '#f59e0b',

                backgroundColor: 'rgba(245,158,11,.15)',

                fill: true,

                tension: .35,

                pointRadius: 4

            }]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false,

            plugins: {

                legend: {

                    display: false

                }

            }

        }

    });
});