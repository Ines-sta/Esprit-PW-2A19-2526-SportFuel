<?php
session_start();
require_once __DIR__ . '/../../../config.php';
require_once __DIR__ . '/../../../controllers/FrontOfficeController.php';

$controller = new FrontOfficeController();
$controller->handlePost();
$data = $controller->getData();

$current_user = $data['current_user'];
$publications = $data['publications'];
$db_error = $data['db_error'];
$focus = $data['focus'] ?? '';
$current_page = basename($_SERVER['PHP_SELF']);
$is_messages_page = ($current_page === 'dashboard.php');
$is_training_page = ($current_page === 'demandes-entrainement.php');
$is_nutrition_page = ($current_page === 'demandes-nutrition.php');
$is_focus_page = in_array($focus, ['entrainement', 'nutrition'], true);
$page_title = 'Mes Demandes & Suivis';
$history_title = 'Mon Historique';

if ($focus === 'entrainement') {
    $page_title = 'Mes Demandes Entraînement';
    $history_title = 'Historique Entraînement';
} elseif ($focus === 'nutrition') {
    $page_title = 'Mes Demandes Nutrition';
    $history_title = 'Historique Nutrition';
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
        .pub-card { background: white; margin-bottom: 22px; padding: 22px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .pub-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 15px; }
        .pub-date { color: #888; font-size: 0.95em; font-weight: 500; letter-spacing: 0.2px; }
        .pub-meta { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-top: 8px; }
        .pub-label { font-weight: 700; color: #22543d; letter-spacing: 0.3px; word-spacing: 2px; }
        .pub-toggle { background: transparent; border: 1px solid #22543d; color: #22543d; border-radius: 8px; padding: 6px 12px; cursor: pointer; }
        .pub-body { font-size: 1.1em; line-height: 1.8; color: #333; margin-bottom: 15px; word-spacing: 2px; }
        .text-preview-content { white-space: pre-wrap; line-height: 1.6; color: #333; min-height: 80px; }
        .comment-section { margin-top: 15px; padding-top: 15px; border-top: 2px dashed #eee; }
        .comment-item { background: #f8fafc; padding: 16px; border-radius: 8px; margin-bottom: 12px; border-left: 4px solid var(--vert-foret); line-height: 1.7; word-spacing: 2px; }
        .comment-header { display: flex; justify-content: space-between; margin-bottom: 5px; font-weight: bold; color: var(--vert-foret); font-size: 0.9em; }
        .form-message { width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 1.02em; margin-bottom: 15px; resize: vertical; line-height: 1.7; word-spacing: 2px; }
        .message-actions { display: flex; gap: 10px; margin-top: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 700; letter-spacing: 0.3px; }
        .textarea-small { min-height: 120px; }
        .pub-section-title { margin-top: 14px; margin-bottom: 8px; font-weight: 800; color: #22543d; letter-spacing: 0.35px; }
        .attachment-list { margin-top: 10px; }
        .attachment-item { margin-bottom: 8px; font-size: 0.95em; }
        .attachment-item a { color: #2f855a; text-decoration: none; }
        .attachment-item a:hover { text-decoration: underline; }
        .file-note { font-size: 0.92em; color: #555; margin-top: -10px; margin-bottom: 15px; line-height: 1.6; word-spacing: 1.5px; }
        .type-select { width: 100%; padding: 14px; border-radius: 8px; border: 1px solid #ddd; background: white; font-family: inherit; font-size: 1.02em; margin-bottom: 15px; line-height: 1.6; word-spacing: 1.5px; }
        .btn { padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .btn-success { background: #22c55e; color: white; }
        .btn-danger { background: #ef4444; color: white; }
        .btn-outline { background: transparent; border: 1px solid #22543d; color: #22543d; }
        .btn-sm { font-size: 0.9em; padding: 8px 14px; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; }
        .modal-overlay.active { display: flex; }
        .modal { background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 500px; }
        .coach-header h1 { font-weight: 800; letter-spacing: 0.4px; margin-bottom: 10px; }
        .coach-header p { line-height: 1.8; word-spacing: 2px; }
        .suivi-history h2 { font-weight: 800; letter-spacing: 0.35px; margin-bottom: 14px; }
    </style>
</head>
<body>

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
        <li><a href="dashboard.php" class="<?php echo $is_messages_page ? 'active' : ''; ?>">Mes Messages</a></li>
        <li><a href="demandes-entrainement.php" class="<?php echo $is_training_page ? 'active' : ''; ?>">Demandes entraînement</a></li>
        <li><a href="demandes-nutrition.php" class="<?php echo $is_nutrition_page ? 'active' : ''; ?>">Demandes nutrition</a></li>
    </ul>
    <div class="navbar-user"><?php echo $current_user ? substr($current_user['prenom'],0,1).substr($current_user['nom'],0,1) : 'SF'; ?></div>
</nav>

<div class="main-content">
    <div class="coach-header">
        <h1><?php echo htmlspecialchars($page_title); ?></h1>
        <p>Laissez vos messages, remarques ou notes. Les administrateurs / coachs vous répondront directement ici.</p>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong>Erreur :</strong> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if ($db_error): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <strong>Information :</strong> <?php echo $db_error; ?>
        </div>
    <?php endif; ?>

    <?php if (!$is_focus_page): ?>
    <div class="pub-card" style="background:#f1fdf4; border: 1px solid #dcf5e3;">
        <h3>📮 Nouvelle Demande</h3>
        <form id="add-pub-form" action="<?php echo htmlspecialchars($current_page); ?>" method="POST" enctype="multipart/form-data" novalidate>
            <input type="hidden" name="action" value="add_pub">

            <div class="form-group">
                <label for="request-type">Type de message</label>
                <select id="request-type" name="type" class="type-select">
                    <option value="" disabled selected>Choisissez un type de message</option>
                    <option value="Demande">Demande — poser une question au coach</option>
                    <option value="Conseil">Conseil — partager une astuce</option>
                    <option value="Problème">Problème — signaler une difficulté</option>
                    <option value="Feedback">Feedback — retour sur un plan / séance</option>
                </select>
            </div>

            <div class="form-group">
                <label for="demande_entrainement">Demande entraînement</label>
                <textarea id="demande_entrainement" name="demande_entrainement" class="form-message textarea-small" placeholder="Décrivez votre besoin en entraînement..."></textarea>
            </div>

            <div class="form-group">
                <label for="demande_nutrition">Demande nutrition</label>
                <textarea id="demande_nutrition" name="demande_nutrition" class="form-message textarea-small" placeholder="Décrivez votre besoin en nutrition..."></textarea>
            </div>

            <div class="form-group">
                <label for="attachments">Ajouter des fichiers</label>
                <input id="attachments" name="attachments[]" type="file" accept="image/*,video/*,application/pdf" multiple style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; background:#fff;">
                <div class="file-note">Images, vidéos ou PDF. Vous pouvez ajouter plusieurs fichiers.</div>
            </div>

            <input type="hidden" name="text" id="text-combined" value="">
            <button type="submit" class="btn btn-success">Publier</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="suivi-history">
        <h2><?php echo htmlspecialchars($history_title); ?></h2>

        <?php if(empty($publications)): ?>
            <p>Vous n'avez laissé aucune publication pour le moment.</p>
        <?php else: ?>

            <?php foreach($publications as $pub): ?>
            <div class="pub-card">
                <div class="pub-header">
                    <?php
                    $displayType = 'Message';
                    if (isset($pub['type']) && trim((string)$pub['type']) !== '') {
                        $displayType = trim((string)$pub['type']);
                    } elseif (preg_match('/Type\s*:\s*(.*?)(?:\n|$)/i', (string)($pub['text'] ?? ''), $typeMatch)) {
                        $displayType = trim($typeMatch[1]);
                    }
                    ?>
                    <strong><?php echo htmlspecialchars(trim(($current_user['prenom'] ?? '') . ' ' . ($current_user['nom'] ?? '')) ?: 'Utilisateur'); ?></strong>
                    <span class="pub-date"><?php echo isset($pub['date']) && $pub['date'] ? date('d/m/Y à H:i', strtotime($pub['date'])) : '-'; ?></span>
                </div>
                <?php
                $rawText = (string)($pub['text'] ?? '');
                $plainTextToShow = trim($rawText);
                $normalizedText = str_replace("\r\n", "\n", $rawText);
                $trainingText = '';
                $nutritionText = '';

                if (preg_match('/Entra(?:î|i)nement\s*:[ \t]*(.*?)(?:\n\s*\n\s*Nutrition\s*:|$)/isu', $normalizedText, $trainingMatch)) {
                    $trainingText = trim($trainingMatch[1]);
                }
                if (preg_match('/Nutrition\s*:[ \t]*(.*)$/isu', $normalizedText, $nutritionMatch)) {
                    $nutritionText = trim($nutritionMatch[1]);
                }

                if ($focus === 'entrainement') {
                    $plainTextToShow = $trainingText !== '' ? $trainingText : trim($rawText);
                } elseif ($focus === 'nutrition') {
                    $plainTextToShow = $nutritionText !== '' ? $nutritionText : trim($rawText);
                } elseif ($trainingText !== '' || $nutritionText !== '') {
                    $plainTextToShow = trim($trainingText . "\n\n" . $nutritionText);
                }
                ?>
                <div class="pub-meta">
                    <div class="pub-label">Type : <?php echo htmlspecialchars($displayType); ?></div>
                    <button type="button" class="pub-toggle" onclick='openTextPreviewModal(<?php echo json_encode($plainTextToShow, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>Afficher le texte</button>
                </div>

                <?php if (!empty($pub['attachments']) && is_array($pub['attachments'])): ?>
                    <div class="attachment-list">
                        <div class="pub-section-title">Fichiers joints :</div>
                        <?php foreach ($pub['attachments'] as $filePath): ?>
                            <div class="attachment-item">
                                <a href="<?php echo htmlspecialchars($filePath); ?>" target="_blank"><?php echo basename($filePath); ?></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <div class="message-actions">
                    <?php if ($is_focus_page): ?>
                    <button class="btn btn-primary btn-sm" onclick="openReplyModal(<?php echo $pub['id_pub']; ?>)">Répondre</button>
                    <?php endif; ?>
                    <?php if (!$is_focus_page): ?>
                    <button class="btn btn-outline btn-sm" onclick="openEditModal(<?php echo $pub['id_pub']; ?>, '<?php echo htmlspecialchars(addslashes($pub['text'])); ?>')">Modifier</button>
                    <?php endif; ?>
                    <form action="<?php echo htmlspecialchars($current_page); ?>" method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="delete_pub">
                        <input type="hidden" name="id_pub" value="<?php echo $pub['id_pub']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?');">Supprimer</button>
                    </form>
                </div>

                <?php if(!empty($pub['commentaires']) && count($pub['commentaires']) > 0): ?>
                <div class="comment-section">
                    <h4 style="margin-bottom:10px; color:#555;">Réponses reçues :</h4>
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

<?php if (!$is_focus_page): ?>
<div class="modal-overlay" id="modal-edit-pub">
    <div class="modal">
        <h3 style="margin-bottom: 15px;">Modifier ma demande</h3>
        <form action="<?php echo htmlspecialchars($current_page); ?>" method="POST">
            <input type="hidden" name="action" value="edit_pub">
            <input type="hidden" name="id_pub" id="edit-pub-id" value="">
            <textarea name="text" id="edit-pub-text" class="form-message" rows="4"></textarea>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:15px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-edit-pub')">Annuler</button>
                <button type="submit" class="btn btn-success">Enregistrer</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="modal-overlay" id="modal-text-preview">
    <div class="modal">
        <h3 style="margin-bottom: 15px;">Texte de la demande</h3>
        <div id="text-preview-content" class="text-preview-content"></div>
        <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:15px;">
            <button type="button" class="btn btn-outline" onclick="closeModal('modal-text-preview')">Fermer</button>
        </div>
    </div>
</div>

<div class="modal-overlay" id="modal-reply">
    <div class="modal">
        <h3 style="margin-bottom: 15px;">Répondre à la demande</h3>
        <form action="<?php echo htmlspecialchars($current_page); ?>" method="POST">
            <input type="hidden" name="action" value="add_comment">
            <input type="hidden" name="id_pub" id="reply-id-pub" value="">
            <textarea name="text" id="reply-text" class="form-message" rows="4" placeholder="Écrivez votre réponse..."></textarea>
            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:15px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modal-reply')">Annuler</button>
                <button type="submit" class="btn btn-success">Envoyer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openTextPreviewModal(text) {
        document.getElementById('text-preview-content').textContent = text || '';
        document.getElementById('modal-text-preview').classList.add('active');
    }

    function openEditModal(id_pub, text) {
        document.getElementById('edit-pub-id').value = id_pub;
        document.getElementById('edit-pub-text').value = text;
        document.getElementById('modal-edit-pub').classList.add('active');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
    }
    function openReplyModal(id_pub) {
        document.getElementById('reply-id-pub').value = id_pub;
        document.getElementById('modal-reply').classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const addForm = document.getElementById('add-pub-form');
        if (addForm) {
            const entrainementField = addForm.querySelector('textarea[name="demande_entrainement"]');
            const nutritionField = addForm.querySelector('textarea[name="demande_nutrition"]');

            function updateExclusiveFields() {
                const hasEntrainement = entrainementField.value.trim().length > 0;
                const hasNutrition = nutritionField.value.trim().length > 0;

                if (hasEntrainement && !hasNutrition) {
                    nutritionField.disabled = true;
                    entrainementField.disabled = false;
                } else if (hasNutrition && !hasEntrainement) {
                    entrainementField.disabled = true;
                    nutritionField.disabled = false;
                } else {
                    entrainementField.disabled = false;
                    nutritionField.disabled = false;
                }
            }

            entrainementField.addEventListener('input', updateExclusiveFields);
            nutritionField.addEventListener('input', updateExclusiveFields);
            updateExclusiveFields();

            addForm.addEventListener('submit', function(e) {
                const type = addForm.querySelector('select[name="type"]').value;
                const entrainement = entrainementField.value.trim();
                const nutrition = nutritionField.value.trim();
                const files = addForm.querySelector('input[name="attachments[]"]').files;

                if (!type || !type.trim()) {
                    alert("Le champ type du message doit etre non vide.");
                    e.preventDefault();
                    return;
                }

                if (!entrainement && !nutrition && files.length === 0) {
                    alert("Veuillez ajouter un texte ou un fichier pour votre demande.");
                    e.preventDefault();
                    return;
                }

                if (entrainement.length > 1000 || nutrition.length > 1000) {
                    alert("Chaque champ texte ne peut pas dépasser 1000 caractères.");
                    e.preventDefault();
                    return;
                }

                document.getElementById('text-combined').value =
                    "Type : " + type + "\n\nEntraînement : " + entrainement + "\n\nNutrition : " + nutrition;
            });
        }

        const editForm = document.querySelector('#modal-edit-pub form');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const text = document.getElementById('edit-pub-text').value;
                if (!text.trim()) {
                    alert("Le message ne peut pas être vide.");
                    e.preventDefault();
                } else if (text.length > 1000) {
                    alert("Le message ne peut pas dépasser 1000 caractères.");
                    e.preventDefault();
                }
            });
        }

        const replyForm = document.querySelector('#modal-reply form');
        if (replyForm) {
            replyForm.addEventListener('submit', function(e) {
                const text = document.getElementById('reply-text').value;
                if (!text.trim()) {
                    alert("Le message ne peut pas être vide.");
                    e.preventDefault();
                } else if (text.length > 200) {
                    alert("Le message ne peut pas dépasser 200 caractères.");
                    e.preventDefault();
                }
            });
        }
    });
</script>

<div class="footer">
    &copy; 2026 SportFuel — Nutrition intelligente pour sportifs
</div>

</body>
</html>