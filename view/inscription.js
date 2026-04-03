document.getElementById('inscriptionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    if (password !== confirmPassword) {
        alert('Les mots de passe ne correspondent pas.');
        return;
    }
    alert('Inscription réussie !');
});
document.querySelector('.link-btn').addEventListener('click', function(e) {
    e.preventDefault();
    const panel = document.querySelector('.panel');
    panel.classList.add('signup-mode');
    setTimeout(function() {
        window.location.href = 'connexion.html';
    }, 400);
});
