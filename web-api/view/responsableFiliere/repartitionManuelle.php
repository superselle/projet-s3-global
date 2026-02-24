<?php
require_once 'view/commun/header.php';

// Initialisation des variables
$promoId = (isset($promotion) && $promotion) ? $promotion->get('id') : '';
$promoLabel = (isset($promotion) && $promotion) ? $promotion->getLabel() : '';
$groupes = isset($groupes) && is_array($groupes) ? $groupes : [];
$studentsAll = isset($studentsAll) && is_array($studentsAll) ? $studentsAll : [];
$nonAffectes = isset($nonAffectes) && is_array($nonAffectes) ? $nonAffectes : [];
$studentsByGroup = isset($studentsByGroup) && is_array($studentsByGroup) ? $studentsByGroup : [];
$success = isset($_GET['success']) ? (string)$_GET['success'] : '';
$error = isset($_GET['error']) ? (string)$_GET['error'] : '';
?>

<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (!empty($showSidebar)) require_once 'view/commun/navbar.php'; ?>
        
        <main class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Répartition Manuelle</h2>
                    <p class="text-muted mb-0"><?= htmlspecialchars($promoLabel) ?> — <?= count($studentsAll) ?> étudiants</p>
                </div>
                <a href="index.php?controller=responsableFiliere&action=constitutionGroupes" class="btn btn-secondary">
                    ← Retour
                </a>
            </div>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success === 'created' ? 'Groupes créés avec succès !' : ($success === 'affected' ? 'Affectation enregistrée !' : 'Opération réussie !') ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">Erreur : <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <!-- Formulaire création groupes si aucun groupe -->
            <?php if (empty($groupes)): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h4>Créer des groupes</h4>
                    <form method="post" action="index.php?controller=responsableFiliere&action=creerGroupesManuels" class="d-flex gap-3 align-items-end flex-wrap">
                        <input type="hidden" name="id_promo" value="<?= htmlspecialchars($promoId) ?>">
                        <div>
                            <label class="form-label">Nombre de groupes</label>
                            <input type="number" name="nb_groupes" value="4" min="1" max="20" class="form-control" style="width:100px;" required>
                        </div>
                        <div>
                            <label class="form-label">Taille max/groupe</label>
                            <input type="number" name="taille_groupe" value="18" min="5" max="30" class="form-control" style="width:100px;">
                        </div>
                        <button type="submit" class="btn btn-primary">Créer les groupes</button>
                    </form>
                </div>
            </div>
            <?php else: ?>

            <!-- Actions rapides -->
            <div class="d-flex gap-2 mb-4 flex-wrap">
                <form method="post" action="index.php?controller=responsableFiliere&action=creerGroupesManuels" class="d-inline">
                    <input type="hidden" name="id_promo" value="<?= htmlspecialchars($promoId) ?>">
                    <input type="hidden" name="nb_groupes" value="1">
                    <input type="hidden" name="taille_groupe" value="18">
                    <button type="submit" class="btn btn-outline-primary btn-sm">+ Ajouter un groupe</button>
                </form>
                <form method="post" action="index.php?controller=responsableFiliere&action=creerGroupesManuels" class="d-inline" onsubmit="return confirm('Cela supprimera tous les groupes et désaffectera les étudiants. Continuer ?');">
                    <input type="hidden" name="id_promo" value="<?= htmlspecialchars($promoId) ?>">
                    <input type="hidden" name="nb_groupes" value="4">
                    <input type="hidden" name="taille_groupe" value="18">
                    <input type="hidden" name="reset_groupes" value="1">
                    <button type="submit" class="btn btn-outline-danger btn-sm">Réinitialiser les groupes</button>
                </form>
            </div>

            <!-- Tableau des étudiants -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <strong>Liste des étudiants</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nom</th>
                                    <th>Prénom</th>
                                    <th>Redoublant</th>
                                    <th>Anglophone</th>
                                    <th style="width:200px;">Groupe</th>
                                    <th style="width:120px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($studentsAll as $etu): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($etu->get('nom')) ?></td>
                                    <td><?= htmlspecialchars($etu->get('prenom')) ?></td>
                                    <td class="text-center">
                                        <?php if ($etu->get('est_redoublant')): ?>
                                            <span class="badge bg-warning text-dark">Oui</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted">Non</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($etu->get('est_anglophone')): ?>
                                            <span class="badge bg-info">Oui</span>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted">Non</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <form method="post" action="index.php?controller=responsableFiliere&action=affecterEtudiant" class="d-flex gap-2">
                                            <input type="hidden" name="id_promo" value="<?= htmlspecialchars($promoId) ?>">
                                            <input type="hidden" name="id_etudiant" value="<?= $etu->get('id_etudiant') ?>">
                                            <select name="id_groupe" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="0" <?= !$etu->get('id_groupe') ? 'selected' : '' ?>>-- Non affecté --</option>
                                                <?php foreach ($groupes as $g): ?>
                                                    <option value="<?= $g->get('id_groupe') ?>" <?= $etu->get('id_groupe') == $g->get('id_groupe') ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($g->get('nom_groupe')) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <?php if ($etu->get('id_groupe')): ?>
                                        <form method="post" action="index.php?controller=responsableFiliere&action=affecterEtudiant" class="d-inline">
                                            <input type="hidden" name="id_promo" value="<?= htmlspecialchars($promoId) ?>">
                                            <input type="hidden" name="id_etudiant" value="<?= $etu->get('id_etudiant') ?>">
                                            <input type="hidden" name="id_groupe" value="0">
                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Retirer du groupe">
                                                <i class="fa fa-times"></i> Retirer
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Résumé des groupes -->
            <h4 class="mt-4">Résumé des groupes</h4>
            <div class="row">
                <?php foreach ($groupes as $groupe): 
                    $idG = $groupe->get('id_groupe');
                    $membres = isset($studentsByGroup[$idG]) ? $studentsByGroup[$idG] : [];
                ?>
                <div class="col-md-4 col-lg-3 mb-3">
                    <div class="card h-100">
                        <div class="card-header bg-secondary text-white py-2">
                            <strong><?= htmlspecialchars($groupe->get('nom_groupe')) ?></strong>
                            <span class="badge bg-light text-dark float-end"><?= count($membres) ?>/<?= $groupe->get('effectif_max') ?: 18 ?></span>
                        </div>
                        <div class="card-body p-2" style="max-height:200px; overflow-y:auto;">
                            <?php if (empty($membres)): ?>
                                <p class="text-muted text-center small mb-0">Aucun étudiant</p>
                            <?php else: ?>
                                <ul class="list-unstyled mb-0 small">
                                    <?php foreach ($membres as $m): ?>
                                        <li><?= htmlspecialchars($m->get('nom') . ' ' . $m->get('prenom')) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php endif; ?>

        </main>
    </div>
</div>

<?php require_once 'view/commun/footer.php'; ?>