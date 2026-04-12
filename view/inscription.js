document.getElementById('inscriptionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const nom = document.getElementById('nom').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const age = document.getElementById('age').value.trim();
    const poids = document.getElementById('poids').value.trim();
    const taille = document.getElementById('taille').value.trim();

    // Regex pour validation email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Validation des champs
    if (nom.length < 3) {
        alert('❌ Le nom doit contenir au moins 3 caractères.');
        return;
    }

    if (!emailRegex.test(email)) {
        alert('❌ Veuillez entrer une adresse email valide.');
        return;
    }

    if (password.length < 6) {
        alert('❌ Le mot de passe doit contenir au moins 6 caractères.');
        return;
    }

    if (password !== confirmPassword) {
        alert('❌ Les mots de passe ne correspondent pas.');
        return;
    }

    if (isNaN(age) || age < 1 || age > 100) {
        alert('❌ Veuillez entrer un âge valide (1-100).');
        return;
    }

    if (isNaN(poids) || poids <= 0) {
        alert('❌ Veuillez entrer un poids valide.');
        return;
    }

    if (isNaN(taille) || taille <= 0) {
        alert('❌ Veuillez entrer une taille valide.');
        return;
    }

    // Si tout est valide, soumission du formulaire
    this.submit();
});

document.querySelector('.link-btn').addEventListener('click', function(e) {
    e.preventDefault();
    const panel = document.querySelector('.panel');
    if (panel) {
        panel.classList.add('signup-mode');
        setTimeout(function() {
            window.location.href = 'connexion.html';
        }, 400);
    } else {
        window.location.href = 'connexion.html';
    }
});
