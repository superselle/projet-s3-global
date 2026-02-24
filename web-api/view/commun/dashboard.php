<?php
require_once 'view/commun/components.php';
require_once 'view/commun/header.php';
$displayName = 'Utilisateur';
if (isset($etudiant) && is_object($etudiant)) {
    $displayName = $etudiant->get('prenom') . ' ' . $etudiant->get('nom');
} elseif (isset($enseignant) && is_object($enseignant)) {
    $displayName = $enseignant->get('prenom') . ' ' . $enseignant->get('nom');
} elseif (isset($userName)) {
    $displayName = $userName;
} elseif (isset($_SESSION['user_prenom'])) {
    $displayName = $_SESSION['user_prenom'];
}
?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <div class="dashboard-welcome">
                <h2>Bienvenue <?= htmlspecialchars($displayName) ?> !</h2>
                <p>
                    <?php 
                    if ($userRole === 'etudiant') echo "Bienvenue dans votre espace étudiant.";
                    elseif ($userRole === 'responsable_filiere') echo "Espace de pilotage de la filière.";
                    elseif ($userRole === 'responsable_formation') echo "Espace responsable formation.";
                    elseif ($userRole === 'enseignant') echo "Espace enseignant - Consultation des promotions.";
                    else echo "Bienvenue dans votre espace.";
                    ?>
                </p>
            </div>

            <?php if ($userRole === 'etudiant'): ?>
                <?php echo card('<h3>Accès rapide</h3><div class="flex flex-wrap gap-lg mt-lg"><a href="index.php?controller=etudiant&action=notes" class="btn btn-primary">Mes résultats</a><a href="index.php?controller=etudiant&action=binome" class="btn btn-secondary">Choix du covoiturage</a><a href="index.php?controller=etudiant&action=sondages" class="btn btn-info">Répondre aux sondages</a></div>'); ?>
                <?php if (isset($etudiant)): echo card('<h3>Mes informations</h3><ul><li>Nom : ' . htmlspecialchars($etudiant->get('prenom') . ' ' . $etudiant->get('nom')) . '</li><li>Email : ' . htmlspecialchars($etudiant->get('email')) . '</li><li>Parcours : ' . htmlspecialchars($etudiant->get('nom_parcours') ?? 'Non défini') . '</li></ul>', '', 'mt-3'); endif; ?>

            <?php elseif ($userRole === 'responsable_filiere'): ?>
                <?php if (isset($enseignant)): echo card('<h3>Mes informations</h3><ul><li>Email : ' . htmlspecialchars($enseignant->get('email')) . '</li><li>Rôle : Responsable Filière</li></ul>', '', 'mt-3'); endif; ?>

            <?php elseif ($userRole === 'responsable_formation'): ?>
                <div class="card">
                    <h3>Accès rapide</h3>
                    <div class="flex flex-wrap gap-lg mt-lg">
                        <a href="index.php?controller=responsableFormation&action=gestionEnseignants" class="btn btn-primary">Gestion enseignants</a>
                        <a href="index.php?controller=promotions&action=promotions" class="btn btn-secondary">Consulter promotions</a>
                        <a href="index.php?controller=sondage&action=index" class="btn btn-info">Gérer les sondages</a>
                    </div>
                </div>
                <?php if (isset($enseignant)): echo card('<h3>Mes informations</h3><ul><li>Email : ' . htmlspecialchars($enseignant->get('email')) . '</li><li>Rôle : Responsable Formation</li></ul>', '', 'mt-3'); endif; ?>

            <?php elseif ($userRole === 'enseignant'): ?>
                <?php echo card('<h3>Accès rapide</h3><div class="flex flex-wrap gap-lg mt-lg"><a href="index.php?controller=promotions&action=promotions" class="btn btn-primary">Consulter les promotions</a></div>'); ?>
                <?php if (isset($enseignant)): echo card('<h3>Mes informations</h3><ul><li>Email : ' . htmlspecialchars($enseignant->get('email')) . '</li><li>Rôle : Enseignant</li></ul>', '', 'mt-3'); endif; ?>

            <?php endif; ?>
        </main>
    </div>
</div>
<?php require_once 'view/commun/footer.php'; ?>