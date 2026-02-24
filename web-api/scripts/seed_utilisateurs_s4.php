<?php
// Insère les UTILISATEUR S4 (IDs 111..210) depuis un fichier PHP généré.
// Usage (depuis C:\xampp\htdocs\WEB\sae-s301-groupes):
//   C:\xampp\php\php.exe scripts/seed_utilisateurs_s4.php

require_once __DIR__ . '/../config/connexion.php';

Connexion::connect();
$pdo = Connexion::pdo();

if (!($pdo instanceof PDO)) {
    echo "Connexion PDO indisponible. Vérifie config/connexion.php\n";
    exit;
}

$dataFile = __DIR__ . '/seed_utilisateurs_data_s4.generated.php';
if (!file_exists($dataFile)) {
    echo "Fichier manquant: scripts/seed_utilisateurs_data_s4.generated.php\n";
    echo "Astuce: lance d'abord C:\\xampp\\php\\php.exe scripts/generate-s2-student.php\n";
    exit;
}

$users = require $dataFile;
if (!is_array($users)) {
    echo "seed_utilisateurs_data_s4.generated.php doit retourner un array.\n";
    exit;
}

$insertSql = 'INSERT INTO UTILISATEUR ('
    . 'id_utilisateur, prenom_utilisateur, nom_utilisateur, mail_utilisateur, '
    . 'tel_utilisateur, adresse_utilisateur, genre_utilisateur, date_naissance, '
    . 'login_utilisateur, mdp_hash_utilisateur, statut_utilisateur'
    . ') VALUES ('
    . ':id, :prenom, :nom, :mail, :tel, :adresse, :genre, :date_naissance, :login, :mdp_hash, :statut'
    . ') ON DUPLICATE KEY UPDATE '
    . 'prenom_utilisateur = VALUES(prenom_utilisateur), '
    . 'nom_utilisateur = VALUES(nom_utilisateur), '
    . 'mail_utilisateur = VALUES(mail_utilisateur), '
    . 'tel_utilisateur = VALUES(tel_utilisateur), '
    . 'adresse_utilisateur = VALUES(adresse_utilisateur), '
    . 'genre_utilisateur = VALUES(genre_utilisateur), '
    . 'date_naissance = VALUES(date_naissance), '
    . 'login_utilisateur = VALUES(login_utilisateur), '
    . 'mdp_hash_utilisateur = VALUES(mdp_hash_utilisateur), '
    . 'statut_utilisateur = VALUES(statut_utilisateur)';

$stmt = $pdo->prepare($insertSql);

$total = 0;
$maxId = 0;

$pdo->beginTransaction();
try {
    foreach ($users as $u) {
        if (!is_array($u)) {
            continue;
        }

        if (!isset($u['id_utilisateur']) || !isset($u['prenom']) || !isset($u['nom']) || !isset($u['mail']) || !isset($u['login']) || !isset($u['motdepasse'])) {
            continue;
        }

        $id = $u['id_utilisateur'];
        if ($id <= 0) {
            continue;
        }

        $mdpHash = password_hash($u['motdepasse'], PASSWORD_DEFAULT);

        $stmt->execute([
            'id' => $id,
            'prenom' => $u['prenom'],
            'nom' => $u['nom'],
            'mail' => $u['mail'],
            'tel' => array_key_exists('tel', $u) ? $u['tel'] : null,
            'adresse' => array_key_exists('adresse', $u) ? $u['adresse'] : null,
            'genre' => array_key_exists('genre', $u) ? $u['genre'] : null,
            'date_naissance' => array_key_exists('date_naissance', $u) ? $u['date_naissance'] : null,
            'login' => $u['login'],
            'mdp_hash' => $mdpHash,
            'statut' => array_key_exists('statut', $u) ? $u['statut'] : null,
        ]);

        $total++;
        if ($id > $maxId) {
            $maxId = $id;
        }
    }

    if ($maxId > 0) {
        $next = $maxId + 1;
        $pdo->exec('ALTER TABLE UTILISATEUR AUTO_INCREMENT = ' . $next);
    }

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Erreur: " . $e->getMessage() . "\n";
    exit;
}

echo "OK: $total utilisateurs S4 insérés/mis à jour (mdp hashés).\n";
