<?php 
require_once 'view/commun/components.php';
require_once 'view/commun/header.php';
$userRoleLabel = $userRoleLabel ?? $_SESSION['role'] ?? 'Utilisateur';
?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <h2 class="mb-4">Mes informations</h2>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <h3 class="card-title text-primary h5">Identité</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Rôle :</strong> <span class="badge badge-info"><?= htmlspecialchars($userRoleLabel) ?></span>
                        </div>
                        <div class="col-md-4">
                            <strong>Nom :</strong> <?= htmlspecialchars($utilisateur->get('nom_utilisateur')) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Prénom :</strong> <?= htmlspecialchars($utilisateur->get('prenom_utilisateur')) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <h3 class="card-title text-primary h5">Contact</h3>
                    <p><strong>Email :</strong> <a href="mailto:<?= htmlspecialchars($utilisateur->get('mail_utilisateur')) ?>"><?= htmlspecialchars($utilisateur->get('mail_utilisateur')) ?></a></p>
                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($utilisateur->get('tel_utilisateur') ?: 'Non renseigné') ?></p>
                    <p><strong>Adresse :</strong> <br><?= nl2br(htmlspecialchars($utilisateur->get('adresse_utilisateur') ?: 'Non renseignée')) ?></p>
                </div>
            </div>

            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">
                    <h3 class="card-title text-primary h5">Informations complémentaires</h3>
                    <?php
                    $genre = $utilisateur->get('genre_utilisateur');
                    $genreLabel = ($genre === 'M') ? 'Homme' : (($genre === 'F') ? 'Femme' : 'Non renseigné');
                    ?>
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Genre :</strong> <?= htmlspecialchars($genreLabel) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Date de naissance :</strong> <?= htmlspecialchars($utilisateur->get('date_naissance') ?: 'Non renseignée') ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Login :</strong> <code><?= htmlspecialchars($utilisateur->get('login_utilisateur')) ?></code>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php require_once 'view/commun/footer.php'; ?>