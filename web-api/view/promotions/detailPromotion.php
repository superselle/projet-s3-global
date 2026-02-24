<?php
require_once 'view/commun/components.php';
require_once 'view/commun/header.php';
$role = $userRole ?? $_SESSION['user_role'] ?? '';
$isEtudiant = ($role === 'etudiant');
?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <?php if (empty($promotion)) { echo '<div class="alert alert-warning text-center"><p>Promotion introuvable.</p><a href="index.php?controller=promotions&action=promotions" class="btn btn-secondary">← Retour aux promotions</a></div>'; } else { ?>
                <div class="mb-3">
                    <a href="index.php?controller=promotions&action=detailPromotion&id=<?= urlencode($promotion->get('id')) ?>" class="btn btn-outline-secondary btn-sm">
                        ← Retour à la liste des groupes
                    </a>
                </div>
                <h2 class="text-center mb-4 text-primary">
                    <?= htmlspecialchars($promotion->getLabel()) ?>
                    <?php if (isset($groupe) && $groupe): ?>
                        <span class="text-dark"> — <?= htmlspecialchars($groupe->get('nom_groupe')) ?></span>
                    <?php endif; ?>
                </h2>
                <div class="card shadow-sm border-0">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <?php if (!$isEtudiant): ?>
                                            <th class="text-center">Numéro</th>
                                        <?php endif; ?>
                                        <th>Nom</th>
                                        <th>Prénom</th>
                                        <th>Email</th>
                                        <th class="text-center">Genre</th>
                                        <th class="text-center">Bac</th> 
                                        <?php if (!$isEtudiant): ?>
                                            <th class="text-center">Redoublant</th>
                                            <th class="text-center">Anglophone</th>
                                            <th class="text-center">Apprenti</th>
                                        <?php endif; ?>
                                        <th class="text-center">Groupe</th>
                                    </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($etudiants)): ?>
                                    <tr>
                                        <td colspan="<?= $isEtudiant ? 6 : 8 ?>" class="text-center py-5 text-muted">
                                            Aucun étudiant trouvé dans ce groupe.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($etudiants as $e): ?>
                                        <tr>
                                            <?php if (!$isEtudiant): ?>
                                                <td class="text-center fw-bold text-secondary">
                                                    <?= htmlspecialchars($e->get('id_etudiant')) ?>
                                                </td>
                                            <?php endif; ?>
                                            <td class="fw-bold"><?= htmlspecialchars($e->get('nom')) ?></td>
                                            <td><?= htmlspecialchars($e->get('prenom')) ?></td>
                                            <td>
                                                <a href="mailto:<?= htmlspecialchars($e->get('email')) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($e->get('email')) ?>
                                                </a>
                                            </td>
                                            <td class="text-center"><?= htmlspecialchars($e->get('genre_utilisateur')) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($e->get('id_type')) ?></td>
                                            <?php if (!$isEtudiant): ?>
                                                <td class="text-center">
                                                    <?php if ($e->get('est_redoublant')): ?>
                                                        <span class="badge bg-warning text-dark">Oui</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Non</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($e->get('est_anglophone')): ?>
                                                        <span class="badge bg-info text-dark">Oui</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Non</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($e->get('est_apprenti')): ?>
                                                        <span class="badge bg-primary">Oui</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Non</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; ?>
                                            <td class="text-center">
                                                <?php if ($e->get('nom_groupe')): ?>
                                                    <span class="badge bg-primary">
                                                        <?= htmlspecialchars($e->get('nom_groupe')) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <?php } ?>
        </main>
    </div>
</div>
<?php require_once 'view/commun/footer.php'; ?>