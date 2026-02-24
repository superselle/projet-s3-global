<?php
require_once 'view/commun/components.php';

// Initialisation des variables
$promoId = (isset($promotion) && $promotion) ? $promotion->get('id') : (isset($idPromotion) ? $idPromotion : '');
$groupes = isset($groupes) && is_array($groupes) ? $groupes : [];
$studentsAll = isset($studentsAll) && is_array($studentsAll) ? $studentsAll : [];
$nonAffectes = isset($nonAffectes) && is_array($nonAffectes) ? $nonAffectes : [];
$studentsByGroup = isset($studentsByGroup) && is_array($studentsByGroup) ? $studentsByGroup : [];
$objectifs = isset($objectifs) && is_array($objectifs) ? $objectifs : [];
$success = isset($_GET['success']) ? (string)$_GET['success'] : '';
$error = isset($_GET['error']) ? (string)$_GET['error'] : '';

// Affichage des alertes
if ($success !== '') echo alert('Action effectuée.', 'success');
if ($error !== '') echo alert('Une erreur est survenue (' . htmlspecialchars($error) . ').', 'error');

// Préparation de l'index des objectifs
$objIndex = [];
foreach ($objectifs as $o) {
    $gid = isset($o['id_groupe']) ? (int)$o['id_groupe'] : 0;
    $crit = isset($o['critere']) ? (string)$o['critere'] : '';
    $val = isset($o['valeur']) ? (string)$o['valeur'] : '';
    $obj = isset($o['objectif']) ? (int)$o['objectif'] : 0;
    if ($gid <= 0 || $crit === '' || $val === '') continue;
    if (!isset($objIndex[$gid])) $objIndex[$gid] = [];
    $objIndex[$gid][$crit . '|' . $val] = $obj;
}

