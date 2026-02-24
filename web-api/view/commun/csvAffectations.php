<?php require_once "view/commun/header.php"; ?>
<div class="content-wrapper" style="padding: 30px;">
    <div class="container">
        <a href="index.php?controller=promotions&action=detailPromotion&id=<?= urlencode($pid) ?>" class="btn btn-secondary mb-4">Retour</a>
        
        <h2 class="mb-4">Gestion CSV</h2>
        
        <?php if ($msg): ?>
            <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <!-- EXPORT -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Exporter les donnees</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Export Pedagogique</h6>
                        <p class="text-muted small">9 colonnes : numero, nom, prenom, genre, email, telephone, groupe, parcours</p>
                        <a href="?controller=csv&action=exportPromotionMinimum&id=<?= urlencode($pid) ?>" class="btn btn-primary btn-sm">Telecharger</a>
                    </div>
                    <div class="col-md-6">
                        <h6>Export Complet</h6>
                        <p class="text-muted small">15 colonnes : toutes les informations (redoublant, apprenti, anglophone...)</p>
                        <?php if (isset($userRole) && in_array($userRole, ['responsable_filiere', 'responsable_formation'])): ?>
                            <a href="?controller=csv&action=exportPromotionComplete&id=<?= urlencode($pid) ?>" class="btn btn-success btn-sm">Telecharger</a>
                        <?php else: ?>
                            <button class="btn btn-success btn-sm" disabled>Reserve responsables</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- IMPORT -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Importer les notes</h5>
            </div>
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="op" value="importNotes">
                    
                    <div class="form-group">
                        <label>Fichier CSV</label>
                        <input type="file" name="csv" class="form-control-file" required accept=".csv">
                        <small class="form-text text-muted">Format: numero;nom;prenom;Matiere1;Matiere2...</small>
                    </div>
                    
                    <div class="alert alert-info small">
                        <strong>Format attendu:</strong> numero;nom;prenom;note1;note2... avec separateur point-virgule
                    </div>
                    
                    <button type="submit" class="btn btn-success">Importer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once "view/commun/footer.php"; ?>

