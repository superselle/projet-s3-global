<?php
// Inclusion de la configuration des chemins
require_once 'config/paths.php';

$pageTitle = 'Connexion - Plateforme Pédagogique';
$isConnected = false;
$baseUrl = BASE_URL;
require_once 'view/commun/header.php';
?>

<div class="login-container">
    <div class="login-card">
        <h2>Connexion à la plateforme pédagogique</h2>
        
        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
            <div class="alert alert-error">
                Identifiant non reconnu. Veuillez utiliser un identifiant de test.
            </div>
        <?php endif; ?>
        
        <form action="<?php echo $baseUrl; ?>/index.php?controller=auth&action=traiterConnexion" method="POST" autocomplete="off">
            <div class="form-group">
                <label for="identifiant">Email (identifiant)</label>
                <input type="text" id="identifiant" name="identifiant" placeholder="prenom.nom@..." required>
            </div>
            <div class="form-group">
                <label for="motdepasse">Mot de passe</label>
                <input type="password" id="motdepasse" name="motdepasse" placeholder="(accepté pour ce jeu de données)" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Connexion</button>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="#" style="color: var(--rouge-erreur);">Mot de passe oublié ?</a>
            </div>
        </form>
        
        <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--gris-border);">
            <p style="font-size: 0.875rem; color: var(--gris-texte); text-align: center;">
                <strong>Mode test :</strong> utilisez les emails présents dans UTILISATEUR (ex. nicolas.ferey@univ.fr, jean.dupont@etu.univ.fr). Le mot de passe est accepté tel quel dans ce jeu de données.
            </p>
        </div>
    </div>
</div>

<?php require_once 'view/commun/footer.php'; ?>



