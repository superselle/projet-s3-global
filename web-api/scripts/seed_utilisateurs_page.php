<?php
require_once __DIR__ . '/../config/connexion.php';
require_once __DIR__ . '/../model/Utilisateur.php';

Connexion::connect();

$nb = null;
$message = null;

if (isset($_POST['lancer']) && $_POST['lancer'] === '1') {
    $dataFile = __DIR__ . '/seed_utilisateurs_data.php';
    if (!file_exists($dataFile)) {
        $message = "Fichier manquant: scripts/seed_utilisateurs_data.php";
    } else {
        $users = require $dataFile;
        $nb = 0;
        foreach ($users as $u) {
            Utilisateur::create($u);
            $nb++;
        }
        $message = "OK: $nb utilisateurs insérés (mdp hashés par le modèle).";
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Seed UTILISATEUR</title>
</head>
<body>
    <h1>Insertion des UTILISATEUR</h1>

    <p>Ce script lit <code>scripts/seed_utilisateurs_data.php</code> et insère les utilisateurs via le modèle.</p>

    <form method="post">
        <button type="submit" name="lancer" value="1">Lancer l'insertion</button>
    </form>

    <?php if ($message !== null) { ?>
        <p><?php echo $message; ?></p>
    <?php } ?>

    <p>Conseil: supprime cette page après usage.</p>
</body>
</html>
