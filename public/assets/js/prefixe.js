document.addEventListener("DOMContentLoaded", function () {
    const popup = document.getElementById("prefixePopupOverlay");
    const inputId = document.getElementById("modifierId");
    const inputPrefixe = document.getElementById("modifierPrefixe");
    const inputActif = document.getElementById("modifierActif");

    const boutonFermer = document.getElementById("closePrefixePopup");
    const boutonAnnuler = document.getElementById("cancelPrefixePopup");

    if (!popup) {
        console.error("Erreur : le popup #prefixePopupOverlay est introuvable.");
        return;
    }

    function ouvrirPopup(bouton) {
        const id = bouton.dataset.id;
        const prefixe = bouton.dataset.prefixe;
        const actif = bouton.dataset.actif;

        inputId.value = id;
        inputPrefixe.value = prefixe;
        inputActif.value = String(actif);

        popup.style.display = "flex";
        document.body.style.overflow = "hidden";

        setTimeout(function () {
            inputPrefixe.focus();
        }, 100);
    }

    function fermerPopup() {
        popup.style.display = "none";
        document.body.style.overflow = "";
    }

    /*
     * Utilisation de la délégation d'événement.
     * Cela fonctionne même si le tableau est modifié dynamiquement.
     */
    document.addEventListener("click", function (event) {
        const boutonModifier = event.target.closest(
            ".btn-modifier-prefixe"
        );

        if (boutonModifier) {
            event.preventDefault();
            ouvrirPopup(boutonModifier);
        }
    });

    if (boutonFermer) {
        boutonFermer.addEventListener("click", fermerPopup);
    }

    if (boutonAnnuler) {
        boutonAnnuler.addEventListener("click", fermerPopup);
    }

    popup.addEventListener("click", function (event) {
        if (event.target === popup) {
            fermerPopup();
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            fermerPopup();
        }
    });

    /*
     * Recherche dans le tableau
     */
    const searchInput = document.getElementById("searchPrefix");
    const table = document.getElementById("prefixTable");

    if (searchInput && table) {
        searchInput.addEventListener("input", function () {
            const valeur = this.value.toLowerCase().trim();
            const lignes = table.querySelectorAll("tbody tr");

            lignes.forEach(function (ligne) {
                const contenu = ligne.textContent.toLowerCase();

                ligne.style.display = contenu.includes(valeur)
                    ? ""
                    : "none";
            });
        });
    }
});