document.addEventListener('DOMContentLoaded', function() {
    const connexionForm = document.getElementById('connexionForm');
    
    if (connexionForm) {
        connexionForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            // Regex pour validation email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!emailRegex.test(email)) {
                alert('❌ Veuillez entrer une adresse email valide.');
                return;
            }

            if (password.length < 1) {
                alert('❌ Le mot de passe est obligatoire.');
                return;
            }

            // Si tout est valide, soumission du formulaire
            this.submit();
        });
    }
});
