<?php
require_once 'view/commun/components.php';
require_once 'view/commun/header.php';
?>

<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>

        <main class="main-content">
            <div class="card">
                <h2>Gestion des sondages</h2>

                <?php if (!empty($success) && $success === 'created'): ?>
                    <div class="alert alert-success">Sondage créé avec succès.</div>
                <?php endif; ?>
                <?php if (!empty($error) && $error === 'notfound'): ?>
                    <div class="alert alert-danger">Sondage introuvable.</div>
                <?php endif; ?>

                <p>
                    <a class="btn btn-primary" href="<?php echo $baseUrl; ?>/index.php?controller=sondage&action=creer">Créer un sondage</a>
                </p>

                <?php if (!empty($sondages)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Contenu</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sondages as $s): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($s->get('id_sondage')); ?></td>
                                    <td><strong><?php echo htmlspecialchars($s->get('nom_sondage')); ?></strong></td>
                                    <td><?php echo nl2br(htmlspecialchars($s->get('contenu_sondage'))); ?></td>
                                    <td>
                                        <a class="btn btn-secondary" href="<?php echo $baseUrl; ?>/index.php?controller=sondage&action=resultats&id=<?php echo $s->get('id_sondage'); ?>">Résultats</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucun sondage pour le moment.</p>
                <?php endif; ?>
            </div>
<?php layoutEnd(); ?>