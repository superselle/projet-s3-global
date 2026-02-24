<?php require_once 'view/commun/components.php'; require_once 'view/commun/header.php'; ?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <div class="card">
                <h2><?= htmlspecialchars($sondage->get('nom_sondage')) ?></h2>
                <div class="mb-4 text-muted">
                    <?= nl2br(htmlspecialchars($sondage->get('contenu_sondage'))) ?>
                </div>
                <?php if (!empty($message)): ?>
                    <div class="alert alert-info"><?= $message ?></div>
                <?php endif; ?>
                <form method="post">
                    <fieldset class="form-group">
                        <legend class="col-form-label pt-0">Faites votre choix :</legend>
                        <?php 
                        // D�tection du type d'input
                        $inputType = ($sondage->get('mode_sondage') === 'multiple') ? 'checkbox' : 'radio';
                        ?>
                        <?php foreach ($questions as $q): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" 
                                       type="<?= $inputType ?>" 
                                       name="reponse[]" 
                                       id="rep_<?= $q['id_reponse'] ?>"
                                       value="<?= $q['id_reponse'] ?>"
                                       <?= in_array($q['id_reponse'], $mesReponses) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="rep_<?= $q['id_reponse'] ?>">
                                    <?= htmlspecialchars($q['libelle_reponse'] ?? $q['contenu_reponse']) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-success">Enregistrer ma r�ponse</button>
                        <a href="index.php?controller=etudiant&action=sondages" class="btn btn-secondary">Retour</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<?php require_once 'view/commun/footer.php'; ?>