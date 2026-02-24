<?php 
require_once 'view/commun/header.php';
require_once 'view/commun/components.php';

// $statsPromo contient les statistiques de tous les groupes
// $promoId contient l'ID de la promotion
?>

<div class="content-wrapper" style="padding: 20px;">
    <h2><i class="fas fa-chart-bar"></i> Contrôle Qualité des Groupes</h2>
    
    <?php if (!empty($promoId)): ?>
        <div class="mb-3">
            <a href="index.php?controller=responsableFiliere&action=constitutionGroupes&id=<?= urlencode($promoId) ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la constitution
            </a>
        </div>
    <?php endif; ?>
    
    <?php if (empty($statsPromo)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Aucun groupe n'a encore été constitué pour cette promotion.
        </div>
    <?php else: ?>
        
        <!-- Vue d'ensemble -->
        <div class="card mb-4">
            <div class="card-header">
                <h4><i class="fas fa-tachometer-alt"></i> Vue d'ensemble</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-box">
                            <div class="stat-label">Nombre de groupes</div>
                            <div class="stat-value"><?= count($statsPromo) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box">
                            <div class="stat-label">Total étudiants</div>
                            <div class="stat-value"><?= array_sum(array_column($statsPromo, 'total')) ?></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box">
                            <div class="stat-label">Score moyen</div>
                            <div class="stat-value">
                                <?php 
                                $scoreMoyen = round(array_sum(array_column($statsPromo, 'score_conformite')) / count($statsPromo), 1);
                                $badgeClass = $scoreMoyen >= 80 ? 'badge-success' : ($scoreMoyen >= 60 ? 'badge-warning' : 'badge-danger');
                                ?>
                                <span class="badge <?= $badgeClass ?>"><?= $scoreMoyen ?>%</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box">
                            <div class="stat-label">Covoiturages</div>
                            <div class="stat-value">
                                <?php 
                                $covoitRespectes = array_sum(array_column($statsPromo, 'covoit_respectes'));
                                $covoitTotal = array_sum(array_column($statsPromo, 'covoit_total'));
                                echo $covoitRespectes . ' / ' . $covoitTotal;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Détail par groupe -->
        <div class="row">
            <?php foreach ($statsPromo as $stats): ?>
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-users"></i> <?= htmlspecialchars($stats['nom_groupe']) ?>
                            </h5>
                            <span class="badge <?= $stats['score_conformite'] >= 80 ? 'badge-success' : ($stats['score_conformite'] >= 60 ? 'badge-warning' : 'badge-danger') ?> badge-lg">
                                Score: <?= $stats['score_conformite'] ?>%
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="stat-group mb-3">
                                <div class="stat-row">
                                    <i class="fas fa-user-friends text-primary"></i>
                                    <strong>Effectif:</strong> 
                                    <span><?= $stats['total'] ?> étudiants</span>
                                </div>
                            </div>
                            
                            <div class="stat-group mb-3">
                                <h6 class="text-muted mb-2"><i class="fas fa-venus-mars"></i> Répartition Genre</h6>
                                <div class="stat-row">
                                    <span class="stat-label">Hommes:</span>
                                    <span class="stat-value">
                                        <?= $stats['nb_hommes'] ?> 
                                        <span class="text-muted">(<?= $stats['pct_hommes'] ?>%)</span>
                                    </span>
                                    <?php 
                                    $equilibreGenre = abs(50 - $stats['pct_hommes']) < 15;
                                    ?>
                                    <span class="stat-indicator">
                                        <?php if ($equilibreGenre): ?>
                                            <i class="fas fa-check-circle text-success" title="Équilibré"></i>
                                        <?php else: ?>
                                            <i class="fas fa-exclamation-triangle text-warning" title="Déséquilibré"></i>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="stat-row">
                                    <span class="stat-label">Femmes:</span>
                                    <span class="stat-value">
                                        <?= $stats['nb_femmes'] ?> 
                                        <span class="text-muted">(<?= $stats['pct_femmes'] ?>%)</span>
                                    </span>
                                </div>
                                <div class="progress mt-2" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: <?= $stats['pct_hommes'] ?>%"></div>
                                    <div class="progress-bar bg-danger" style="width: <?= $stats['pct_femmes'] ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="stat-group mb-3">
                                <h6 class="text-muted mb-2"><i class="fas fa-graduation-cap"></i> Profils</h6>
                                <div class="stat-row">
                                    <span class="stat-label">Redoublants:</span>
                                    <span class="stat-value">
                                        <?= $stats['nb_redoublants'] ?> / <?= $stats['total'] ?>
                                        <span class="text-muted">(<?= $stats['pct_redoublants'] ?>%)</span>
                                    </span>
                                </div>
                                <?php if ($stats['nb_apprentis'] > 0): ?>
                                    <div class="stat-row">
                                        <span class="stat-label">Apprentis:</span>
                                        <span class="stat-value"><?= $stats['nb_apprentis'] ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($stats['nb_anglophones'] > 0): ?>
                                    <div class="stat-row">
                                        <span class="stat-label">Anglophones:</span>
                                        <span class="stat-value"><?= $stats['nb_anglophones'] ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="stat-group">
                                <h6 class="text-muted mb-2"><i class="fas fa-car"></i> Covoiturage</h6>
                                <?php if ($stats['covoit_total'] > 0): ?>
                                    <div class="stat-row">
                                        <span class="stat-label">Choix respectés:</span>
                                        <span class="stat-value">
                                            <?= $stats['covoit_respectes'] ?> / <?= $stats['covoit_total'] ?>
                                            <span class="text-muted">(<?= $stats['pct_covoit'] ?>%)</span>
                                        </span>
                                        <span class="stat-indicator">
                                            <?php if ($stats['pct_covoit'] >= 80): ?>
                                                <i class="fas fa-check-circle text-success" title="Excellent"></i>
                                            <?php elseif ($stats['pct_covoit'] >= 50): ?>
                                                <i class="fas fa-exclamation-circle text-warning" title="Moyen"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle text-danger" title="Faible"></i>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <div class="stat-row text-muted">
                                        <em>Aucun choix de covoiturage</em>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <?php if ($stats['score_conformite'] >= 80): ?>
                                <span class="text-success"><i class="fas fa-thumbs-up"></i> Groupe bien équilibré</span>
                            <?php elseif ($stats['score_conformite'] >= 60): ?>
                                <span class="text-warning"><i class="fas fa-balance-scale"></i> Groupe acceptable</span>
                            <?php else: ?>
                                <span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Groupe à ajuster</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
    <?php endif; ?>
</div>

<style>
.stat-box {
    text-align: center;
    padding: 15px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: #f8f9fa;
}
.stat-label {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 5px;
}
.stat-value {
    font-size: 1.8rem;
    font-weight: bold;
    color: #2c3e50;
}
.stat-group {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
}
.stat-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 5px 0;
    border-bottom: 1px solid #e9ecef;
}
.stat-row:last-child {
    border-bottom: none;
}
.stat-indicator {
    font-size: 1.2rem;
}
.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 0.75rem;
}
.progress {
    background-color: #e9ecef;
}
</style>

<?php require_once 'view/commun/footer.php'; ?>
    padding: 8px 12px;
}
</style>

<?php require_once 'view/commun/footer.php'; ?>
