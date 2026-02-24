<?php require_once 'view/commun/components.php'; require_once 'view/commun/header.php'; ?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <div class="card">
                <h2>Mes R�sultats</h2>
                <div class="mb-3">
                    <strong>�tudiant :</strong> 
                    <?= htmlspecialchars($etudiant->get('prenom') . ' ' . $etudiant->get('nom')) ?>
                </div>
                <?php if (empty($notes)): ?>
                    <div class="alert alert-info">Aucune note disponible pour le moment.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Mati�re</th>
                                    <th>Note</th>
                                    <th>Commentaire</th> </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notes as $n): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($n['nom_matiere']) ?></td>
                                        <td>
                                            <span class="badge <?= ($n['valeur_note'] >= 10) ? 'badge-success' : 'badge-danger'; ?>">
                                                <?= htmlspecialchars($n['valeur_note']) ?> / 20
                                            </span>
                                        </td>
                                        <td>
                                            <em><?= htmlspecialchars($n['commentaire_note'] ?? '') ?></em>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
<?php require_once 'view/commun/footer.php'; ?>