<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportFuel Admin — Gestion des Aliments</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<aside class="sidebar">
    <a href="#" class="sidebar-brand">
        <div class="sidebar-logo">SF</div>
        <span>Sport<em>Fuel</em></span>
    </a>
    <div class="sidebar-role">ADMIN</div>

    <ul class="sidebar-menu">
        <li><a href="#"><span class="icon">📊</span> Dashboard</a></li>
    </ul>
    <div class="sidebar-section">Modules</div>
    <ul class="sidebar-menu">
        <li><a href="#"><span class="icon">👥</span> Utilisateurs</a></li>
        <li><a href="#"><span class="icon">🍽️</span> Plans alimentaires</a></li>
        <li><a href="#"><span class="icon">🏋️</span> Entraînements</a></li>
        <li><a href="/Esprit-PW-2A19-2526-SportFuel/BackOffice/controllers/aliment_controller.php" class="active"><span class="icon">🥗</span> Aliments & courses</a></li>
        <li><a href="/Esprit-PW-2A19-2526-SportFuel/BackOffice/controllers/categorie_controller.php"><span class="icon">📁</span> Catégories</a></li>
        <li><a href="/Esprit-PW-2A19-2526-SportFuel/BackOffice/views/courses/courses.html"><span class="icon">🛒</span> Listes de courses</a></li>
        <li><a href="#"><span class="icon">🤝</span> Espace coach</a></li>
    </ul>
    <div class="sidebar-section">Général</div>
    <ul class="sidebar-menu">
        <li><a href="#"><span class="icon">📈</span> Statistiques</a></li>
        <li><a href="#"><span class="icon">⚙️</span> Paramètres</a></li>
    </ul>
</aside>

<!-- ===== MAIN ===== -->
<div class="main-area">

    <div class="topbar">
        <h1>Gestion des Aliments</h1>
        <span class="date"><?php echo date('l j F Y'); ?></span>
    </div>

    <!-- Messages -->
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Stats (dummy) -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="value">48</div>
            <div class="trend">+5 cette semaine</div>
            <div class="label">Total aliments</div>
        </div>
        <div class="stat-card">
            <div class="value">32</div>
            <div class="trend">67%</div>
            <div class="label">Produits bio</div>
        </div>
        <div class="stat-card">
            <div class="value">41</div>
            <div class="trend">85%</div>
            <div class="label">Produits locaux</div>
        </div>
        <div class="stat-card">
            <div class="value orange">156</div>
            <div class="trend">Moy. / 100g</div>
            <div class="label">Kcal moyen</div>
        </div>
    </div>

    <!-- Search Bar (dummy) -->
    <div class="search-bar" style="margin-bottom:20px;display:flex;gap:12px;align-items:center;">
        <input type="text" placeholder="Rechercher un aliment..." style="flex:1;padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
        <button class="btn btn-outline" type="button">🔍 Rechercher</button>
    </div>

    <!-- Aliments Table -->
    <div class="card">
        <div class="card-header">
            <h3>Liste des aliments</h3>
            <button class="btn btn-primary" onclick="document.getElementById('modalAjout').classList.add('active')">+ Ajouter un aliment</button>
        </div>

        <div class="table-container">
            <table id="tableAliments">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Kcal/100g</th>
                        <th>CO₂ (kg)</th>
                        <th>Bio</th>
                        <th>Local</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aliments as $a): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['nom']); ?></td>
                        <td><?php echo htmlspecialchars($a['nom_categorie']); ?></td>
                        <td><?php echo $a['kcal_portion']; ?></td>
                        <td><?php echo $a['co2_impact']; ?></td>
                        <td><?php echo $a['est_bio'] ? '<span class="badge badge-bio">Bio</span>' : '—'; ?></td>
                        <td><?php echo $a['est_local'] ? '<span class="badge badge-local">Local</span>' : '—'; ?></td>
                        <td class="actions">
                            <a href="../controllers/aliment_controller.php?action=edit&id=<?php echo $a['id_aliment']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="../controllers/aliment_controller.php?action=supprimer&id=<?php echo $a['id_aliment']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cet aliment ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===== Modal: Ajouter Aliment ===== -->
<div class="modal-overlay <?php echo (!empty($error) && $action === 'ajouter') ? 'active' : ''; ?>" id="modalAjout">
    <div class="modal">
        <h3>Ajouter un aliment</h3>
        <form method="POST" action="../controllers/aliment_controller.php?action=ajouter" id="formAjoutAliment" onsubmit="return validerFormAliment(this)">
            <div class="form-row">
                <div class="form-group">
                    <label>Nom de l'aliment</label>
                    <input type="text" name="nom" placeholder="Ex: Huile d'olive" id="ajout_nom">
                </div>
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="id_categorie" id="ajout_categorie">
                        <option value="">— Sélectionner —</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c['id_categorie']; ?>"><?php echo htmlspecialchars($c['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Calories (kcal / 100g)</label>
                    <input type="text" name="kcal_portion" placeholder="Ex: 884" id="ajout_kcal">
                </div>
                <div class="form-group">
                    <label>Impact CO₂ (kg)</label>
                    <input type="text" name="co2_impact" placeholder="Ex: 0.8" id="ajout_co2">
                </div>
            </div>
            <div style="display:flex;gap:24px;">
                <div class="form-check">
                    <input type="checkbox" name="est_bio" id="checkBio">
                    <label for="checkBio">Produit Bio</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="est_local" id="checkLocal">
                    <label for="checkLocal">Produit Local</label>
                </div>
            </div>
            <div id="erreurAjout" style="color:#e63946;margin-top:8px;display:none;"></div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalAjout').classList.remove('active')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal: Modifier Aliment ===== -->
<?php if ($alimentEdit): ?>
<div class="modal-overlay active" id="modalModif">
    <div class="modal">
        <h3>Modifier l'aliment</h3>
        <form method="POST" action="../controllers/aliment_controller.php?action=modifier" id="formModifAliment" onsubmit="return validerFormAliment(this)">
            <input type="hidden" name="id" value="<?php echo $alimentEdit['id_aliment']; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Nom de l'aliment</label>
                    <input type="text" name="nom" value="<?php echo htmlspecialchars($alimentEdit['nom']); ?>">
                </div>
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="id_categorie">
                        <option value="">— Sélectionner —</option>
                        <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c['id_categorie']; ?>" <?php echo $c['id_categorie'] == $alimentEdit['id_categorie'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['nom']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Calories (kcal / 100g)</label>
                    <input type="text" name="kcal_portion" value="<?php echo $alimentEdit['kcal_portion']; ?>">
                </div>
                <div class="form-group">
                    <label>Impact CO₂ (kg)</label>
                    <input type="text" name="co2_impact" value="<?php echo $alimentEdit['co2_impact']; ?>">
                </div>
            </div>
            <div style="display:flex;gap:24px;">
                <div class="form-check">
                    <input type="checkbox" name="est_bio" id="editBio" <?php echo $alimentEdit['est_bio'] ? 'checked' : ''; ?>>
                    <label for="editBio">Produit Bio</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="est_local" id="editLocal" <?php echo $alimentEdit['est_local'] ? 'checked' : ''; ?>>
                    <label for="editLocal">Produit Local</label>
                </div>
            </div>
            <div id="erreurModif" style="color:#e63946;margin-top:8px;display:none;"></div>
            <div class="modal-actions">
                <a href="../controllers/aliment_controller.php" class="btn btn-outline">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="../assets/js/validation.js"></script>

</body>
</html>
