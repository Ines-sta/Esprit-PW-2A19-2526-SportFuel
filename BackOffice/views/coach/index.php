<?php
require_once __DIR__ . '/../../../config.php';

// --- GESTION DES POST (AJOUTS CRUD) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- CRUD PUBLICATION ---
    
    // Les publications sont ajoutées uniquement par les clients via le FrontOffice.

    // Modifier une publication
    if (isset($_POST['action']) && $_POST['action'] === 'edit_pub') {
        $stmt = $pdo->prepare("UPDATE publication SET text = ? WHERE id_pub = ?");
        $stmt->execute([$_POST['text'], $_POST['id_pub']]);
        header("Location: index.php");
        exit;
    }

    // Supprimer une publication
    if (isset($_POST['action']) && $_POST['action'] === 'delete_pub') {
        $stmt = $pdo->prepare("DELETE FROM publication WHERE id_pub = ?");
        $stmt->execute([$_POST['id_pub']]);
        header("Location: index.php");
        exit;
    }
    
    // --- CRUD COMMENTAIRE ---

    // Ajouter un commentaire manuellement
    if (isset($_POST['action']) && $_POST['action'] === 'add_comment_manual') {
        $stmt = $pdo->prepare("INSERT INTO commentaire (id_pub, text, date) VALUES (?, ?, NOW())");
        $stmt->execute([$_POST['id_pub'], $_POST['text']]);
        header("Location: index.php");
        exit;
    }

    // Ajouter un commentaire (Répondre)
    if (isset($_POST['action']) && $_POST['action'] === 'add_comment') {
        $stmt = $pdo->prepare("INSERT INTO commentaire (id_pub, text, date) VALUES (?, ?, NOW())");
        $stmt->execute([$_POST['id_pub'], $_POST['text']]);
        header("Location: index.php");
        exit;
    }

    // Modifier un commentaire
    if (isset($_POST['action']) && $_POST['action'] === 'edit_comment') {
        $stmt = $pdo->prepare("UPDATE commentaire SET text = ?, date = NOW() WHERE id_cmmnt = ?");
        $stmt->execute([$_POST['text'], $_POST['id_cmmnt']]);
        header("Location: index.php");
        exit;
    }

    // Supprimer un commentaire
    if (isset($_POST['action']) && $_POST['action'] === 'delete_comment') {
        $stmt = $pdo->prepare("DELETE FROM commentaire WHERE id_cmmnt = ?");
        $stmt->execute([$_POST['id_cmmnt']]);
        header("Location: index.php");
        exit;
    }
}

// Initialisations
$publications = [];
$commentaires = [];
$users = [];

try {
    // Récupérer les utilisateurs (pour le select)
    $stmt_users = $pdo->query("SELECT user_id, prenom, nom, email FROM User");
    if ($stmt_users) {
        $users = $stmt_users->fetchAll();
    }

    // Récupérer les publications
    $sql_pubs = "SELECT p.*, u.nom, u.prenom, u.email 
                 FROM publication p 
                 LEFT JOIN User u ON p.id_user = u.user_id 
                 ORDER BY p.date DESC";
    $stmt_pubs = $pdo->query($sql_pubs);
    if ($stmt_pubs) {
        $publications = $stmt_pubs->fetchAll();
    }

    // Récupérer les commentaires
    $sql_comments = "SELECT c.*, p.text as pub_text 
                     FROM commentaire c
                     JOIN publication p ON c.id_pub = p.id_pub
                     ORDER BY c.date DESC";
    $stmt_comments = $pdo->query($sql_comments);
    if ($stmt_comments) {
        $commentaires = $stmt_comments->fetchAll();
    }
} catch (PDOException $e) {
    $db_error = "Veuillez initialiser la base de données (setup_db.php). Erreur : " . $e->getMessage();
}

setlocale(LC_TIME, 'fr_FR.UTF8', 'fr.UTF8', 'fr_FR.UTF-8', 'fr_FR');
$date_jour = date('l j F Y');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SportFuel Admin — Publications & Commentaires</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/coach.css">
</head>
<body>

<!-- SIDEBAR -->
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
        <li><a href="../aliments/aliments.html"><span class="icon">🥗</span> Aliments & courses</a></li>
        <li><a href="../categories/categories.html"><span class="icon">📁</span> Catégories</a></li>
        <li><a href="../courses/courses.html"><span class="icon">🛒</span> Listes de courses</a></li>
        <li><a href="index.php" class="active"><span class="icon">📝</span> Publications & Suivi</a></li>
    </ul>
</aside>

