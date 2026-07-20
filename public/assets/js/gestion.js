document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById("sidebar");
    const menuToggle = document.getElementById("menuToggle");
    const sidebarClose = document.getElementById("sidebarClose");
    const sidebarOverlay = document.getElementById("sidebarOverlay");
    const currentDate = document.getElementById("currentDate");

    /*
     * Ouverture de la barre latérale sur mobile
     */
    if (menuToggle && sidebar) {
        menuToggle.addEventListener("click", function () {
            sidebar.classList.add("open");

            if (sidebarOverlay) {
                sidebarOverlay.classList.add("show");
            }
        });
    }

    /*
     * Fermeture de la barre latérale
     */
    function closeSidebar() {
        if (sidebar) {
            sidebar.classList.remove("open");
        }

        if (sidebarOverlay) {
            sidebarOverlay.classList.remove("show");
        }
    }

    if (sidebarClose) {
        sidebarClose.addEventListener("click", closeSidebar);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", closeSidebar);
    }

    /*
     * Affichage de la date actuelle
     */
    if (currentDate) {
        const date = new Date();

        currentDate.textContent = date.toLocaleDateString("fr-FR", {
            weekday: "long",
            day: "2-digit",
            month: "long",
            year: "numeric"
        });
    }

    /*
     * Vérification de Chart.js
     */
    if (typeof Chart === "undefined") {
        console.error("Chart.js n'est pas chargé.");
        return;
    }

    /*
     * Données envoyées depuis la vue PHP
     */
    const dashboardData = window.dashboardData || {};

    const labels = Array.isArray(dashboardData.labels)
        ? dashboardData.labels
        : [];

    const withdrawalData = Array.isArray(dashboardData.retraits)
        ? dashboardData.retraits.map(Number)
        : [];

    const transferData = Array.isArray(dashboardData.transferts)
        ? dashboardData.transferts.map(Number)
        : [];

    /*
     * Formatage des montants en Ariary
     */
    function formatAriary(value) {
        return new Intl.NumberFormat("fr-FR").format(value) + " Ar";
    }

    /*
     * Options communes aux deux graphiques
     */
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,

        interaction: {
            mode: "index",
            intersect: false
        },

        plugins: {
            legend: {
                display: false
            },

            tooltip: {
                backgroundColor: "#182032",
                padding: 12,
                cornerRadius: 10,
                displayColors: false,

                callbacks: {
                    label: function (context) {
                        return "Gain : " + formatAriary(context.raw);
                    }
                }
            }
        },

        scales: {
            x: {
                grid: {
                    display: false
                },

                border: {
                    display: false
                },

                ticks: {
                    color: "#8a91a2",
                    font: {
                        family: "Manrope",
                        size: 11
                    }
                }
            },

            y: {
                beginAtZero: true,

                grid: {
                    color: "rgba(24, 32, 50, 0.06)"
                },

                border: {
                    display: false
                },

                ticks: {
                    color: "#8a91a2",
                    padding: 10,

                    font: {
                        family: "Manrope",
                        size: 10
                    },

                    callback: function (value) {
                        if (value >= 1000000) {
                            return value / 1000000 + " M";
                        }

                        if (value >= 1000) {
                            return value / 1000 + " k";
                        }

                        return value;
                    }
                }
            }
        }
    };

    /*
     * Graphique des gains sur les retraits
     */
    const withdrawalCanvas =
        document.getElementById("withdrawalChart");

    if (withdrawalCanvas) {
        const withdrawalContext =
            withdrawalCanvas.getContext("2d");

        const withdrawalGradient =
            withdrawalContext.createLinearGradient(
                0,
                0,
                0,
                300
            );

        withdrawalGradient.addColorStop(
            0,
            "rgba(57, 119, 246, 0.32)"
        );

        withdrawalGradient.addColorStop(
            1,
            "rgba(57, 119, 246, 0.01)"
        );

        new Chart(withdrawalContext, {
            type: "line",

            data: {
                labels: labels,

                datasets: [
                    {
                        label: "Gains sur retraits",
                        data: withdrawalData,

                        borderColor: "#3977f6",
                        backgroundColor: withdrawalGradient,

                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,

                        pointRadius: 4,
                        pointHoverRadius: 6,

                        pointBackgroundColor: "#ffffff",
                        pointBorderColor: "#3977f6",
                        pointBorderWidth: 2
                    }
                ]
            },

            options: commonOptions
        });
    }

    /*
     * Graphique des gains sur les transferts
     */
    const transferCanvas =
        document.getElementById("transferChart");

    if (transferCanvas) {
        const transferContext =
            transferCanvas.getContext("2d");

        const transferGradient =
            transferContext.createLinearGradient(
                0,
                0,
                0,
                300
            );

        transferGradient.addColorStop(
            0,
            "rgba(24, 169, 112, 0.32)"
        );

        transferGradient.addColorStop(
            1,
            "rgba(24, 169, 112, 0.01)"
        );

        new Chart(transferContext, {
            type: "bar",

            data: {
                labels: labels,

                datasets: [
                    {
                        label: "Gains sur transferts",
                        data: transferData,

                        backgroundColor: "#18a970",
                        borderColor: "#18a970",

                        borderWidth: 0,
                        borderRadius: 9,
                        borderSkipped: false,

                        maxBarThickness: 36
                    }
                ]
            },

            options: commonOptions
        });
    }

    /*
     * Sélection de la période
     *
     * Pour le moment, ce sélecteur ne fait qu'afficher
     * la période sélectionnée dans la console.
     *
     * Tu pourras ensuite l'utiliser avec AJAX ou
     * une nouvelle requête vers le contrôleur.
     */
    const chartPeriod = document.getElementById("chartPeriod");

    if (chartPeriod) {
        chartPeriod.addEventListener("change", function () {
            const period = this.value;

            console.log(
                "Période sélectionnée :",
                period,
                "jours"
            );
        });
    }
});