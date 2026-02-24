<?php
require_once 'view/commun/components.php';
require_once 'view/commun/header.php';
$isEdit = isset($etudiant) && $etudiant;
$titrePage = $isEdit ? 'Modifier un étudiant' : 'Ajouter un étudiant';
$btnTexte  = $isEdit ? 'Enregistrer les modifications' : 'Créer';
$formAction = $isEdit ? "index.php?controller=responsableFiliere&action=mettreAJourEtudiant&id=" . $etudiant->get('id_etudiant') : "index.php?controller=responsableFiliere&action=enregistrerEtudiant";
$valNom = $isEdit ? $etudiant->get('nom') : '';
$valPrenom = $isEdit ? $etudiant->get('prenom') : '';
$valEmail = $isEdit ? $etudiant->get('email') : '';
$valLogin = $isEdit ? $etudiant->get('login_utilisateur') : '';
$pwdRequired = !$isEdit;
$pwdLabel = $isEdit ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe';
$pwdPlace = $isEdit ? 'Laisser vide si inchangé' : '';
$currentParcours = $isEdit ? $etudiant->get('id_parcours') : '';
$currentType = $isEdit ? $etudiant->get('id_type') : '';
$currentMention = $isEdit ? $etudiant->get('id_mention') : '';
$currentSemestre = $isEdit ? $etudiant->get('semestre') : 1;
$isRedoublant = $isEdit ? $etudiant->get('est_redoublant') : false;
$isAnglophone = $isEdit ? $etudiant->get('est_anglophone') : false;
$isApprenti = $isEdit ? $etudiant->get('est_apprenti') : false;
?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <div class="mb-xl"><?= linkBack('index.php?controller=responsableFiliere&action=gestionEtudiants') ?></div>
            <?php ob_start(); ?>
<h2><?= $titrePage ?></h2>
<?php displayFlashMessages(); ?>
<form method="post" action="<?= $formAction ?>">
    <div class="form-group">
        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($valPrenom) ?>" required>
    </div>
    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($valNom) ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($valEmail) ?>" required>
    </div>
    <div class="form-group">
        <label for="login">Login <?= !$isEdit ? '(optionnel)' : '' ?></label>
        <input type="text" id="login" name="login" value="<?= htmlspecialchars($valLogin) ?>">
    </div>
    <div class="form-group">
        <label for="password"><?= $pwdLabel ?><?= $pwdRequired ? ' *' : '' ?></label>
        <input type="password" id="password" name="password" <?= $pwdRequired ? 'required' : '' ?> placeholder="<?= $pwdPlace ?>">
    </div>
    <div class="form-group">
        <label for="id_parcours">Parcours</label>
        <?= renderSelect('id_parcours', $parcours, 'id_parcours', 'nom_parcours', $currentParcours, true) ?>
    </div>
    <div class="form-group">
        <label for="id_type">Type bac</label>
        <?= renderSelect('id_type', $typesBac, 'id_type', 'libelle_type', $currentType, true) ?>
    </div>
    <div class="form-group">
        <label for="id_mention">Mention bac</label>
        <?= renderSelect('id_mention', $mentions, 'id_mention', 'libelle_mention', $currentMention, true) ?>
    </div>
    <div class="form-group">
        <label for="semestre">Semestre</label>
        <select id="semestre" name="semestre" required>
            <?php for ($s = 1; $s <= 6; $s++): ?>
                <option value="<?= $s ?>" <?= $currentSemestre == $s ? 'selected' : '' ?>>Semestre <?= $s ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="form-group" style="margin-top:15px;">
        <label><input type="checkbox" name="est_redoublant" value="1" <?= $isRedoublant ? 'checked' : '' ?>> Redoublant</label>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="est_anglophone" value="1" <?= $isAnglophone ? 'checked' : '' ?>> Anglophone</label>
    </div>
    <div class="form-group">
        <label><input type="checkbox" name="est_apprenti" value="1" <?= $isApprenti ? 'checked' : '' ?>> Apprenti</label>
    </div>
    <div class="action-buttons" style="margin-top:20px;">
        <button type="submit" class="btn btn-primary"><?= $btnTexte ?></button>
    </div>
</form>
            <?php echo card(ob_get_clean()); ?>
        </main>
    </div>
</div>
<?php require_once 'view/commun/footer.php'; ?>
