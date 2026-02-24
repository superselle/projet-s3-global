<?php
// Page web minimaliste : bouton pour générer puis insérer les 100 utilisateurs S4.
// IMPORTANT: à supprimer après utilisation (sécurité).

$baseDir = __DIR__;
$generator = $baseDir . '/generate-s2-student.php';
$seeder = $baseDir . '/seed_utilisateurs_s4.php';

function runScriptWithOutput($scriptPath) {
    if (!file_exists($scriptPath)) {
        return "Fichier manquant: $scriptPath\n";
    }

    ob_start();
    try {
        require $scriptPath;
    } catch (Exception $e) {
        echo "Erreur: " . $e->getMessage() . "\n";
    }
    return ob_get_clean();
}

$didRun = false;
$output = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $didRun = true;

    $output .= "=== Génération fichier PHP S4 ===\n";
    $output .= runScriptWithOutput($generator);

    $output .= "\n=== Insertion utilisateurs S4 en BDD ===\n";
    $output .= runScriptWithOutput($seeder);
}

?><!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Seed utilisateurs S4</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 900px; margin: 32px auto; padding: 0 16px; }
        .warn { background: #fff3cd; padding: 12px; border: 1px solid #ffeeba; }
        button { padding: 10px 14px; font-size: 16px; }
        pre { background: #111; color: #eee; padding: 12px; overflow: auto; }
    </style>
</head>
<body>
    <h1>Générer + insérer les utilisateurs S4</h1>

    <div class="warn">
        <strong>Important :</strong> ce fichier exécute un seed en base. Supprime <code>scripts/seed_utilisateurs_s4_web.php</code> après usage.
    </div>

    <p>
        Cette page va :
        1) lire <code>info-bd/s4-etc.txt</code> et regénérer <code>scripts/seed_utilisateurs_data_s4.generated.php</code>
        2) insérer/mettre à jour les utilisateurs <code>id_utilisateur</code> 111→210 dans la table <code>UTILISATEUR</code>.
    </p>

    <form method="post">
        <button type="submit">Lancer (générer + insérer S4)</button>
    </form>

    <?php
    if ($didRun) {
        ?>
        <h2>Résultat</h2>
        <pre><?php echo $output; ?></pre>
        <?php
    }
    ?>
</body>
</html>
