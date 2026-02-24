<?php require_once 'view/commun/components.php'; layoutStart(!empty($showSidebar)); ?>
        <h2>R�sultat de la r�partition automatique</h2>
        <?php $promoId = (isset($promotion) && $promotion) ? $promotion->id : ''; ?>
        <?php if (!empty($promotion)): ?>
            <p>
                Promotion :
                <strong>
                    <?php echo $promotion->getLabel(); ?>
                </strong>
            </p>
        <?php endif; ?>
        <?php if (empty($resultats)) { echo alertEmpty("Aucun groupe n'a �t� g�n�r� pour cette promotion.", 'warning'); } else { ?>
            <div class="alert alert-success">
                ? Les groupes ont �t� g�n�r�s avec succ�s selon les r�gles s�lectionn�es
            </div>
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
        <div class="action-buttons">
            <a href="<?php echo $baseUrl; ?>/index.php?controller=responsableFiliere&action=constitutionGroupes<?php echo ($promoId !== '' ? '&id=' . $promoId : ''); ?>" class="btn btn-secondary">? Revenir au choix de m�thode</a>
            <a href="<?php echo $baseUrl; ?>/index.php?controller=responsableFiliere&action=validerGroupes<?php echo ($promoId !== '' ? '&id=' . $promoId : ''); ?>" class="btn btn-success">? Valider et enregistrer la constitution</a>
        </div>
<?php layoutEnd(); ?>
