<?php if (!empty($showSidebar)): ?>
<nav class="sidebar">
    <ul class="sidebar-menu">
        <?php $role = $userRole ?? ''; ?>
        <?php $current = $currentPage ?? ''; ?>

        <?php if ($role === 'etudiant'): ?>
            <li>
                <a href="index.php?controller=etudiant&action=notes" class="<?= $current === 'notes' ? 'active' : '' ?>">
                    Mes résultats
                </a>
            </li>
            <li>
                <a href="index.php?controller=etudiant&action=sondages" class="<?= $current === 'sondages' ? 'active' : '' ?>">
                    Sondages
                </a>
            </li>
            <li>
                <a href="index.php?controller=promotions&action=maPromotion" class="<?= $current === 'promotions' ? 'active' : '' ?>">
                    Ma promotion
                </a>
            </li>
            <li>
                <a href="index.php?controller=etudiant&action=binome" class="<?= $current === 'binome' ? 'active' : '' ?>">
                    Mon binôme
                </a>
            </li>
            <li>
                <a href="index.php?controller=promotions&action=monGroupe" class="<?= $current === 'monGroupe' ? 'active' : '' ?>">
                    Mon groupe
                </a>
            </li>
            <li>
                <a href="index.php?controller=profil&action=infos" class="<?= $current === 'infos' ? 'active' : '' ?>">
                    Mes informations
                </a>
            </li>
        
        <?php elseif (in_array($role, ['enseignant', 'responsable_filiere', 'responsable_formation'])): ?>
            
            <li>
                <a href="index.php?controller=promotions&action=promotions" class="<?= $current === 'promotions' ? 'active' : '' ?>">
                    Consulter promotions
                </a>
            </li>

            <?php if ($role === 'responsable_filiere' || $role === 'responsable_formation'): ?>
                <li>
                    <a href="index.php?controller=responsableFiliere&action=constitutionGroupes" class="<?= $current === 'constitutionGroupes' ? 'active' : '' ?>">
                        Constitution groupes
                    </a>
                </li>
                <li>
                    <a href="index.php?controller=responsableFiliere&action=gestionEtudiants" class="<?= $current === 'gestionEtudiants' ? 'active' : '' ?>">
                        Gestion étudiants
                    </a>
                </li>
                <li>
                    <a href="index.php?controller=sondage&action=index" class="<?= $current === 'sondagesGestion' ? 'active' : '' ?>">
                        Gestion Sondages
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($role === 'responsable_formation'): ?>
                <li>
                    <a href="index.php?controller=responsableFormation&action=gestionEnseignants" class="<?= $current === 'gestionEnseignants' ? 'active' : '' ?>">
                        Gestion enseignants
                    </a>
                </li>
            <?php endif; ?>

            <li>
                <a href="index.php?controller=profil&action=infos" class="<?= $current === 'infos' ? 'active' : '' ?>">
                    Mes informations
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>