<!-- CONTENU -->
<div class="main-area">
    <div class="topbar">
        <h1>Gestion Publications & Commentaires</h1>
        <span class="date"><?php echo htmlspecialchars($date_jour); ?></span>
    </div>

    <?php if (isset($db_error)): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong>Information :</strong> <?php echo $db_error; ?>
        </div>
    <?php endif; ?>

    <div class="dashboard-grid">
        <!-- Table Publications -->
        <div class="card">
            <div class="card-header">
                <h3>📝 Liste des Publications</h3>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Utilisateur</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($publications) === 0): ?>
                        <tr><td colspan="4" style="text-align: center;">Aucune publication disponible.</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach($publications as $p): ?>
                        <tr>
                            <td><?php echo isset($p['date']) && $p['date'] ? date('d/m/Y H:i', strtotime($p['date'])) : '-'; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars(($p['prenom'] ?? '') . ' ' . ($p['nom'] ?? '')); ?></strong>
                            </td>
                            <td><?php echo nl2br(htmlspecialchars(substr($p['text'], 0, 50))) . (strlen($p['text']) > 50 ? '...' : ''); ?></td>
                            <td>
                                <div class="actions">
                                    <button class="btn btn-primary btn-sm" onclick="openReplyModal(<?php echo $p['id_pub']; ?>)">Répondre</button>
                                    <button class="btn btn-outline btn-sm" onclick="openEditPubModal(<?php echo $p['id_pub']; ?>, '<?php echo htmlspecialchars(addslashes($p['text'])); ?>')">Editer</button>
                                    <form action="index.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_pub">
                                        <input type="hidden" name="id_pub" value="<?php echo $p['id_pub']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette publication ?');">Suppr</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table Commentaires -->
        <div class="card">
            <div class="card-header">
                <h3>💬 Vos Réponses (Commentaires)</h3>
                <button class="btn btn-success btn-sm" onclick="openModal('modal-add-comment-manual')">+ Ajouter Commentaire</button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Réf. Publication</th>
                            <th>Votre Réponse</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($commentaires) === 0): ?>
                        <tr><td colspan="4" style="text-align: center;">Aucun commentaire disponible.</td></tr>
                        <?php endif; ?>

                        <?php foreach($commentaires as $c): ?>
                        <tr>
                            <td><?php echo isset($c['date']) && $c['date'] ? date('d/m/Y H:i', strtotime($c['date'])) : '-'; ?></td>
                            <td><span class="badge badge-local">Pub #<?php echo $c['id_pub']; ?></span></td>
                            <td><?php echo nl2br(htmlspecialchars(substr($c['text'], 0, 50))) . (strlen($c['text']) > 50 ? '...' : ''); ?></td>
                            <td>
                                <div class="actions">
                                    <button class="btn btn-outline btn-sm" onclick="openEditModal(<?php echo $c['id_cmmnt']; ?>, '<?php echo htmlspecialchars(addslashes($c['text'])); ?>')">Editer</button>
                                    <form action="index.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_comment">
                                        <input type="hidden" name="id_cmmnt" value="<?php echo $c['id_cmmnt']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce commentaire ?');">Suppr</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODALS PUBLICATION ================= -->



<!-- Modal Editer Publication -->
<div class="modal-overlay" id="modal-edit-pub">
    <div class="modal">
        <h3>Editer la Publication</h3>
        <form action="index.php" method="POST">
            <input type="hidden" name="action" value="edit_pub">
            <input type="hidden" name="id_pub" id="edit-pub-id" value="">
            <div class="form-group">
                <label>Nouveau Message</label>
                <textarea name="text" id="edit-pub-text" rows="4" minlength="5" maxlength="1000" required></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-edit-pub')">Annuler</button>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODALS COMMENTAIRE ================= -->

<!-- Modal Ajouter Commentaire Manuellement -->
<div class="modal-overlay" id="modal-add-comment-manual">
    <div class="modal">
        <h3>Lier un nouveau Commentaire</h3>
        <form action="index.php" method="POST">
            <input type="hidden" name="action" value="add_comment_manual">
            <div class="form-group">
                <label>Publication Réf.</label>
                <select name="id_pub" required>
                    <option value="">-- Sélectionner une publication --</option>
                    <?php foreach($publications as $p): ?>
                        <option value="<?php echo $p['id_pub']; ?>">Pub #<?php echo $p['id_pub']; ?> - <?php echo substr($p['text'], 0, 30); ?>...</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Votre Réponse</label>
                <textarea name="text" rows="4" minlength="5" maxlength="1000" required></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-add-comment-manual')">Annuler</button>
                <button type="submit" class="btn btn-success">Créer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Répondre Publication (= Add Comment) -->
<div class="modal-overlay" id="modal-reply">
    <div class="modal">
        <h3>Répondre à la Publication</h3>
        <form action="index.php" method="POST">
            <input type="hidden" name="action" value="add_comment">
            <input type="hidden" name="id_pub" id="reply-id-pub" value="">
            <div class="form-group">
                <label>Votre Réponse</label>
                <textarea name="text" rows="4" placeholder="Écrivez votre réponse ici..." minlength="5" maxlength="1000" required></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-reply')">Annuler</button>
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Editer Commentaire -->
<div class="modal-overlay" id="modal-edit">
    <div class="modal">
        <h3>Editer le Commentaire</h3>
        <form action="index.php" method="POST">
            <input type="hidden" name="action" value="edit_comment">
            <input type="hidden" name="id_cmmnt" id="edit-id-cmmnt" value="">
            <div class="form-group">
                <label>Modifier votre Réponse</label>
                <textarea name="text" id="edit-text" rows="4" minlength="5" maxlength="1000" required></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-edit')">Annuler</button>
                <button type="submit" class="btn btn-success">Mettre à jour</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.add('active');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }

    // Handlers Publication
    function openEditPubModal(id_pub, text) {
        document.getElementById('edit-pub-id').value = id_pub;
        document.getElementById('edit-pub-text').value = text;
        openModal('modal-edit-pub');
    }

    // Handlers Commentaire
    function openReplyModal(id_pub) {
        document.getElementById('reply-id-pub').value = id_pub;
        openModal('modal-reply');
    }
    function openEditModal(id_cmmnt, text) {
        document.getElementById('edit-id-cmmnt').value = id_cmmnt;
        document.getElementById('edit-text').value = text;
        openModal('modal-edit');
    }
</script>

</body>
</html>
