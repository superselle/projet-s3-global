<?php require_once 'view/commun/components.php'; require_once 'view/commun/header.php'; ?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?= isset($titre) ? htmlspecialchars($titre) : 'Gestion' ?></h2>
                <?php if (!empty($btnAjoutUrl)): ?>
                    <a href="<?= $btnAjoutUrl ?>" class="btn btn-success"><i class="fa fa-plus"></i> <?= isset($btnAjoutTexte) ? htmlspecialchars($btnAjoutTexte) : 'Ajouter' ?></a>
                <?php endif; ?>
            </div>
            <?php displayFlashMessages(); ?>
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <?php if (!empty($colonnes)): foreach ($colonnes as $col): ?>
                                        <th<?= isset($col['class']) ? ' class="' . $col['class'] . '"' : '' ?><?= isset($col['style']) ? ' style="' . $col['style'] . '"' : '' ?>><?= htmlspecialchars($col['label']) ?></th>
                                    <?php endforeach; endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($items)): ?>
                                    <tr><td colspan="<?= count($colonnes) ?>" class="text-center py-5 text-muted"><?= isset($msgVide) ? htmlspecialchars($msgVide) : 'Aucun élément trouvé.' ?></td></tr>
                                <?php else: foreach ($items as $item): ?>
                                    <tr>
                                        <?php foreach ($colonnes as $col): 
                                            if ($col['type'] === 'text'): ?>
                                                <td<?= isset($col['class']) ? ' class="' . $col['class'] . '"' : '' ?>><?= htmlspecialchars($item->get($col['key'])) ?></td>
                                            <?php elseif ($col['type'] === 'email'): ?>
                                                <td><a href="mailto:<?= htmlspecialchars($item->get($col['key'])) ?>"><?= htmlspecialchars($item->get($col['key'])) ?></a></td>
                                            <?php elseif ($col['type'] === 'badge'): 
                                                $val = $item->get($col['key']); ?>
                                                <td class="text-center"><?= $val ? '<span class="badge ' . (isset($col['badgeClass']) ? $col['badgeClass'] : 'badge-primary') . '">' . htmlspecialchars($val) . '</span>' : '<span class="text-muted">—</span>' ?></td>
                                            <?php elseif ($col['type'] === 'bool'): 
                                                $val = $item->get($col['key']);
                                                $yes = $col['yes'] ?? 'Oui';
                                                $no  = $col['no']  ?? 'Non';
                                                $badgeYes = $col['badgeYes'] ?? 'badge-success';
                                                $badgeNo  = $col['badgeNo']  ?? 'badge-secondary'; ?>
                                                <td class="text-center">
                                                    <?php if (!empty($val)): ?>
                                                        <span class="badge <?= $badgeYes ?>"><?= htmlspecialchars($yes) ?></span>
                                                    <?php else: ?>
                                                        <span class="badge <?= $badgeNo ?>"><?= htmlspecialchars($no) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php elseif ($col['type'] === 'actions'): ?>
                                                <td class="text-center">
                                                    <?php if (!empty($col['modifier'])): ?>
                                                        <a href="<?= $col['modifier']['url'] . $item->get($col['modifier']['param']) ?>" class="btn btn-sm btn-outline-primary" title="Modifier"><i class="fa fa-pencil"></i></a>
                                                    <?php endif; ?>
                                                    <?php if (!empty($col['supprimer'])): ?>
                                                        <form method="post" action="<?= $col['supprimer']['url'] ?>" class="d-inline-block" onsubmit="return confirm('<?= isset($col['supprimer']['confirm']) ? $col['supprimer']['confirm'] : 'Êtes-vous sûr ?' ?>');">
                                                            <input type="hidden" name="id" value="<?= $item->get($col['supprimer']['param']) ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer"><i class="fa fa-trash"></i></button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endif; 
                                        endforeach; ?>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php require_once 'view/commun/footer.php'; ?>
