document.getElementById('connexionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Connexion réussie !');
});
document.querySelector('.link-btn').addEventListener('click', function(e) {
    e.preventDefault();
    const panel = document.getElementById('connexionPanel');
    panel.classList.add('signup-mode');
    setTimeout(function() {
        window.location.href = 'inscription.html';
    }, 400);
});
