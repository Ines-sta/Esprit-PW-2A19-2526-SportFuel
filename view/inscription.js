document.addEventListener('DOMContentLoaded', function () {

  const roleInput  = document.getElementById('roleInput');
  const submitBtn  = document.getElementById('submitBtn');
  const btnSportif = document.getElementById('btnSportif');
  const btnAdmin   = document.getElementById('btnAdmin');
  const ageInput   = document.getElementById('age');
  const poidsInput = document.getElementById('poids');
  const tailleInput = document.getElementById('taille');

  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('role') === 'Admin') selectRole('Admin');

  function selectRole(role) {
    roleInput.value = role;

    if (role === 'Admin') {
      btnAdmin.classList.add('sel-admin');
      btnAdmin.classList.remove('sel-sportif');
      btnSportif.classList.remove('sel-sportif', 'sel-admin');
      submitBtn.textContent = '⭐ S\'inscrire comme Admin';
      submitBtn.classList.add('admin-mode');
      ageInput.style.display = 'none';
      poidsInput.style.display = 'none';
      tailleInput.style.display = 'none';
      ageInput.value = '';
      poidsInput.value = '';
      tailleInput.value = '';
    } else {
      btnSportif.classList.add('sel-sportif');
      btnSportif.classList.remove('sel-admin');
      btnAdmin.classList.remove('sel-admin', 'sel-sportif');
      submitBtn.textContent = '🏃 S\'inscrire comme Sportif';
      submitBtn.classList.remove('admin-mode');
      ageInput.style.display = '';
      poidsInput.style.display = '';
      tailleInput.style.display = '';
    }
  }

  btnSportif.addEventListener('click', () => selectRole('Sportif'));
  btnAdmin.addEventListener('click',   () => selectRole('Admin'));

  
  document.getElementById('inscriptionForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const nom             = document.getElementById('nom').value.trim();
    const email           = document.getElementById('email').value.trim();
    const password        = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const age             = document.getElementById('age').value.trim();
    const poids           = document.getElementById('poids').value.trim();
    const taille          = document.getElementById('taille').value.trim();
    const emailRegex      = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (nom.length < 3)              { alert('❌ Le nom doit contenir au moins 3 caractères.'); return; }
    if (!emailRegex.test(email))     { alert('❌ Email invalide.'); return; }
    if (password.length < 6)         { alert('❌ Mot de passe trop court (min 6 car.).'); return; }
    if (password !== confirmPassword){ alert('❌ Les mots de passe ne correspondent pas.'); return; }
    if (roleInput.value === 'Sportif') {
      if (isNaN(age) || age < 1 || age > 100) { alert('❌ Âge invalide (1-100).'); return; }
      if (isNaN(poids) || poids <= 0)  { alert('❌ Poids invalide.'); return; }
      if (isNaN(taille) || taille <= 0){ alert('❌ Taille invalide.'); return; }
    }

    this.submit();
  });

  
  const linkBtn = document.querySelector('.link-btn');
  if (linkBtn) {
    linkBtn.addEventListener('click', function (e) {
      e.preventDefault();
      window.location.href = 'connexion.html';
    });
  }
});