layoutStart(isset($showSidebar) && $showSidebar);
?>

    <?php
    $content = '<h2>Répartition manuelle';
    if (isset($promotion) && $promotion) $content .= ' - ' . $promotion->getLabel();
    $content .= '</h2><div style="display:flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.75rem;">' . linkBack($baseUrl . '/index.php?controller=responsableFiliere&action=constitutionGroupes' . ($promoId !== '' ? '&id=' . urlencode($promoId) : '')) . '<a class="btn btn-secondary" href="' . $baseUrl . '/index.php?controller=responsableFiliere&action=configContraintes' . ($promoId !== '' ? '&id=' . urlencode($promoId) : '') . '">Configurer contraintes / objectifs</a></div>';
    echo card($content, '', 'mb-3');
    ?>

    <?php
    $content = '<h3>Créer / réinitialiser les groupes</h3><p><small>Si vous n\'avez pas encore de groupes pour cette promotion, vous pouvez les créer ici. Vous pouvez aussi réinitialiser (suppression des groupes + désaffectation des étudiants).</small></p><form method="post" action="' . $baseUrl . '/index.php?controller=responsableFiliere&action=creerGroupesManuels" class="flex flex-wrap flex-end gap-md"><input type="hidden" name="id_promo" value="' . htmlspecialchars($promoId) . '"><div><label for="nb_groupes" style="display:block; font-weight: 600; margin-bottom: 0.25rem;">Nombre de groupes</label><input id="nb_groupes" type="number" name="nb_groupes" min="1" step="1" required style="width: 160px;"></div><div><label for="taille_groupe" style="display:block; font-weight: 600; margin-bottom: 0.25rem;">Taille de groupe</label><input id="taille_groupe" type="number" name="taille_groupe" min="1" step="1" required style="width: 160px;"></div><div class="flex flex-center gap-sm"><input id="reset_groupes" type="checkbox" name="reset_groupes" value="1"><label for="reset_groupes">Réinitialiser (supprimer les groupes existants)</label></div><button class="btn btn-warning" type="submit">Créer</button></form>';
    if (!empty($groupes)) $content .= '<p class="mt-sm"><small>Groupes existants : ' . count($groupes) . '.</small></p>';
    echo card($content, '', 'mb-3');
    ?>

    <div class="card mb-lg">
        <h3>Affecter un étudiant</h3>
        <form method="post" action="<?php echo $baseUrl; ?>/index.php?controller=responsableFiliere&action=affecterEtudiant" class="flex flex-wrap flex-end gap-md">
            <input type="hidden" name="id_promo" value="<?php echo htmlspecialchars($promoId); ?>">
            <div style="min-width: 280px;">
                <label for="id_etudiant" style="display:block; font-weight: 600; margin-bottom: 0.25rem;">Étudiant</label>
                <select id="id_etudiant" name="id_etudiant" required style="width: 100%;">
                    <option value="">-- Choisir --</option>
                    <?php foreach ($studentsAll as $s): ?>
                        <?php
                        $num = isset($s['numero']) ? (int)$s['numero'] : 0;
                        $nom = isset($s['nom']) ? (string)$s['nom'] : '';
                        $prenom = isset($s['prenom']) ? (string)$s['prenom'] : '';
                        $grp = isset($s['groupe']) ? (string)$s['groupe'] : '';
                        ?>
                        <option value="<?php echo $num; ?>"><?php echo $nom . ' ' . $prenom . ($grp !== '' ? ' (' . $grp . ')' : ' (non affecté)'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="min-width: 220px;">
                <label for="id_groupe" style="display:block; font-weight: 600; margin-bottom: 0.25rem;">Groupe</label>
                <select id="id_groupe" name="id_groupe" style="width: 100%;">
                    <option value="0">Non affecté</option>
                    <?php foreach ($groupes as $g): ?>
                        <?php
                        $gid = isset($g->id_groupe) ? (int)$g->id_groupe : 0;
                        $gnom = isset($g->nom_groupe) ? (string)$g->nom_groupe : ('Groupe #' . $gid);
                        $eff = isset($g->effectif) ? (int)$g->effectif : 0;
                        $max = isset($g->effectif_max) ? (int)$g->effectif_max : 0;
                        $suffix = $max > 0 ? " - {$eff}/{$max}" : " - {$eff}";
                        ?>
                        <option value="<?php echo $gid; ?>"><?php echo $gnom . $suffix; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn btn-success" type="submit">Appliquer</button>
        </form>
    </div>

    <div class="card mb-lg">
        <h3>Contrôle rapide (objectifs configurés)</h3>
        <?php if (empty($objIndex)): ?>
            <?php echo alert("Aucun objectif configuré. Vous pouvez en définir dans 'Configurer contraintes / objectifs'.", 'info'); ?>
        <?php else: ?>
            <div style="overflow-x:auto;">
                <table class="table" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Groupe</th>
                            <th>Femmes</th>
                            <th>Hommes</th>
                            <th>Anglophones</th>
                            <th>Redoublants</th>
                            <th>Apprentis</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($groupes as $g): ?>
                            <?php
                            $gid = isset($g->id_groupe) ? (int)$g->id_groupe : 0;
                            $gnom = isset($g->nom_groupe) ? (string)$g->nom_groupe : ('Groupe #' . $gid);
                            $list = isset($studentsByGroup[$gid]) ? $studentsByGroup[$gid] : [];
                            $cntF = 0; $cntH = 0; $cntAnglo = 0; $cntRedo = 0; $cntApp = 0;
                            
                            foreach ($list as $s) {
                                $genre = isset($s['genre']) ? strtolower((string)$s['genre']) : '';
                                if (strpos($genre, 'f') === 0) $cntF++;
                                elseif (strpos($genre, 'h') === 0 || strpos($genre, 'm') === 0) $cntH++;
                                
                                if (!empty($s['est_anglophone'])) $cntAnglo++;
                                if (!empty($s['est_redoublant'])) $cntRedo++;
                                if (!empty($s['est_apprenti'])) $cntApp++;
                            }
                            
                            $tF = isset($objIndex[$gid]['genre|femme']) ? (int)$objIndex[$gid]['genre|femme'] : null;
                            $tH = isset($objIndex[$gid]['genre|homme']) ? (int)$objIndex[$gid]['genre|homme'] : null;
                            $tA = isset($objIndex[$gid]['profil|anglophone']) ? (int)$objIndex[$gid]['profil|anglophone'] : null;
                            $tR = isset($objIndex[$gid]['profil|redoublant']) ? (int)$objIndex[$gid]['profil|redoublant'] : null;
                            $tP = isset($objIndex[$gid]['profil|apprenti']) ? (int)$objIndex[$gid]['profil|apprenti'] : null;
                            ?>
                            <tr>
                                <td><?php echo $gnom; ?></td>
                                <td><?php echo $cntF . ($tF !== null ? ' / ' . $tF : ''); ?></td>
                                <td><?php echo $cntH . ($tH !== null ? ' / ' . $tH : ''); ?></td>
                                <td><?php echo $cntAnglo . ($tA !== null ? ' / ' . $tA : ''); ?></td>
                                <td><?php echo $cntRedo . ($tR !== null ? ' / ' . $tR : ''); ?></td>
                                <td><?php echo $cntApp . ($tP !== null ? ' / ' . $tP : ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <?php if (empty($groupes)): ?>
        <?php echo alert('Aucun groupe pour cette promotion. Créez des groupes ci-dessus puis affectez les étudiants.', 'warning'); ?>
    <?php else: ?>
        <div class="groups-result">
            <?php foreach ($groupes as $g): ?>
                <?php
                $gid = isset($g->id_groupe) ? (int)$g->id_groupe : 0;
                $gnom = isset($g->nom_groupe) ? (string)$g->nom_groupe : ('Groupe #' . $gid);
                $list = isset($studentsByGroup[$gid]) ? $studentsByGroup[$gid] : [];
                ?>
                <div class="group-panel">
                    <div class="group-panel-header">
                        <h3><?php echo $gnom; ?></h3>
                        <span class="badge badge-primary"><?php echo count($list); ?> étudiants</span>
                    </div>
                    <?php if (empty($list)): ?>
                        <?php echo alertEmpty('Aucun étudiant dans ce groupe.'); ?>
                    <?php else: ?>
                        <ul class="student-list">
                            <?php foreach ($list as $s): ?>
                                <?php
                                $num = isset($s['numero']) ? (int)$s['numero'] : 0;
                                $nom = isset($s['nom']) ? (string)$s['nom'] : '';
                                $prenom = isset($s['prenom']) ? (string)$s['prenom'] : '';
                                ?>
                                <li class="student-item flex flex-between flex-center gap-md">
                                    <div>
                                        <div class="student-name"><?php echo $nom . ' ' . $prenom; ?></div>
                                        <div class="student-details"><span>#<?php echo $num; ?></span></div>
                                    </div>
                                    <form method="post" action="<?php echo $baseUrl; ?>/index.php?controller=responsableFiliere&action=affecterEtudiant" class="m-0">
                                        <input type="hidden" name="id_promo" value="<?php echo htmlspecialchars($promoId); ?>">
                                        <input type="hidden" name="id_etudiant" value="<?php echo $num; ?>">
                                        <input type="hidden" name="id_groupe" value="0">
                                        <button type="submit" class="btn btn-secondary">Retirer</button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card" style="margin-top: 1rem;">
            <h3>Étudiants non affectés</h3>
            <?php if (empty($nonAffectes)): ?>
                <div class="alert alert-success">Aucun étudiant non affecté.</div>
            <?php else: ?>
                <ul class="student-list">
                    <?php foreach ($nonAffectes as $s): ?>
                        <?php
                        $num = isset($s['numero']) ? (int)$s['numero'] : 0;
                        $nom = isset($s['nom']) ? (string)$s['nom'] : '';
                        $prenom = isset($s['prenom']) ? (string)$s['prenom'] : '';
                        ?>
                        <li class="student-item">
                            <div class="student-name"><?php echo $nom . ' ' . $prenom; ?> (#<?php echo $num; ?>)</div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; height: 80vh; gap: 2rem;">
        <!-- Partie gauche : Liste des étudiants -->
        <div style="flex: 1; overflow-y: auto; border-right: 1px solid #eee; padding-right: 1rem;">
            <h3>Étudiants disponibles</h3>
            <?php foreach ($studentsAll as $etu): ?>
                <div class="card mb-2 p-2">
                    <strong><?= htmlspecialchars($etu->get('nom')) ?> <?= htmlspecialchars($etu->get('prenom')) ?></strong>
                    <div>ID: <?= $etu->get('id_etudiant') ?> | Semestre: <?= $etu->get('semestre') ?></div>
                    <div>Covoiturage: <?= $etu->get('id_covoiturage') ? $etu->get('id_covoiturage') : 'Non' ?></div>
                    <div>Redoublant: <?= $etu->get('est_redoublant') ? 'Oui' : 'Non' ?> | Anglophone: <?= $etu->get('est_anglophone') ? 'Oui' : 'Non' ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Partie droite : Deux groupes -->
        <div style="flex: 2; display: flex; flex-direction: column; gap: 2rem;">
            <div style="flex: 1; border-bottom: 1px solid #eee; padding-bottom: 1rem;">
                <h3>Groupe 1</h3>
                <?php if (isset($groupes[0])): ?>
                    <?php foreach ($studentsByGroup[$groupes[0]->get('id_groupe')] ?? [] as $etu): ?>
                        <div><?= htmlspecialchars($etu->get('nom')) ?> <?= htmlspecialchars($etu->get('prenom')) ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div style="flex: 1; padding-top: 1rem;">
                <h3>Groupe 2</h3>
                <?php if (isset($groupes[1])): ?>
                    <?php foreach ($studentsByGroup[$groupes[1]->get('id_groupe')] ?? [] as $etu): ?>
                        <div><?= htmlspecialchars($etu->get('nom')) ?> <?= htmlspecialchars($etu->get('prenom')) ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php layoutEnd(); ?>