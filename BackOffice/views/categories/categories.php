<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportFuel Admin — Catégories Alimentaires</title>
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
        <li><a href="/Esprit-PW-2A19-2526-SportFuel/BackOffice/controllers/aliment_controller.php"><span class="icon">🥗</span> Aliments & courses</a></li>
        <li><a href="/Esprit-PW-2A19-2526-SportFuel/BackOffice/controllers/categorie_controller.php" class="active"><span class="icon">📁</span> Catégories</a></li>
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
        <h1>Catégories Alimentaires</h1>
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
            <div class="value">6</div>
            <div class="trend">+1 ce mois</div>
            <div class="label">Total catégories</div>
        </div>
        <div class="stat-card">
            <div class="value">48</div>
            <div class="trend">Moy. 8 / cat.</div>
            <div class="label">Aliments classés</div>
        </div>
        <div class="stat-card">
            <div class="value orange">12</div>
            <div class="trend">+3 récents</div>
            <div class="label">Produits bio</div>
        </div>
    </div>

    <!-- Search Bar (dummy) -->
    <div class="search-bar" style="margin-bottom:20px;display:flex;gap:12px;align-items:center;">
        <input type="text" placeholder="Rechercher une catégorie..." style="flex:1;padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
        <button class="btn btn-outline" type="button">🔍 Rechercher</button>
    </div>

    <!-- Categories Table -->
    <div class="card">
        <div class="card-header">
            <h3>Liste des catégories</h3>
            <button class="btn btn-primary" onclick="document.getElementById('modalCat').classList.add('active')">+ Nouvelle catégorie</button>
        </div>

        <div class="table-container">
            <table id="tableCategories">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom de la catégorie</th>
                        <th>Description</th>
                        <th>Nb aliments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $c): ?>
                    <tr>
                        <td><?php echo $c['id_categorie']; ?></td>
                        <td><strong><?php echo htmlspecialchars($c['nom']); ?></strong></td>
                        <td><?php echo htmlspecialchars($c['description']); ?></td>
                        <td><?php echo $c['nb_aliments']; ?></td>
                        <td class="actions">
                            <a href="../controllers/categorie_controller.php?action=edit&id=<?php echo $c['id_categorie']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="../controllers/categorie_controller.php?action=supprimer&id=<?php echo $c['id_categorie']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette catégorie et tous ses aliments ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===== Modal: Ajouter Catégorie ===== -->
<div class="modal-overlay <?php echo (!empty($error) && $action === 'ajouter') ? 'active' : ''; ?>" id="modalCat">
    <div class="modal">
        <h3>Nouvelle catégorie</h3>
        <form method="POST" action="../controllers/categorie_controller.php?action=ajouter" id="formAjoutCategorie" onsubmit="return validerFormCategorie(this)">
            <div class="form-group">
                <label>Nom de la catégorie</label>
                <input type="text" name="nom" placeholder="Ex: Fruits secs" id="ajout_nom_cat">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" placeholder="Description de la catégorie..." id="ajout_desc_cat"></textarea>
            </div>
            <div id="erreurAjoutCat" style="color:#e63946;margin-top:8px;display:none;"></div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalCat').classList.remove('active')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal: Modifier Catégorie ===== -->
<?php if ($categorieEdit): ?>
<div class="modal-overlay active" id="modalModifCat">
    <div class="modal">
        <h3>Modifier la catégorie</h3>
        <form method="POST" action="../controllers/categorie_controller.php?action=modifier" id="formModifCategorie" onsubmit="return validerFormCategorie(this)">
            <input type="hidden" name="id" value="<?php echo $categorieEdit['id_categorie']; ?>">
            <div class="form-group">
                <label>Nom de la catégorie</label>
                <input type="text" name="nom" value="<?php echo htmlspecialchars($categorieEdit['nom']); ?>">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3"><?php echo htmlspecialchars($categorieEdit['description']); ?></textarea>
            </div>
            <div id="erreurModifCat" style="color:#e63946;margin-top:8px;display:none;"></div>
            <div class="modal-actions">
                <a href="../controllers/categorie_controller.php" class="btn btn-outline">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="../assets/js/validation.js"></script>

</body>
</html>
