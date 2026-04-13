<?php
/**
 * BackOffice — Liste des repas
 */
require_once 'Controller/RepasController.php';
$repasController = new RepasController();

$id_plan = $_GET['id_plan'] ?? null;
if ($id_plan) {
    $repasList = $repasController->listRepasByPlan($id_plan);
    require_once 'Controller/PlanAlimentaireController.php';
    $planController = new PlanAlimentaireController();
    $planFiltre = $planController->getPlan($id_plan);
} else {
    $repasList = $repasController->listRepas();
    $planFiltre = null;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repas — SportFuel Admin</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="app-layout">

    <?php include 'View/partials/sidebar.php'; ?>

    <div class="main-content">
        <div class="page-header">
            <div>
                <h1>Repas<?= $planFiltre ? ' — ' . htmlspecialchars($planFiltre->getNom()) : '' ?></h1>
                <div class="page-date"><?= count($repasList) ?> repas enregistre(s)</div>
            </div>
            <div style="display:flex;gap:10px;">
                <?php if ($planFiltre): ?>
                    <a href="index.php?page=back&action=listRepas" class="btn btn-outline btn-sm">Tous les repas</a>
                <?php endif; ?>
                <a href="index.php?page=back&action=addRepas" class="btn btn-accent">+ Nouveau repas</a>
            </div>
        </div>

        <div class="content-area">
            <div class="card">
                <div class="card-header">
                    <h3>Liste des repas</h3>
                </div>
                <div class="search-wrap">
                    <input type="text" id="searchInput" class="search-input" placeholder="Rechercher...">
                </div>
                <?php if (empty($repasList)): ?>
                    <div class="empty-state">Aucun repas enregistre.</div>
                <?php else: ?>
                    <table class="data-table" id="repasTable">
                        <thead>
                            <tr>
                                <th>Plan</th>
                                <th>Jour</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Kcal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($repasList as $repas): ?>
                                <tr data-search="<?= strtolower(htmlspecialchars($repas['plan_nom'] ?? '')) . ' ' . strtolower(htmlspecialchars($repas['description'])) ?>">
                                    <td><?= htmlspecialchars($repas['plan_nom'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($repas['jour']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= htmlspecialchars($repas['type_repas']) ?>">
                                            <?= str_replace('_', ' ', htmlspecialchars($repas['type_repas'])) ?>
                                        </span>
                                    </td>
                                    <td class="td-muted"><?= htmlspecialchars(mb_strimwidth($repas['description'], 0, 60, '...')) ?></td>
                                    <td>
                                        <span class="badge badge-actif"><?= htmlspecialchars($repas['kcal']) ?> kcal</span>
                                    </td>
                                    <td>
                                        <div class="td-actions">
                                            <a href="index.php?page=back&action=updateRepas&id=<?= $repas['id_repas'] ?>" class="btn btn-outline btn-sm">Modifier</a>
                                            <a href="index.php?page=back&action=deleteRepas&id=<?= $repas['id_repas'] ?>"
                                               class="btn btn-danger btn-sm"
                                               onclick="return confirm('Supprimer ce repas ?')">Supprimer</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <?php include 'View/partials/footer.php'; ?>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#repasTable tbody tr').forEach(row => {
        row.style.display = row.dataset.search.includes(q) ? '' : 'none';
    });
});
</script>
</body>
</html>
