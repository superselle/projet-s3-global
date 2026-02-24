<?php require_once 'view/commun/components.php'; require_once 'view/commun/header.php'; ?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <div class="container-fluid p-0">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h2 class="text-primary mb-3"><?= isset($titre) ? htmlspecialchars($titre) : 'Annuaire des Promotions' ?></h2>
                        <?php if (!empty($soustitre)): ?>
                            <p class="text-muted mb-4"><?= htmlspecialchars($soustitre) ?></p>
                        <?php endif; ?>
                        <?php if (empty($promotions)) { echo alertEmpty('Aucune promotion disponible.'); } else { ?>
                            <div class="row">
                                <?php foreach ($promotions as $p): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100 border hover-effect text-center p-3" style="cursor:pointer;transition:transform .2s" onclick="window.location.href='index.php?controller=promotions&action=detailPromotion&id=<?= urlencode($p->get('id')) ?>'" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                                            <h4 class="text-primary mb-2"><?= htmlspecialchars($p->getLabel()) ?></h4>
                                            <p class="text-muted small mb-3">
                                                <?= htmlspecialchars($p->get('nom_parcours')) ?>
                                                <?php if ($p->get('annee_scolaire')): ?>
                                                    <br><i class="fa fa-calendar"></i> <?= htmlspecialchars($p->get('annee_scolaire')) ?>
                                                <?php endif; ?>
                                            </p>
                                            <span class="btn btn-outline-primary btn-sm stretched-link">Voir la liste</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php require_once 'view/commun/footer.php'; ?>
