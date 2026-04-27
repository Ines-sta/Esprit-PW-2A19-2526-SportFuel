<?php
// Vue FO: Listes de courses
// Reçoit : $courses, $stats, $courseDetail, $users, $filtre_*, $success,
// $currentUserName et getUserName().
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportFuel — Mes Listes de Courses</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<!-- ===== NAVBAR ===== -->
<nav class="navbar">
    <a href="../../index.php" class="navbar-brand">
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
    <div class="navbar-user"><?php echo htmlspecialchars(strtoupper(substr($currentUserName, 0, 2))); ?></div>
</nav>

<!-- ===== MAIN ===== -->
<div class="main-content">

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="welcome-banner">
        <div>
            <p class="greeting">Listes de courses</p>
            <h2>Vos listes de courses</h2>
            <p class="sub">Connecté en tant que <?php echo htmlspecialchars($currentUserName); ?>. Suivez vos achats et vos apports énergétiques.</p>
        </div>
    </div>

    <?php if ($courseDetail):
        $totalKcal = Course::kcalTotal($courseDetail['articles']);
    ?>
    <!-- ===== VUE DÉTAIL ===== -->
    <?php
    $emojis = [
        'Fruits' => '🍎', 'Légumes' => '🥬', 'Protéines' => '🥩',
        'Céréales' => '🌾', 'Céréales & Féculents' => '🌾',
        'Produits laitiers' => '🥛', 'Huiles & Graisses' => '🫒', 'Fruits secs' => '🌰'
    ];

    $parCategorie = [];
    foreach ($courseDetail['articles'] as $art) {
        $cat = $art['categorie'];
        if (!isset($parCategorie[$cat])) $parCategorie[$cat] = [];
        $parCategorie[$cat][] = $art;
    }
    ?>

    <div class="card">
        <div class="card-header">
            <h3><?php echo htmlspecialchars($courseDetail['nom']); ?></h3>
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
        <div style="padding:0 16px 16px;color:#6c757d;font-size:14px;">
            <span><?php echo htmlspecialchars($courseDetail['date']); ?></span> &middot;
            <span><?php echo htmlspecialchars(getUserName($users, $courseDetail['id_utilisateur'])); ?></span> &middot;
            <strong><?php echo round($totalKcal); ?> kcal estimées</strong>
            <small>(unité « pièce » exclue)</small>
        </div>

        <?php foreach ($parCategorie as $cat => $items):
            $emoji = $emojis[$cat] ?? '🍽️';
        ?>
            <h4 style="color:var(--vert-foret);margin:16px 0 8px;font-size:14px;padding:0 16px;"><?php echo $emoji . ' ' . htmlspecialchars($cat); ?></h4>
            <div class="table-container" style="padding:0 16px;">
                <table>
                    <thead>
                        <tr>
                            <th></th>
                            <th>Aliment</th>
                            <th>Quantité</th>
                            <th>Kcal</th>
                            <th>Statut</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $art):
                            $kcal = Course::kcalArticle($art);
                        ?>
                        <tr class="<?php echo $art['achete'] ? 'checked' : ''; ?>">
                            <td>
                                <a href="/Esprit-PW-2A19-2526-SportFuel/FrontOffice/controllers/course_controller.php?action=toggle_achete&id_course=<?php echo $courseDetail['id_course']; ?>&id_aliment=<?php echo $art['id_aliment']; ?>">
                                    <input type="checkbox" <?php echo $art['achete'] ? 'checked' : ''; ?> onclick="return false;">
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($art['nom']); ?></td>
                            <td><?php echo $art['quantite'] . ' ' . htmlspecialchars($art['unite']); ?></td>
                            <td><?php echo $kcal === null ? '—' : round($kcal); ?></td>
                            <td>
                                <?php if ($art['achete']): ?>
                                    <span class="badge badge-bio">Acheté</span>
                                <?php else: ?>
                                    <span class="badge badge-inactif">À acheter</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/Esprit-PW-2A19-2526-SportFuel/FrontOffice/controllers/course_controller.php?action=toggle_achete&id_course=<?php echo $courseDetail['id_course']; ?>&id_aliment=<?php echo $art['id_aliment']; ?>" class="btn btn-outline btn-sm">
                                    <?php echo $art['achete'] ? 'Annuler' : 'Marquer acheté'; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>

        <div style="padding:16px;text-align:right;border-top:1px solid #eee;margin-top:8px;">
            <strong>Total kcal :</strong> <?php echo round($totalKcal); ?> kcal
        </div>
    </div>

    <?php else: ?>
    <!-- ===== LISTE DE TOUTES LES COURSES ===== -->

    <!-- Stats dynamiques -->
    <div class="stat-cards">
        <div class="stat-card">
            <div class="value"><?php echo $stats['total']; ?></div>
            <div class="label">Listes de courses</div>
        </div>
        <div class="stat-card">
            <div class="value"><?php echo $stats['articles_moyen']; ?></div>
            <div class="label">Articles / liste (moy.)</div>
        </div>
        <div class="stat-card">
            <div class="value orange"><?php echo $stats['pourcent_achetes']; ?>%</div>
            <div class="label">Articles achetés</div>
        </div>
        <div class="stat-card">
            <div class="value orange"><?php echo $stats['total_kcal_global']; ?></div>
            <div class="label">Kcal cumulées</div>
        </div>
    </div>

    <!-- Recherche & filtres -->
    <form method="GET" action="course_controller.php" class="search-bar" style="margin-bottom:20px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
        <input type="text" name="q" value="<?php echo htmlspecialchars($filtre_q); ?>" placeholder="Rechercher par nom..." style="flex:1;min-width:200px;padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
        <select name="statut_filtre" style="padding:10px 16px;border:1px solid #ddd;border-radius:8px;font-size:14px;">
            <option value="">Tous les statuts</option>
            <option value="Non démarrée" <?php echo $filtre_statut === 'Non démarrée' ? 'selected' : ''; ?>>Non démarrée</option>
            <option value="En cours"     <?php echo $filtre_statut === 'En cours'     ? 'selected' : ''; ?>>En cours</option>
            <option value="Complétée"    <?php echo $filtre_statut === 'Complétée'    ? 'selected' : ''; ?>>Complétée</option>
        </select>
        <button class="btn btn-primary" type="submit">🔍 Rechercher</button>
        <a class="btn btn-outline" href="course_controller.php">Réinitialiser</a>
    </form>

    <p style="margin-bottom:12px;color:#6c757d;"><?php echo count($courses); ?> liste(s) trouvée(s).</p>
    <div class="food-grid">
        <?php if (empty($courses)): ?>
            <p style="color:#6c757d;">Aucune liste ne correspond à vos critères.</p>
        <?php else: ?>
            <?php foreach ($courses as $c):
                $badgeClass = 'badge-inactif';
                if ($c['statut'] === 'En cours') $badgeClass = 'badge-actif';
                elseif ($c['statut'] === 'Complétée') $badgeClass = 'badge-bio';
            ?>
            <div class="food-card">
                <div class="food-card-img">🛒</div>
                <div class="food-card-body">
                    <h4><?php echo htmlspecialchars($c['nom']); ?></h4>
                    <p class="category"><?php echo htmlspecialchars($c['date']); ?> &middot; <?php echo htmlspecialchars(getUserName($users, $c['id_utilisateur'])); ?></p>
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
        <?php endif; ?>
    </div>
    <?php endif; ?>

</div>

<div class="footer">
    &copy; 2026 SportFuel — Nutrition intelligente pour sportifs | Projet Web 2A Esprit
</div>

</body>
</html>
