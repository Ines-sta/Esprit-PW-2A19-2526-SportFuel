// ============================================
// SportFuel — Validation JavaScript côté client
// Pas de validation HTML5 (contrainte du projet)
// ============================================

/**
 * Valider le formulaire Aliment (ajout et modification)
 */
function validerFormAliment(form) {
    var nom = form.querySelector('[name="nom"]').value.trim();
    var categorie = form.querySelector('[name="categorie"]').value.trim();
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
    if (categorie === '') {
        erreurs.push("La catégorie est obligatoire.");
    } else if (categorie.length > 100) {
        erreurs.push("La catégorie ne doit pas dépasser 100 caractères.");
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
 * Valider le formulaire Course (ajout et modification d'une liste)
 */
function validerFormCourse(form) {
    var date = form.querySelector('[name="date"]').value.trim();
    var statut = form.querySelector('[name="statut"]').value.trim();
    var erreurs = [];

    // Date obligatoire et format AAAA-MM-JJ
    if (date === '') {
        erreurs.push("La date est obligatoire.");
    } else if (!/^\d{4}-\d{2}-\d{2}$/.test(date)) {
        erreurs.push("La date doit être au format AAAA-MM-JJ.");
    }

    // Statut obligatoire
    if (statut === '') {
        erreurs.push("Le statut est obligatoire.");
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
 * Valider le formulaire d'ajout d'article à une course
 */
function validerFormArticle(form) {
    var aliment = form.querySelector('[name="id_aliment"]').value.trim();
    var quantite = form.querySelector('[name="quantite"]').value.trim();
    var erreurs = [];

    // Aliment obligatoire
    if (aliment === '') {
        erreurs.push("Veuillez sélectionner un aliment.");
    }

    // Quantité obligatoire et positive
    if (quantite === '') {
        erreurs.push("La quantité est obligatoire.");
    } else if (isNaN(quantite) || parseFloat(quantite) <= 0) {
        erreurs.push("La quantité doit être un nombre positif.");
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
