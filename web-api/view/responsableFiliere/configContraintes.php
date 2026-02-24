<?php
require_once 'view/commun/components.php';
$promoId = (isset($promotion) && $promotion) ? $promotion->get('id') : '';
$groupes = $groupes ?? [];
$etudiants = $etudiants ?? [];
$contraintes = $contraintes ?? [];
$objectifs = $objectifs ?? [];
$success = $_GET['success'] ?? '';
$selected = $_GET['crit'] ?? 'genre|femme';
list($selectedCritere, $selectedValeur) = explode('|', $selected . '|');
$targets = [];
foreach ($objectifs as $o) {
    $gid = $o['id_groupe'] ?? 0;
    $crit = $o['critere'] ?? '';
    $val = $o['valeur'] ?? '';
    $obj = $o['objectif'] ?? 0;
    if ($gid > 0 && $crit === $selectedCritere && $val === $selectedValeur) {
        $targets[$gid] = $obj;
    }
}
$criteriaOptions = ['genre|femme' => 'Genre : Femmes', 'genre|homme' => 'Genre : Hommes', 'profil|anglophone' => 'Profil : Anglophones', 'profil|redoublant' => 'Profil : Redoublants', 'profil|apprenti' => 'Profil : Apprentis'];
layoutStart(!empty($showSidebar));
?>
            <div class="mb-3">
                <a href="index.php?controller=responsableFiliere&action=constitutionGroupes&id=<?= urlencode($promoId) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Retour
                </a>
            </div>
            <?php if ($success): ?>
                <div class="alert alert-success">Enregistré avec succès.</div>
            <?php endif; ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h2 class="card-title text-primary h4">
                        Configuration des Contraintes & Objectifs
                        <?php if ($promotion): ?>
                            <small class="text-muted">- <?= htmlspecialchars($promotion->getLabel()) ?></small>
                        <?php endif; ?>
                    </h2>
                    <p class="text-muted">Définissez ici les affinités (qui doit être avec qui) et les objectifs chiffrés (ex: 50% de femmes par groupe).</p>
                </div>
            </div>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0 text-primary">Contraintes Individuelles (Incompatibilités / Affinités)</h3>
                </div>
                <div class="card-body">
                    <form method="post" action="index.php?controller=responsableFiliere&action=configContraintes&id=<?= urlencode($promoId) ?>" class="row g-3 align-items-end">
                        <input type="hidden" name="op" value="add_contrainte">
                        <div class="col-md-3">
                            <label for="type_contrainte" class="form-label">Type</label>
                            <select id="type_contrainte" name="type_contrainte" class="form-control">
                                <option value="ENSEMBLE">Doivent être ensemble</option>
                                <option value="SEPARER">Ne doivent PAS être ensemble</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="id_etudiant1" class="form-label">Étudiant 1</label>
                            <select id="id_etudiant1" name="id_etudiant1" class="form-control" required>
                                <option value="">-- Choisir --</option>
                                <?php foreach ($etudiants as $e): ?>
                                    <option value="<?= $e->get('id_etudiant') ?>">
                                        <?= htmlspecialchars($e->get('nom') . ' ' . $e->get('prenom')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="id_etudiant2" class="form-label">Étudiant 2</label>
                            <select id="id_etudiant2" name="id_etudiant2" class="form-control" required>
                                <option value="">-- Choisir --</option>
                                <?php foreach ($etudiants as $e): ?>
                                    <option value="<?= $e->get('id_etudiant') ?>">
                                        <?= htmlspecialchars($e->get('nom') . ' ' . $e->get('prenom')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-success w-100" type="submit">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </form>
                    <hr class="my-4">
                    <?php if (empty($contraintes)) echo alertEmpty('Aucune contrainte enregistrée pour le moment.', 'info') . '<style>.mb-0{margin-bottom:0}</style>'; else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Étudiant 1</th>
                                        <th>Étudiant 2</th>
                                        <th class="text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($contraintes as $c): ?>
                                        <tr>
                                            <td>
                                                <?php if ($c['type_contrainte'] === 'ENSEMBLE'): ?>
                                                    <span class="badge badge-success">Ensemble</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Séparer</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>#<?= htmlspecialchars($c['id_etudiant1']) ?></td>
                                            <td>#<?= htmlspecialchars($c['id_etudiant2']) ?></td>
                                            <td class="text-right">
                                                <form method="post" action="index.php?controller=responsableFiliere&action=configContraintes&id=<?= urlencode($promoId) ?>" class="d-inline">
                                                    <input type="hidden" name="op" value="delete_contrainte">
                                                    <input type="hidden" name="id_contrainte" value="<?= $c['id_contrainte'] ?>">
                                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h3 class="h5 mb-0 text-primary">Objectifs par Groupe</h3>
                </div>
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-4">
                            <label for="critSel" class="form-label fw-bold">Critère à configurer :</label>
                            <select id="critSel" class="form-control" onchange="window.location.href='index.php?controller=responsableFiliere&action=configContraintes&id=<?= urlencode($promoId) ?>&crit=' + encodeURIComponent(this.value)">
                                <?php foreach ($criteriaOptions as $val => $label): ?>
                                    <option value="<?= htmlspecialchars($val) ?>" <?= ($val === $selected) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-8 text-muted">
                            <small>Exemple : Pour avoir 16 femmes au total réparties équitablement, définissez une cible (ex: 4) pour chaque groupe.</small>
                        </div>
                    </div>
                    <?php if (empty($groupes)) { echo alertEmpty('Aucun groupe trouvé. Veuillez d\'abord générer ou créer des groupes.', 'warning'); } else { ?>
                        <form method="post" action="index.php?controller=responsableFiliere&action=configContraintes&id=<?= urlencode($promoId) ?>">
                            <input type="hidden" name="op" value="save_objectifs">
                            <input type="hidden" name="critere" value="<?= htmlspecialchars($selectedCritere) ?>">
                            <input type="hidden" name="valeur" value="<?= htmlspecialchars($selectedValeur) ?>">
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Groupe</th>
                                            <th style="width: 200px;">Cible (Nombre d'étudiants)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupes as $g): ?>
                                            <?php
                                            $gid = $g->get('id_groupe');
                                            $gnom = $g->get('nom_groupe') ?: 'Groupe #' . $gid;
                                            $val = $targets[$gid] ?? 0;
                                            ?>
                                            <tr>
                                                <td class="align-middle fw-bold"><?= htmlspecialchars($gnom) ?></td>
                                                <td>
                                                    <input type="number" name="objectif[<?= $gid ?>]" class="form-control" min="0" step="1" value="<?= $val ?>">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-save"></i> Enregistrer les objectifs
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>
<?php layoutEnd(); ?>