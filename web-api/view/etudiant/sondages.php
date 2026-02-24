<?php 
require_once 'view/commun/components.php';
require_once 'view/commun/header.php'; 
?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (!empty($showSidebar)) require_once 'view/commun/navbar.php'; ?>
        <main class="main-content">
            <div class="card">
                <div class="card-header bg-white border-bottom-0">
                    <h3 class="mb-0">Sondages disponibles</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($sondages)): ?>
                        <div class="alert alert-info">
                            Aucun sondage pour votre promotion en ce moment.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 25%;">Titre</th>
                                        <th style="width: 55%;">Description</th>
                                        <th style="width: 20%;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($sondages as $s): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-primary">
                                                    <?= htmlspecialchars($s->get('nom_sondage')) ?>
                                                </strong>
                                            </td>
                                            <td class="text-muted">
                                                <?= nl2br(htmlspecialchars($s->get('contenu_sondage'))) ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="index.php?controller=etudiant&action=repondreSondage&id=<?= $s->get('id_sondage') ?>" 
                                                   class="btn btn-primary btn-sm">
                                                    Répondre
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
<?php layoutEnd(); ?>