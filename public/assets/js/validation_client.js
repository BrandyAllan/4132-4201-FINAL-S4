// =================================================================
// 1. SÉLECTION DES ÉLÉMENTS ET CONFIGURATION REGEX
// =================================================================
const form = document.querySelector('form');

// Éléments pour le Login
const telInput = document.getElementById('telephone');
const telError = document.getElementById('telephone-error');

// Éléments pour le Transfert / Dépôt / Retrait
const destinataireInput = document.getElementById('destinataire');
const montantInput = document.getElementById('montant');

// Règle : EXACTEMENT 10 chiffres au total avec les préfixes autorisés
const telRegex = /^(032|033|034|038)\d{7}$/;

// =================================================================
// 2. LOGIQUE DE VALIDATION EN TEMPS RÉEL (INPUTS)
// =================================================================

// --- Validation Téléphone (Login) ---
if (telInput) {
    telInput.addEventListener('input', () => {
        const valeur = telInput.value.trim();
        if (telRegex.test(valeur)) {
            telInput.classList.remove('is-invalid');
            telInput.classList.add('is-valid');
            if (telError) telError.classList.remove('visible');
        } else {
            telInput.classList.add('is-invalid');
            telInput.classList.remove('is-valid');
            if (telError) {
                telError.textContent = "Le numéro doit contenir exactement 10 chiffres (ex: 0340000000).";
                telError.classList.add('visible');
            }
        }
    });
}

// --- Validation Destinataire (Transfert) ---
if (destinataireInput) {
    destinataireInput.addEventListener('input', () => {
        const valeur = destinataireInput.value.trim();
        if (telRegex.test(valeur)) {
            destinataireInput.classList.remove('is-invalid');
            destinataireInput.classList.add('is-valid');
        } else {
            destinataireInput.classList.add('is-invalid');
            destinataireInput.classList.remove('is-valid');
        }
    });
}

// --- Validation Montant (Dépôt / Retrait / Transfert) ---
if (montantInput) {
    montantInput.addEventListener('input', () => {
        const montant = parseFloat(montantInput.value);
        if (!isNaN(montant) && montant > 0) {
            montantInput.classList.remove('is-invalid');
            montantInput.classList.add('is-valid');
        } else {
            montantInput.classList.add('is-invalid');
            montantInput.classList.remove('is-valid');
        }
    });
}

// =================================================================
// 3. SÉCURITÉ À LA SOUMISSION DU FORMULAIRE (SUBMIT)
// =================================================================
if (form) {
    form.addEventListener('submit', (e) => {
        let hasError = false;

        // --- Validation du champ Téléphone si présent ---
        if (telInput) {
            const valeur = telInput.value.trim();
            if (valeur === "" || !telRegex.test(valeur)) {
                hasError = true;
                telInput.classList.add('is-invalid');
                telInput.classList.remove('is-valid');
            }
        }

        // --- Validation du champ Destinataire si présent ---
        if (destinataireInput) {
            const telVal = destinataireInput.value.trim();
            if (telVal === "" || !telRegex.test(telVal)) {
                hasError = true;
                destinataireInput.classList.add('is-invalid');
                destinataireInput.classList.remove('is-valid');
            }
        }

        // --- Validation du champ Montant si présent ---
        if (montantInput) {
            const montantVal = parseFloat(montantInput.value);
            if (isNaN(montantVal) || montantVal <= 0) {
                hasError = true;
                montantInput.classList.add('is-invalid');
                montantInput.classList.remove('is-valid');
            }
        }

        // Blocage global si une anomalie persiste
        if (hasError) {
            e.preventDefault();
            alert("Veuillez vérifier les informations saisies avant de continuer.");
        }
    });
}