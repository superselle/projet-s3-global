<?php
require_once 'view/commun/components.php';
require_once 'view/commun/header.php';
?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if ($showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
        <div class="alert alert-success">
            ? La constitution des groupes a �t� enregistr�e avec succ�s !
        </div>
        <?php $promoId = (isset($promotion) && $promotion) ? $promotion->id : ''; ?>
        <div class="card">
            <h2>
                Constitution finale
                <?php if (!empty($promotion)): ?>
                    - <?php echo $promotion->getLabel(); ?>
                <?php endif; ?>
            </h2>
            <p>Les groupes ont �t� enregistr�s dans le syst�me. Les �tudiants pourront consulter leur affectation.</p>
            <?php if (empty($resultats)) echo alertEmpty("Aucun groupe n'a �t� trouv� pour cette promotion.", 'warning'); else: ?>
                <div class="groups-result">
                    <?php foreach ($resultats as $nomGroupe => $etudiants): ?>
                        <div class="group-panel">
                            <div class="group-panel-header">
                                <h3><?php echo $nomGroupe; ?></h3>
                                <span class="badge badge-primary"><?php echo count($etudiants); ?> �tudiants</span>
                            </div>
                            <ul class="student-list">
                                <?php foreach ($etudiants as $e): ?>
                                    <li class="student-item">
                                        <div class="student-name">
                                            <?php echo isset($e['nom_utilisateur']) ? $e['nom_utilisateur'] : ''; ?>
                                            <?php echo isset($e['prenom_utilisateur']) ? $e['prenom_utilisateur'] : ''; ?>
                                        </div>
                                        <div class="student-details">
                                            <span>Mention bac : <?php echo isset($e['mention_bac']) ? $e['mention_bac'] : ''; ?></span>
                                            <span>Moyenne: <?php echo isset($e['moyenne_generale']) ? $e['moyenne_generale'] : 'N/A'; ?></span>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="action-buttons mt-lg">
                <?= linkBack($baseUrl . '/index.php?controller=responsableFiliere&action=constitutionGroupes' . ($promoId !== '' ? '&id=' . $promoId : '')) ?>
            </div>
        </div>
<?php layoutEnd(); ?>
