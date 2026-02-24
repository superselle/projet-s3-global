<?php
require_once 'view/commun/components.php';
require_once 'view/commun/header.php';
$isEdit = isset($utilisateur) && $utilisateur;
$titrePage = $isEdit ? 'Modifier un enseignant' : 'Ajouter un enseignant';
$btnTexte  = $isEdit ? 'Enregistrer les modifications' : 'Enregistrer l\'enseignant';
$formAction = $isEdit ? "index.php?controller=responsableFormation&action=mettreAJourEnseignant&id=" . $utilisateur->get('id_utilisateur') : "index.php?controller=responsableFormation&action=enregistrerEnseignant";

// Utiliser l'objet enseignant si disponible (a les alias mappés), sinon utilisateur
$sourceData = $isEdit && isset($enseignant) && $enseignant ? $enseignant : ($isEdit ? $utilisateur : null);

$valNom = $isEdit ? ($sourceData->get('nom') ?? $utilisateur->get('nom_utilisateur') ?? '') : '';
$valPrenom = $isEdit ? ($sourceData->get('prenom') ?? $utilisateur->get('prenom_utilisateur') ?? '') : '';
$valEmail = $isEdit ? ($sourceData->get('email') ?? $utilisateur->get('mail_utilisateur') ?? '') : '';
$valTel = $isEdit ? ($utilisateur->get('tel_utilisateur') ?? '') : '';
$pwdRequired = !$isEdit;
$pwdLabel = $isEdit ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe initial';
$pwdPlace = $isEdit ? 'Laisser vide si inchangé' : '';
$currentRole = $isEdit && isset($roleKey) ? $roleKey : '';
?>
<div class="content-wrapper">
    <div class="layout-with-sidebar">
        <?php if (isset($showSidebar) && $showSidebar): ?>
            <?php require_once 'view/commun/navbar.php'; ?>
        <?php endif; ?>
        <main class="main-content">
            <div class="mb-xl"><?= linkBack('index.php?controller=responsableFormation&action=gestionEnseignants') ?></div>
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
        <label for="telephone">Téléphone</label>
        <input type="tel" id="telephone" name="telephone" value="<?= htmlspecialchars($valTel) ?>" placeholder="06 XX XX XX XX">
    </div>
    <div class="form-group">
        <label for="role">Rôle dans l'application</label>
        <select id="role" name="role" required>
            <option value="" disabled <?= empty($currentRole) ? 'selected' : '' ?>>— Sélectionner un rôle —</option>
            <option value="enseignant" <?= ($currentRole === 'enseignant') ? 'selected' : '' ?>>Enseignant</option>
            <option value="responsable_filiere" <?= ($currentRole === 'responsable_filiere') ? 'selected' : '' ?>>Responsable Filière</option>
            <option value="responsable_formation" <?= ($currentRole === 'responsable_formation') ? 'selected' : '' ?>>Responsable Formation</option>
        </select>
    </div>
    <div class="form-group">
        <label for="motdepasse"><?= $pwdLabel ?><?= $pwdRequired ? ' *' : '' ?></label>
        <input type="password" id="motdepasse" name="motdepasse" <?= $pwdRequired ? 'required' : '' ?> placeholder="<?= $pwdPlace ?>">
    </div>
    <div class="form-group">
        <label for="motdepasse_confirm">Confirmer le mot de passe<?= $pwdRequired ? ' *' : '' ?></label>
        <input type="password" id="motdepasse_confirm" name="motdepasse_confirm" <?= $pwdRequired ? 'required' : '' ?>>
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