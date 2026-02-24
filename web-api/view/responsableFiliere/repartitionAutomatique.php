<?php
// Les variables sont d�j� d�finies par le contr�leur via extract()
require_once 'view/commun/header.php';
require_once 'view/commun/components.php';
?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
        <h2>R�partition automatique - S�lection des r�gles</h2>
        <?php if (isset($isParcoursGen) && $isParcoursGen): ?>
            <div class="card" style="padding: 0.75rem; margin-bottom: 1rem;">
                <strong>Info :</strong> pour <strong>P_GEN</strong>, la g�n�ration r�affecte le tronc commun sur <strong>S1, S2 et S3 en m�me temps</strong>.
            </div>
        <?php endif; ?>
        <div class="rules-selection">
            <p><strong>S�lectionnez les r�gles que le syst�me doit appliquer pour g�n�rer les groupes automatiquement :</strong></p>
            <?php $promoId = isset($idPromotion) ? $idPromotion : ''; ?>
            <div class="card" style="padding: 0.75rem; margin-bottom: 1rem;">
                <strong>Contraintes personnalis�es :</strong>
                <div style="margin-top: 0.5rem;">
                    <a class="btn btn-secondary" href="<?php echo $baseUrl; ?>/index.php?controller=responsableFiliere&action=configContraintes<?php echo $promoId !== '' ? '&id=' . urlencode($promoId) : ''; ?>">
                        Configurer contraintes / objectifs
                    </a>
                </div>
            </div>
            <form action="<?php echo $baseUrl; ?>/index.php?controller=responsableFiliere&action=genererGroupes" method="POST">
                <input type="hidden" name="id_promo" value="<?php echo $promoId; ?>">
                <div class="card" style="padding: 0.75rem; margin-bottom: 1rem;">
                    <strong>Param�tres de groupes (optionnel)</strong>
                    <div style="display:flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.5rem;">
                        <div>
                            <label for="nb_groupes" style="display:block; font-weight: 600; margin-bottom: 0.25rem;">Nombre de groupes</label>
                            <input id="nb_groupes" type="number" name="nb_groupes" min="1" step="1" style="width: 160px;" placeholder="auto">
                        </div>
                        <div>
                            <label for="taille_groupe" style="display:block; font-weight: 600; margin-bottom: 0.25rem;">Taille de groupe</label>
                            <input id="taille_groupe" type="number" name="taille_groupe" min="1" step="1" style="width: 160px;" placeholder="auto">
                        </div>
                        <div style="display:flex; align-items: flex-end;">
                            <label style="display:flex; gap: 0.5rem; align-items: center;">
                                <input type="checkbox" name="reset_groupes" value="1">
                                R�initialiser les groupes existants
                            </label>
                        </div>
                    </div>
                    <small>Si des groupes existent d�j�, la r�partition se fait dedans (sauf si r�initialisation).</small>
                </div>
                <ul class="rules-list">
                    <li>
                        <input type="checkbox" id="regle2" name="regles[]" value="genres" checked>
                        <label for="regle2">R�partir �quitablement les genres (mixit�)</label>
                    </li>
                    <li>
                        <input type="checkbox" id="regle2b" name="regles[]" value="redoublants">
                        <label for="regle2b">R�partir �quitablement les redoublants</label>
                    </li>
                    <li>
                        <input type="checkbox" id="regle2c" name="regles[]" value="anglophones">
                        <label for="regle2c">R�partir �quitablement les anglophones</label>
                    </li>
                    <li>
                        <input type="checkbox" id="regle2d" name="regles[]" value="apprentis">
                        <label for="regle2d">R�partir �quitablement les apprentis</label>
                    </li>
                    <li>
                        <input type="checkbox" id="regle3" name="regles[]" value="effectifs" checked>
                        <label for="regle3">Maintenir des effectifs �gaux (� 1 �tudiant)</label>
                    </li>
                    <li>
                        <input type="checkbox" id="regleObj" name="regles[]" value="objectifs">
                        <label for="regleObj">Essayer de respecter les objectifs configur�s (si d�finis)</label>
                    </li>
                </ul>
                <div class="action-buttons">
                    <button type="submit" class="btn btn-success">G�n�rer les groupes automatiquement</button>
                </div>
            </form>
        </div>
<?php layoutEnd(); ?>
