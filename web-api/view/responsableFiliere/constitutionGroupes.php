<?php require_once 'view/commun/header.php'; ?>

<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (!empty($showSidebar)) require_once 'view/commun/navbar.php'; ?>
        
        <main class="main-content">
            <div class="card">

                <h2>Constitution des groupes</h2>

                <form method="get" action="index.php" class="mb-4" style="max-width:400px;">
                    <input type="hidden" name="controller" value="responsableFiliere">
                    <input type="hidden" name="action" value="constitutionGroupes">
                    <label for="select-promo" class="form-label"><strong>Sélectionner une promotion :</strong></label>
                    <select id="select-promo" name="id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Choisir une promotion --</option>
                        <?php foreach ($promotions as $p):
                            $id = $p->get('annee_scolaire') . '|' . $p->get('semestre') . '|' . $p->get('id_parcours');
                            $label = htmlspecialchars($p->get('annee_scolaire') . ' - S' . $p->get('semestre') . ' - ' . $p->get('nom_parcours'));
                        ?>
                            <option value="<?= $id ?>" <?= (isset($promo) && $promo && $promo->get('id') === $id) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <?php if (isset($promo)): ?>
                    <div class="mb-3">
                        <p>Promotion sélectionnée : <strong><?= htmlspecialchars($promo->get('annee_scolaire') . ' - ' . $promo->get('id_parcours')) ?></strong></p>
                        <p>Nombre d'étudiants à répartir : <strong><?= $nbEtudiants ?? '?' ?></strong></p>
                        <p>Estimation nombre de groupes : <strong><?= $nbGroupes ?? '?' ?></strong></p>
                    </div>
                <?php endif; ?>

                <div class="distribution-methods" style="display:flex; gap:20px; margin-top:20px;">
                    <div class="card text-center" style="flex:1; border:1px solid #ddd; <?php if (!isset($promo)): ?>opacity:0.6;cursor:not-allowed;<?php else: ?>cursor:pointer;<?php endif; ?>"
                        <?php if (isset($promo)): ?>
                            onclick="document.getElementById('formAuto').style.display='block'; window.scrollTo(0, document.body.scrollHeight);"
                        <?php endif; ?>
                    >
                        <div style="font-size:3rem;">🤖</div>
                        <h3>Automatique</h3>
                        <p>L'algorithme répartit équitablement.</p>
                        <?php if (isset($promo)): ?>
                            <button class="btn btn-primary btn-sm mt-2">Choisir</button>
                        <?php endif; ?>
                    </div>
                    <div class="card text-center" style="flex:1; border:1px solid #ddd; <?php if (!isset($promo)): ?>opacity:0.6;cursor:not-allowed;<?php else: ?>cursor:pointer;<?php endif; ?>"
                        <?php if (isset($promo)): ?>
                            onclick="window.location.href='index.php?controller=responsableFiliere&action=repartitionManuelle&id=<?= htmlspecialchars($promo->get('id')) ?>'"
                        <?php endif; ?>
                    >
                        <div style="font-size:3rem;">✋</div>
                        <h3>Manuelle</h3>
                        <p>Glisser-déposer pour constituer les groupes</p>
                    </div>
                </div>

                <?php if (isset($promo)): ?>
                <div id="formAuto" style="display:none; margin-top:20px; border-top:1px solid #eee; padding-top:20px;">
                    <h3>Lancer la répartition automatique</h3>
                    <form method="post" action="index.php?controller=responsableFiliere&action=genererGroupes">
                        <input type="hidden" name="id_promo" value="<?= htmlspecialchars($promo->get('id')) ?>">
                        <div class="form-group">
                            <label>Nombre de groupes souhaités :</label>
                            <input type="number" name="nb_groupes" value="<?= $nbGroupes ?? 4 ?>" class="form-control" style="width:100px;">
                        </div>
                        <div class="form-group">
                            <label>Critères d'équilibrage :</label><br>
                            <label><input type="checkbox" name="regles[]" value="genres" checked> Mixité H/F</label><br>
                            <label><input type="checkbox" name="regles[]" value="notes"> Niveau scolaire (si notes dispo)</label>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Générer les groupes</button>
                    </form>
                </div>
                <?php endif; ?>

            </div>
            
            <div class="mt-3">
                <a href="index.php?controller=responsableFiliere&action=configContraintes&id=<?= $promo->id ?? '' ?>" class="btn btn-info">
                    Configurer les contraintes (Qui ne doit pas être avec qui)
                </a>
            </div>
        </main>
    </div>
</div>

<?php require_once 'view/commun/footer.php'; ?>