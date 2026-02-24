<?php 
require_once 'view/commun/components.php';
require_once 'view/commun/header.php';
$promoId = (isset($promotion) && $promotion) ? $promotion->get('id') : '';
?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <?php if (!isset($userRole) || $userRole !== 'etudiant'): ?>
            <div class="mb-3">
                <a href="index.php?controller=promotions&action=promotions" class="btn btn-outline-secondary btn-sm">
                    <i class="fa fa-arrow-left"></i> Retour aux promotions
                </a>
            </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?= htmlspecialchars($promotion->getLabel()) ?></h2>
                <?php if ($areGroupesPublies): ?>
                    <span class="badge badge-success p-2">Groupes publiés</span>
                <?php else: ?>
                    <span class="badge badge-secondary p-2">Groupes non publiés</span>
                <?php endif; ?>
            </div>
            <!-- Bouton Import/Export CSV - Réservé aux responsables (import notes) -->
            <?php if (isset($userRole) && in_array($userRole, ['responsable_filiere', 'responsable_formation']) && $promoId !== ''): ?>
                <div class="card mb-4">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap gap-2">
                            <a href="index.php?controller=csv&action=index&id=<?= urlencode($promoId) ?>" class="btn btn-secondary btn-sm mr-2">
                                <i class="fa fa-file-excel-o"></i> Import / Export CSV
                            </a>
                            <form method="post" action="index.php?controller=promotions&action=setPublicationGroupes" class="d-inline">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($promoId) ?>">
                                <?php if ($areGroupesPublies): ?>
                                    <input type="hidden" name="published" value="0">
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fa fa-eye-slash"></i> Dépublier les groupes
                                    </button>
                                <?php else: ?>
                                    <input type="hidden" name="published" value="1">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fa fa-eye"></i> Publier les groupes
                                    </button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <h3 class="mb-3">Choisir un groupe</h3>
            <?php if (empty($groupes)) { echo alertEmpty('Aucun groupe disponible pour cette promotion.'); } else { ?>
                <div class="row">
                    <?php foreach ($groupes as $g): ?>
                        <?php
                        // Récupération des données via les Getters
                        $gid = $g->get('id_groupe');
                        $nom = $g->get('nom_groupe');
                        $eff = $g->get('effectif');
                        $effMax = $g->get('effectif_max'); // Assure-toi que ton modèle Groupe a bien cet attribut/getter
                        $titreGroupe = $nom ? $nom : ('Groupe #' . $gid);
                        $effectifLabel = $eff . ($effMax > 0 ? ' / ' . $effMax : '');
                        ?>
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card h-100 shadow-sm hover-effect" 
                                 style="cursor:pointer; transition: transform 0.2s;"
                                 onclick="window.location.href='index.php?controller=promotions&action=detailPromotion&id=<?= urlencode($promoId) ?>&groupe=<?= $gid ?>'"
                                 onmouseover="this.style.transform='translateY(-5px)'"
                                 onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body text-center">
                                    <h4 class="card-title text-primary"><?= htmlspecialchars($titreGroupe) ?></h4>
                                    <hr>
                                    <p class="card-text">
                                        <strong>Effectif :</strong> <?= $effectifLabel ?><br>
                                        <small class="text-muted">ID: <?= $gid ?></small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php } ?>
        </main>
    </div>
</div>
<?php require_once 'view/commun/footer.php'; ?>