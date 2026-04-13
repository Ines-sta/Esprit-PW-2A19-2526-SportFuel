// ============================================
// SportFuel — Validation JavaScript côté client
// Pas de validation HTML5 (contrainte du projet)
// ============================================

/**
 * Valider le formulaire Aliment (ajout et modification)
 */
function validerFormAliment(form) {
    var nom = form.querySelector('[name="nom"]').value.trim();
    var categorie = form.querySelector('[name="id_categorie"]').value;
    var kcal = form.querySelector('[name="kcal_portion"]').value.trim();
    var co2 = form.querySelector('[name="co2_impact"]').value.trim();
    var erreurs = [];

    // Nom obligatoire
    if (nom === '') {
        erreurs.push("Le nom de l'aliment est obligatoire.");
    } else if (nom.length > 150) {
        erreurs.push("Le nom ne doit pas dépasser 150 caractères.");
    }

    // Catégorie obligatoire
    if (categorie === '' || categorie === '0') {
        erreurs.push("Veuillez sélectionner une catégorie.");
    }

    // Kcal : nombre positif
    if (kcal === '') {
        erreurs.push("Les calories sont obligatoires.");
    } else if (isNaN(kcal) || parseFloat(kcal) <= 0) {
        erreurs.push("Les calories doivent être un nombre positif.");
    }

    // CO2 : nombre positif ou zéro
    if (co2 === '') {
        erreurs.push("L'impact CO₂ est obligatoire.");
    } else if (isNaN(co2) || parseFloat(co2) < 0) {
        erreurs.push("L'impact CO₂ doit être un nombre positif.");
    }

    // Afficher les erreurs
    var erreurDiv = form.querySelector('[id^="erreur"]');
    if (erreurs.length > 0) {
        if (erreurDiv) {
            erreurDiv.textContent = erreurs.join(' ');
            erreurDiv.style.display = 'block';
        }
        return false;
    }

    if (erreurDiv) {
        erreurDiv.style.display = 'none';
    }
    return true;
}

/**
 * Valider le formulaire Catégorie (ajout et modification)
 */
function validerFormCategorie(form) {
    var nom = form.querySelector('[name="nom"]').value.trim();
    var erreurs = [];

    // Nom obligatoire
    if (nom === '') {
        erreurs.push("Le nom de la catégorie est obligatoire.");
    } else if (nom.length > 100) {
        erreurs.push("Le nom ne doit pas dépasser 100 caractères.");
    }

    // Afficher les erreurs
    var erreurDiv = form.querySelector('[id^="erreur"]');
    if (erreurs.length > 0) {
        if (erreurDiv) {
            erreurDiv.textContent = erreurs.join(' ');
            erreurDiv.style.display = 'block';
        }
        return false;
    }

    if (erreurDiv) {
        erreurDiv.style.display = 'none';
    }
    return true;
}
