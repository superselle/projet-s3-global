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
            <div class="card shadow-sm border-0">
                <h2 class="text-primary mb-4">Créer un nouveau sondage</h2>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php 
                        if ($error === 'missing') echo "Le titre et les réponses sont obligatoires.";
                        elseif ($error === 'reponses') echo "Veuillez saisir au moins 2 réponses (une par ligne).";
                        elseif ($error === 'db') echo "Erreur lors de l'enregistrement en base de données.";
                        ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="index.php?controller=sondage&action=enregistrer">
                    <div class="form-group mb-3">
                        <label for="promotion_id" class="font-weight-bold">Promotion ciblée</label>
                        <select id="promotion_id" name="promotion_id" class="form-control" required>
                            <option value="">— Choisir une promotion —</option>
                            <?php if (!empty($promotions)): foreach ($promotions as $p): ?>
                                <option value="<?= htmlspecialchars($p->get('id')) ?>"><?= htmlspecialchars($p->getLabel()) ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="mode_sondage" class="font-weight-bold">Type de réponse</label>
                        <select id="mode_sondage" name="mode_sondage" class="form-control" required>
                            <option value="unique" selected>Choix unique (Radio)</option>
                            <option value="multiple">Choix multiple (Checkbox)</option> 
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="nom_sondage" class="font-weight-bold">Titre du sondage</label>
                        <input id="nom_sondage" name="nom_sondage" type="text" class="form-control" 
                               required placeholder="Ex: Date du partiel de PHP" />
                    </div>

                    <div class="form-group mb-3">
                        <label for="contenu_sondage" class="font-weight-bold">Description (optionnel)</label>
                        <textarea id="contenu_sondage" name="contenu_sondage" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label for="reponses" class="font-weight-bold">Réponses possibles <small class="text-muted">(Une par ligne)</small></label>
                        <textarea id="reponses" name="reponses" class="form-control" rows="5" required 
                                  placeholder="Lundi 14h&#10;Mardi 10h&#10;Mercredi 8h"></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a class="btn btn-secondary" href="index.php?controller=sondage&action=index">Retour</a>
                        <button class="btn btn-primary" type="submit">Créer le sondage</button>
                    </div>
                </form>
            </div>
<?php layoutEnd(); ?>