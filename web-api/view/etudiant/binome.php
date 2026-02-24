<?php require_once 'view/commun/components.php'; require_once 'view/commun/header.php'; ?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <h2 class="mb-4">Choix du covoiturage</h2>
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h3 class="h5 mb-3">Mon statut</h3>
                    <?php if (!empty($etatBinome['mesChoix'])): ?>
                        <div class="alert alert-info">
                            <strong>Vous avez choisi :</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($etatBinome['mesChoix'] as $choix): ?>
                                    <li>
                                        <?= htmlspecialchars($choix->get('prenom') . ' ' . $choix->get('nom')) ?>
                                        <?php if (in_array($choix->get('id_etudiant'), $etatBinome['reciproques'])): ?>
                                            <span class="badge bg-success"><i class="fas fa-check-circle"></i> Validé</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half"></i> En attente</span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        
                        <?php if (count($etatBinome['reciproques']) > 0): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> <strong>Choix réciproques !</strong> 
                                <?= count($etatBinome['reciproques']) ?> personne(s) vous a/ont également choisi.
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($etatBinome['quiMaChoisi'])): ?>
                            <div class="alert alert-light border">
                                <strong><i class="fas fa-users"></i> Qui m'a choisi :</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach ($etatBinome['quiMaChoisi'] as $demandeur): ?>
                                        <li><?= htmlspecialchars($demandeur->get('prenom') . ' ' . $demandeur->get('nom')) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="index.php?controller=etudiant&action=binome" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler vos choix ?');">
                            <button type="submit" class="btn btn-danger">Annuler mes choix</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-secondary">
                            Vous n'avez pas encore fait de choix de covoiturage.
                        </div>
                        <form method="post" action="index.php?controller=etudiant&action=binome">
                            <div class="form-group mb-3">
                                <label for="id_binome_1"><strong>1er choix</strong> (obligatoire) :</label>
                                <select name="id_binome_1" id="id_binome_1" class="form-control basic-select" required>
                                    <option value="">-- Choisir un camarade --</option>
                                    <?php foreach ($camarades as $camarade): if ($camarade->get('id_etudiant') != $etudiant->get('id_etudiant')): ?>
                                        <option value="<?= $camarade->get('id_etudiant') ?>"><?= htmlspecialchars($camarade->get('nom') . ' ' . $camarade->get('prenom')) ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="id_binome_2"><strong>2e choix</strong> (optionnel) :</label>
                                <select name="id_binome_2" id="id_binome_2" class="form-control basic-select">
                                    <option value="">-- Choisir un camarade --</option>
                                    <?php foreach ($camarades as $camarade): if ($camarade->get('id_etudiant') != $etudiant->get('id_etudiant')): ?>
                                        <option value="<?= $camarade->get('id_etudiant') ?>"><?= htmlspecialchars($camarade->get('nom') . ' ' . $camarade->get('prenom')) ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="id_binome_3"><strong>3e choix</strong> (optionnel) :</label>
                                <select name="id_binome_3" id="id_binome_3" class="form-control basic-select">
                                    <option value="">-- Choisir un camarade --</option>
                                    <?php foreach ($camarades as $camarade): if ($camarade->get('id_etudiant') != $etudiant->get('id_etudiant')): ?>
                                        <option value="<?= $camarade->get('id_etudiant') ?>"><?= htmlspecialchars($camarade->get('nom') . ' ' . $camarade->get('prenom')) ?></option>
                                    <?php endif; endforeach; ?>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Enregistrer mes choix</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="mt-4">
                <div class="alert alert-light border">
                    <h4 class="h6"><i class="fas fa-info-circle"></i> Comment ça marche ?</h4>
                    <p class="mb-2 small">
                        <strong>Groupe de covoiturage (2 à 4 personnes) :</strong>
                    </p>
                    <ul class="mb-0 small">
                        <li>Vous pouvez choisir jusqu'à <strong>3 camarades</strong> pour former un groupe de covoiturage</li>
                        <li>Un groupe de covoiturage est validé lorsque les membres se choisissent mutuellement</li>
                        <li>Les choix sont classés par ordre de préférence (1er, 2e, 3e choix)</li>
                        <li>Vous pouvez modifier vos choix à tout moment avant la validation finale par l'administration</li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</div>
<?php require_once 'view/commun/footer.php'; ?>
