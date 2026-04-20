<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportFuel — Ma Liste de Courses</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
    <a href="#" class="navbar-brand">
        <div class="navbar-logo">SF</div>
        <span>Sport<em>Fuel</em></span>
    </a>
    <ul class="navbar-links">
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Mon plan</a></li>
        <li><a href="#">Entraînements</a></li>
        <li><a href="/Esprit-PW-2A19-2526-SportFuel/FrontOffice/controllers/course_controller.php" class="active">Courses</a></li>
        <li><a href="/Esprit-PW-2A19-2526-SportFuel/FrontOffice/controllers/aliment_controller.php">Aliments</a></li>
    </ul>
    <div class="navbar-user">IN</div>
</nav>

<!-- ===== MAIN ===== -->
<div class="main-content">

    <div class="welcome-banner">
        <div>
            <p class="greeting">Liste de courses</p>
            <h2>Vos listes de courses</h2>
            <p class="sub">Produits sélectionnés pour vos performances sportives</p>
        </div>
    </div>

    <!-- Stats (dummy) -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="value">12</div>
            <div class="label">Articles à acheter</div>
        </div>
        <div class="stat-card">
            <div class="value">5</div>
            <div class="label">Déjà achetés</div>
        </div>
        <div class="stat-card">
            <div class="value orange">7</div>
            <div class="label">Restants</div>
        </div>
    </div>

    <!-- Search & Filter Bar (dummy) -->
    <div class="search-bar" style="margin-bottom:20px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <input type="text" placeholder="Rechercher..." style="flex:1;min-width:200px;padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
        <select style="padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
            <option value="">Tous les statuts</option>
            <option>Non démarrée</option>
            <option>En cours</option>
            <option>Complétée</option>
        </select>
        <button class="btn btn-outline" type="button">🔍 Rechercher</button>
    </div>

    <?php if ($courseDetail): ?>
    <!-- ===== VUE DÉTAIL D'UNE COURSE ===== -->
    <?php
    $emojis = [
        'Fruits' => '🍎', 'Légumes' => '🥬', 'Protéines' => '🥩',
        'Céréales' => '🌾', 'Céréales & Féculents' => '🌾',
        'Produits laitiers' => '🥛', 'Huiles & Graisses' => '🫒', 'Fruits secs' => '🌰'
    ];

    // Group articles by category
    $parCategorie = [];
    foreach ($courseDetail['articles'] as $art) {
        $cat = $art['categorie'];
        if (!isset($parCategorie[$cat])) $parCategorie[$cat] = [];
        $parCategorie[$cat][] = $art;
    }
    ?>

    <div class="card">
        <div class="card-header">
            <h3>Liste du <?php echo htmlspecialchars($courseDetail['date']); ?></h3>
            <div style="display:flex;gap:8px;align-items:center;">
                <?php
                    $badgeClass = 'badge-inactif';
                    if ($courseDetail['statut'] === 'En cours') $badgeClass = 'badge-actif';
                    elseif ($courseDetail['statut'] === 'Complétée') $badgeClass = 'badge-bio';
                ?>
                <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($courseDetail['statut']); ?></span>
                <a href="/Esprit-PW-2A19-2526-SportFuel/FrontOffice/controllers/course_controller.php" class="btn btn-outline btn-sm">← Retour</a>
            </div>
        </div>

        <?php foreach ($parCategorie as $cat => $items): ?>
            <?php $emoji = $emojis[$cat] ?? '🍽️'; ?>
            <h4 style="color:var(--vert-foret);margin:16px 0 8px;font-size:14px;padding:0 16px;"><?php echo $emoji . ' ' . htmlspecialchars($cat); ?></h4>
            <ul class="course-list">
                <?php foreach ($items as $art): ?>
                <li class="course-item <?php echo $art['achete'] ? 'checked' : ''; ?>">
                    <input type="checkbox" <?php echo $art['achete'] ? 'checked' : ''; ?> disabled>
                    <span class="item-name"><?php echo htmlspecialchars($art['nom']); ?></span>
                    <span class="item-qty"><?php echo $art['quantite']; ?></span>
                    <span class="item-cat">
                        <?php if ($art['achete']): ?>
                            <span class="badge badge-bio">Acheté</span>
                        <?php else: ?>
                            <span class="badge badge-inactif">À acheter</span>
                        <?php endif; ?>
                    </span>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endforeach; ?>
    </div>

    <?php else: ?>
    <!-- ===== LISTE DE TOUTES LES COURSES ===== -->
    <div class="food-grid">
        <?php foreach ($courses as $c): ?>
        <?php
            $badgeClass = 'badge-inactif';
            if ($c['statut'] === 'En cours') $badgeClass = 'badge-actif';
            elseif ($c['statut'] === 'Complétée') $badgeClass = 'badge-bio';
        ?>
        <div class="food-card">
            <div class="food-card-img">🛒</div>
            <div class="food-card-body">
                <h4>Liste #<?php echo $c['id_course']; ?></h4>
                <p class="category"><?php echo htmlspecialchars($c['date']); ?></p>
                <div style="display:flex;gap:6px;margin-bottom:8px;">
                    <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($c['statut']); ?></span>
                </div>
                <div class="food-card-meta">
                    <span class="kcal"><?php echo $c['nb_articles']; ?> articles</span>
                    <span class="co2"><?php echo intval($c['nb_achetes']); ?>/<?php echo $c['nb_articles']; ?> achetés</span>
                </div>
                <a href="/Esprit-PW-2A19-2526-SportFuel/FrontOffice/controllers/course_controller.php?id=<?php echo $c['id_course']; ?>" class="btn btn-primary btn-sm" style="margin-top:8px;">Consulter</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

</div>

<div class="footer">
    &copy; 2026 SportFuel — Nutrition intelligente pour sportifs | Projet Web 2A Esprit
</div>

</body>
</html>
