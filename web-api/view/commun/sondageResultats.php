<?php
require_once 'view/commun/header.php';
require_once 'view/commun/components.php';
?>

<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>

        <main class="main-content">
            <?= linkBack($baseUrl . '/index.php?controller=sondage&action=index', 'Retour à la gestion des sondages') ?>

            <div class="card">
                <h2>Résultats du sondage</h2>

                <h3><?php echo htmlspecialchars($sondage->get('nom_sondage')); ?></h3>
                
                <?php if ($sondage->get('contenu_sondage')): ?>
                    <p><?php echo nl2br(htmlspecialchars($sondage->get('contenu_sondage'))); ?></p>
                <?php endif; ?>

                <h4>Statistiques</h4>
                <?php if (!empty($stats)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Réponse</th>
                                <th>Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats as $st): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($st['contenu_reponse']); ?></td>
                                    <td><strong><?php echo $st['nb']; ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucune réponse enregistrée pour le moment.</p>
                <?php endif; ?>

                <h4>Détail (étudiants ayant répondu)</h4>
                <?php if (!empty($lignes)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Numéro</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Login</th>
                                <th>Rang</th>
                                <th>Réponse</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lignes as $l): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($l['numero']); ?></td>
                                    <td><strong><?php echo htmlspecialchars($l['nom']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($l['prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($l['login']); ?></td>
                                    <td>
                                        <?php
                                        $rang = '—';
                                        if (isset($l['rang']) && $l['rang'] !== null) {
                                            $rang = $l['rang'];
                                        }
                                        echo $rang;
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($l['contenu_reponse']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucun étudiant n'a répondu à ce sondage.</p>
                <?php endif; ?>
            </div>
<?php layoutEnd(); ?>