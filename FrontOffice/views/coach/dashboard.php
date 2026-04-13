<?php
require_once __DIR__ . '/../../../config.php';

// Simuler qu'un sportif est connecté
$sportif_id = 3;

// --- GESTION POST FRONT-OFFICE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_pub') {
        $stmt = $pdo->prepare("INSERT INTO publication (id_user, text, date) VALUES (?, ?, NOW())");
        $stmt->execute([$sportif_id, $_POST['text']]);
        header("Location: dashboard.php");
        exit;
    }
    if (isset($_POST['action']) && $_POST['action'] === 'edit_pub') {
        $stmt = $pdo->prepare("UPDATE publication SET text = ? WHERE id_pub = ? AND id_user = ?");
        $stmt->execute([$_POST['text'], $_POST['id_pub'], $sportif_id]);
        header("Location: dashboard.php");
        exit;
    }
    if (isset($_POST['action']) && $_POST['action'] === 'delete_pub') {
        $stmt = $pdo->prepare("DELETE FROM publication WHERE id_pub = ? AND id_user = ?");
        $stmt->execute([$_POST['id_pub'], $sportif_id]);
        header("Location: dashboard.php");
        exit;
    }
}

// Récupération Profil
$current_user = null;
try {
    $stmt_user = $pdo->prepare("SELECT prenom, nom FROM User WHERE user_id = ?");
    $stmt_user->execute([$sportif_id]);
    $current_user = $stmt_user->fetch();

    // Récupérer publications
    $publications = [];
    $stmt_pubs = $pdo->prepare("SELECT * FROM publication WHERE id_user = ? ORDER BY date DESC");
    $stmt_pubs->execute([$sportif_id]);
    $pubs = $stmt_pubs->fetchAll();
    
    foreach($pubs as $p) {
        $stmt_c = $pdo->prepare("SELECT * FROM commentaire WHERE id_pub = ? ORDER BY date ASC");
        $stmt_c->execute([$p['id_pub']]);
        $p['commentaires'] = $stmt_c->fetchAll();
        $publications[] = $p;
    }
} catch (PDOException $e) {
    $db_error = "Base de données non initialisée.";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportFuel — Mes Messages</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/coach.css">
    <style>
        /* Styles spécifiques au chat/message board */
        .pub-card { background: white; margin-bottom: 20px; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .pub-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }
        .pub-date { color: #888; font-size: 0.9em; }
        .pub-body { font-size: 1.1em; line-height: 1.6; color: #333; margin-bottom: 15px; }
        .comment-section { margin-top: 15px; padding-top: 15px; border-top: 2px dashed #eee; }
        .comment-item { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid var(--vert-foret); }
        .comment-header { display: flex; justify-content: space-between; margin-bottom: 5px; font-weight: bold; color: var(--vert-foret); font-size: 0.9em;}
        .form-message { width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 1em; margin-bottom: 15px;}
        .message-actions { display: flex; gap: 10px; margin-top: 10px; }
        
        /* Modals (simplifiés pour FrontOffice) */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000;}
        .modal-overlay.active { display: flex; }
        .modal { background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 500px; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="#" class="navbar-brand">
        <div class="navbar-logo">SF</div>
        <span>Sport<em>Fuel</em></span>
    </a>
    <ul class="navbar-links">
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Mon plan</a></li>
        <li><a href="#">Entraînements</a></li>
        <li><a href="../courses/courses.html">Courses</a></li>
        <li><a href="../aliments/aliments.html">Aliments</a></li>
        <li><a href="dashboard.php" class="active">Mes Messages</a></li>
    </ul>
    <div class="navbar-user"><?php echo $current_user ? substr($current_user['prenom'],0,1).substr($current_user['nom'],0,1) : 'SF'; ?></div>
</nav>

<!-- CONTENU -->
<div class="main-content">
    <div class="coach-header">
        <h1>Mes Demandes & Suivis</h1>
        <p>Laissez vos messages, remarques ou notes. Les administrateurs / coachs vous répondront directement ici.</p>
    </div>

    <!-- Espace pour ajouter une publication -->
    <div class="pub-card" style="background:#f1fdf4; border: 1px solid #dcf5e3;">
        <h3>📮 Nouvelle Demande</h3>
        <form action="dashboard.php" method="POST">
            <input type="hidden" name="action" value="add_pub">
            <textarea name="text" class="form-message" rows="3" placeholder="Écrivez votre message ici..." minlength="5" maxlength="1000" required></textarea>
            <button type="submit" class="btn btn-success">Publier</button>
        </form>
    </div>

    <!-- Historique des Publications -->
    <div class="suivi-history">
        <h2>Mon Historique</h2>

        <?php if(empty($publications)): ?>
            <p>Vous n'avez laissé aucune publication pour le moment.</p>
        <?php else: ?>

            <?php foreach($publications as $pub): ?>
            <div class="pub-card">
                <div class="pub-header">
                    <strong>Message #<?php echo $pub['id_pub']; ?></strong>
                    <span class="pub-date"><?php echo isset($pub['date']) && $pub['date'] ? date('d/m/Y à H:i', strtotime($pub['date'])) : '-'; ?></span>
                </div>
                <div class="pub-body">
                    <?php echo nl2br(htmlspecialchars($pub['text'])); ?>
                </div>
                
                <!-- Actions Utilisateur sur sa publi -->
                <div class="message-actions">
                    <button class="btn btn-outline btn-sm" onclick="openEditModal(<?php echo $pub['id_pub']; ?>, '<?php echo htmlspecialchars(addslashes($pub['text'])); ?>')">Modifier</button>
                    <form action="dashboard.php" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete_pub">
                        <input type="hidden" name="id_pub" value="<?php echo $pub['id_pub']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?');">Supprimer</button>
                    </form>
                </div>

                <!-- Section Réponses Administrateurs -->
                <?php if(count($pub['commentaires']) > 0): ?>
                <div class="comment-section">
                    <h4 style="margin-bottom:10px; color:#555;">Réponses reçeus :</h4>
                    <?php foreach($pub['commentaires'] as $cmt): ?>
                    <div class="comment-item">
                        <div class="comment-header">
                            <span>Admin/Coach</span>
                            <span><?php echo isset($cmt['date']) && $cmt['date'] ? date('d/m à H:i', strtotime($cmt['date'])) : '-'; ?></span>
                        </div>
                        <div><?php echo nl2br(htmlspecialchars($cmt['text'])); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</div>

<!-- Modal Editer Publication -->
<div class="modal-overlay" id="modal-edit-pub">
    <div class="modal">
        <h3 style="margin-bottom: 15px;">Modifier ma demande</h3>
        <form action="dashboard.php" method="POST">
            <input type="hidden" name="action" value="edit_pub">
            <input type="hidden" name="id_pub" id="edit-pub-id" value="">
            
            <textarea name="text" id="edit-pub-text" class="form-message" rows="4" minlength="5" maxlength="1000" required></textarea>
            
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:15px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-edit-pub')">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id_pub, text) {
        document.getElementById('edit-pub-id').value = id_pub;
        document.getElementById('edit-pub-text').value = text;
        document.getElementById('modal-edit-pub').classList.add('active');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }
</script>

<div class="footer">
    &copy; 2026 SportFuel — Nutrition intelligente pour sportifs
</div>

</body>
</html>
