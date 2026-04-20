<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportFuel — Catalogue d'Aliments</title>
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
        <li><a href="/Esprit-PW-2A19-2526-SportFuel/FrontOffice/controllers/course_controller.php">Courses</a></li>
        <li><a href="/Esprit-PW-2A19-2526-SportFuel/FrontOffice/controllers/aliment_controller.php" class="active">Aliments</a></li>
    </ul>
    <div class="navbar-user">IN</div>
</nav>

<!-- ===== MAIN ===== -->
<div class="main-content">

    <!-- Welcome -->
    <div class="welcome-banner">
        <div>
            <p class="greeting">Catalogue d'aliments</p>
            <h2>Découvrez nos aliments bio & locaux</h2>
            <p class="sub">Produits tunisiens sélectionnés pour vos performances sportives</p>
        </div>
    </div>

    <!-- Stats (dummy) -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="value">48</div>
            <div class="label">Aliments disponibles</div>
        </div>
        <div class="stat-card">
            <div class="value">32</div>
            <div class="label">Produits bio</div>
        </div>
        <div class="stat-card">
            <div class="value">41</div>
            <div class="label">Produits locaux</div>
        </div>
        <div class="stat-card">
            <div class="value orange">6</div>
            <div class="label">Catégories</div>
        </div>
    </div>

    <!-- Search & Filter Bar (dummy) -->
    <div class="search-bar" style="margin-bottom:20px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <input type="text" placeholder="Rechercher un aliment..." style="flex:1;min-width:200px;padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
        <select style="padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
            <option value="">Toutes les catégories</option>
            <option>Fruits</option>
            <option>Légumes</option>
            <option>Protéines</option>
            <option>Céréales & Féculents</option>
            <option>Produits laitiers</option>
            <option>Huiles & Graisses</option>
        </select>
        <button class="btn btn-outline" type="button">🔍 Rechercher</button>
    </div>

    <!-- Food Grid -->
    <div class="food-grid" id="foodGrid">
        <?php
        $emojis = [
            'Fruits' => '🌰',
            'Légumes' => '🫑',
            'Protéines' => '🥚',
            'Céréales & Féculents' => '🌾',
            'Produits laitiers' => '🥛',
            'Huiles & Graisses' => '🫒'
        ];
        foreach ($aliments as $a):
            $emoji = $emojis[$a['categorie']] ?? '🍽️';
        ?>
        <div class="food-card" data-categorie="<?php echo htmlspecialchars($a['categorie']); ?>" data-bio="<?php echo $a['est_bio']; ?>" data-local="<?php echo $a['est_local']; ?>">
            <div class="food-card-img"><?php echo $emoji; ?></div>
            <div class="food-card-body">
                <h4><?php echo htmlspecialchars($a['nom']); ?></h4>
                <p class="category"><?php echo htmlspecialchars($a['categorie']); ?></p>
                <div style="display:flex;gap:6px;margin-bottom:8px;">
                    <?php if ($a['est_bio']): ?><span class="badge badge-bio">Bio</span><?php endif; ?>
                    <?php if ($a['est_local']): ?><span class="badge badge-local">Local</span><?php endif; ?>
                </div>
                <div class="food-card-meta">
                    <span class="kcal"><?php echo $a['kcal_portion']; ?> kcal / 100g</span>
                    <span class="co2"><?php echo $a['co2_impact']; ?> kg CO₂</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<div class="footer">
    &copy; 2026 SportFuel — Nutrition intelligente pour sportifs | Projet Web 2A Esprit
</div>

</body>
</html>
