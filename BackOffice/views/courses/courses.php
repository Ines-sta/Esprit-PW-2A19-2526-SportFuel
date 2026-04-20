<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportFuel Admin — Listes de Courses</title>
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
        <li><a href="/Esprit-PW-2A19-2526-SportFuel/BackOffice/controllers/aliment_controller.php"><span class="icon">🥗</span> Aliments</a></li>
        <li><a href="/Esprit-PW-2A19-2526-SportFuel/BackOffice/controllers/course_controller.php" class="active"><span class="icon">🛒</span> Listes de courses</a></li>
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
        <h1>Listes de Courses</h1>
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
            <div class="value">342</div>
            <div class="trend">+18 cette semaine</div>
            <div class="label">Listes générées</div>
        </div>
        <div class="stat-card">
            <div class="value">78%</div>
            <div class="trend">+5%</div>
            <div class="label">Taux de complétion</div>
        </div>
        <div class="stat-card">
            <div class="value orange">2 847</div>
            <div class="trend orange">Articles achetés</div>
            <div class="label">Ce mois</div>
        </div>
    </div>

    <!-- Search Bar (dummy) -->
    <div class="search-bar" style="margin-bottom:20px;display:flex;gap:12px;align-items:center;">
        <input type="text" placeholder="Rechercher une liste..." style="flex:1;padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
        <button class="btn btn-outline" type="button">🔍 Rechercher</button>
    </div>

    <?php if ($courseDetail): ?>
    <!-- ===== VUE DÉTAIL D'UNE COURSE ===== -->
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
            <h3>Liste #<?php echo $courseDetail['id_course']; ?> — <?php echo htmlspecialchars($courseDetail['date']); ?></h3>
            <div style="display:flex;gap:8px;">
                <a href="../controllers/course_controller.php?action=edit&id=<?php echo $courseDetail['id_course']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                <a href="../controllers/course_controller.php" class="btn btn-outline btn-sm">← Retour</a>
            </div>
        </div>
        <div style="padding:16px;">
            <p><strong>Utilisateur ID :</strong> <?php echo $courseDetail['id_utilisateur']; ?></p>
            <p><strong>Statut :</strong>
                <?php
                    $badgeClass = 'badge-inactif';
                    if ($courseDetail['statut'] === 'En cours') $badgeClass = 'badge-actif';
                    elseif ($courseDetail['statut'] === 'Complétée') $badgeClass = 'badge-bio';
                ?>
                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($courseDetail['statut']); ?></span>
            </p>
        </div>
    </div>

    <!-- Articles de la course -->
    <div class="card">
        <div class="card-header">
            <h3>Articles (<?php echo count($courseDetail['articles']); ?>)</h3>
            <button class="btn btn-primary" onclick="document.getElementById('modalAjoutArticle').classList.add('active')">+ Ajouter un article</button>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Aliment</th>
                        <th>Catégorie</th>
                        <th>Quantité</th>
                        <th>Kcal/100g</th>
                        <th>Acheté</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courseDetail['articles'] as $art): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($art['nom']); ?></td>
                        <td><?php echo htmlspecialchars($art['categorie']); ?></td>
                        <td><?php echo $art['quantite']; ?></td>
                        <td><?php echo $art['kcal_portion']; ?></td>
                        <td>
                            <?php if ($art['achete']): ?>
                                <span class="badge badge-bio">✓ Acheté</span>
                            <?php else: ?>
                                <span class="badge badge-inactif">Non</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <a href="../controllers/course_controller.php?action=toggle_achete&id_course=<?php echo $courseDetail['id_course']; ?>&id_aliment=<?php echo $art['id_aliment']; ?>" class="btn btn-success btn-sm"><?php echo $art['achete'] ? 'Annuler' : 'Acheté'; ?></a>
                            <a href="../controllers/course_controller.php?action=supprimer_article&id_course=<?php echo $courseDetail['id_course']; ?>&id_aliment=<?php echo $art['id_aliment']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Retirer cet article ?')">Retirer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php else: ?>
    <!-- ===== LISTE DES COURSES ===== -->
    <div class="card">
        <div class="card-header">
            <h3>Listes de courses</h3>
            <button class="btn btn-primary" onclick="document.getElementById('modalAjout').classList.add('active')">+ Nouvelle liste</button>
        </div>

        <div class="table-container">
            <table id="tableCourses">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Date</th>
                        <th>Articles</th>
                        <th>Progression</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $c): ?>
                    <tr>
                        <td>#<?php echo $c['id_course']; ?></td>
                        <td>Utilisateur <?php echo $c['id_utilisateur']; ?></td>
                        <td><?php echo htmlspecialchars($c['date']); ?></td>
                        <td><?php echo $c['nb_articles']; ?> articles</td>
                        <td><?php echo intval($c['nb_achetes']); ?>/<?php echo $c['nb_articles']; ?></td>
                        <td>
                            <?php
                                $badgeClass = 'badge-inactif';
                                if ($c['statut'] === 'En cours') $badgeClass = 'badge-actif';
                                elseif ($c['statut'] === 'Complétée') $badgeClass = 'badge-bio';
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($c['statut']); ?></span>
                        </td>
                        <td class="actions">
                            <a href="../controllers/course_controller.php?action=voir&id=<?php echo $c['id_course']; ?>" class="btn btn-success btn-sm">Voir</a>
                            <a href="../controllers/course_controller.php?action=edit&id=<?php echo $c['id_course']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="../controllers/course_controller.php?action=supprimer&id=<?php echo $c['id_course']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette liste ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ===== Modal: Nouvelle liste de courses ===== -->
