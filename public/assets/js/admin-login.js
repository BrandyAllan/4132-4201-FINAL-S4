const togglePassword = document.getElementById('togglePassword');
const passwordInput = document.getElementById('mot_de_passe');

if (togglePassword && passwordInput) {
    togglePassword.addEventListener('click', () => {
        const isHidden = passwordInput.type === 'password';

        passwordInput.type = isHidden ? 'text' : 'password';

        const icon = togglePassword.querySelector('i');

        if (icon) {
            icon.className = isHidden ? 'bi bi-eye-slash' : 'bi bi-eye';
        }
    });
}