<div class="modal-overlay <?php echo (!empty($error) && $action === 'ajouter') ? 'active' : ''; ?>" id="modalAjout">
    <div class="modal">
        <h3>Nouvelle liste de courses</h3>
        <form method="POST" action="../controllers/course_controller.php?action=ajouter" id="formAjoutCourse" onsubmit="return validerFormCourse(this)">
            <div class="form-row">
                <div class="form-group">
                    <label>ID Utilisateur</label>
                    <input type="text" name="id_utilisateur" placeholder="Ex: 1" id="ajout_utilisateur" value="1">
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="text" name="date" placeholder="AAAA-MM-JJ" id="ajout_date">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Statut</label>
                    <select name="statut" id="ajout_statut" style="width:100%;padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
                        <option value="">-- Choisir un statut --</option>
                        <option value="Non démarrée">Non démarrée</option>
                        <option value="En cours">En cours</option>
                        <option value="Complétée">Complétée</option>
                    </select>
                </div>
            </div>
            <div id="erreurAjout" style="color:#e63946;margin-top:8px;display:none;"></div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalAjout').classList.remove('active')">Annuler</button>
                <button type="submit" class="btn btn-primary">Créer la liste</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal: Modifier une course ===== -->
<?php if ($courseEdit): ?>
<div class="modal-overlay active" id="modalModif">
    <div class="modal">
        <h3>Modifier la liste #<?php echo $courseEdit['id_course']; ?></h3>
        <form method="POST" action="../controllers/course_controller.php?action=modifier" id="formModifCourse" onsubmit="return validerFormCourse(this)">
            <input type="hidden" name="id" value="<?php echo $courseEdit['id_course']; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>ID Utilisateur</label>
                    <input type="text" name="id_utilisateur" value="<?php echo $courseEdit['id_utilisateur']; ?>">
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="text" name="date" value="<?php echo htmlspecialchars($courseEdit['date']); ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Statut</label>
                    <select name="statut" style="width:100%;padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
                        <option value="Non démarrée" <?php echo $courseEdit['statut'] === 'Non démarrée' ? 'selected' : ''; ?>>Non démarrée</option>
                        <option value="En cours" <?php echo $courseEdit['statut'] === 'En cours' ? 'selected' : ''; ?>>En cours</option>
                        <option value="Complétée" <?php echo $courseEdit['statut'] === 'Complétée' ? 'selected' : ''; ?>>Complétée</option>
                    </select>
                </div>
            </div>
            <div id="erreurModif" style="color:#e63946;margin-top:8px;display:none;"></div>
            <div class="modal-actions">
                <a href="../controllers/course_controller.php" class="btn btn-outline">Annuler</a>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- ===== Modal: Ajouter un article à la course ===== -->
<?php if ($courseDetail): ?>
<div class="modal-overlay" id="modalAjoutArticle">
    <div class="modal">
        <h3>Ajouter un article à la liste #<?php echo $courseDetail['id_course']; ?></h3>
        <form method="POST" action="../controllers/course_controller.php?action=ajouter_article" id="formAjoutArticle" onsubmit="return validerFormArticle(this)">
            <input type="hidden" name="id_course" value="<?php echo $courseDetail['id_course']; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label>Aliment</label>
                    <select name="id_aliment" id="ajout_id_aliment" style="width:100%;padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
                        <option value="">-- Choisir un aliment --</option>
                        <?php foreach ($aliments as $a): ?>
                            <option value="<?php echo $a['id_aliment']; ?>"><?php echo htmlspecialchars($a['nom']); ?> (<?php echo htmlspecialchars($a['categorie']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Quantité</label>
                    <input type="text" name="quantite" placeholder="Ex: 0.5, 1, 12" id="ajout_quantite_article">
                </div>
            </div>
            <div id="erreurArticle" style="color:#e63946;margin-top:8px;display:none;"></div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalAjoutArticle').classList.remove('active')">Annuler</button>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script src="../assets/js/validation.js"></script>

</body>
</html>
