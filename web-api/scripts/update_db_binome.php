<?php
require_once __DIR__ . '/../config/connexion.php';

try {
    Connexion::connect();
    $pdo = Connexion::pdo();
    
    echo "Mise à jour de la base de données pour le système de binôme...\n";

    // 1. Supprimer la contrainte de clé étrangère sur id_covoiturage si elle existe
    // On essaie de deviner le nom ou on le fait brutalement si on connait le nom généré
    // Souvent c'est etudiant_ibfk_... mais ça dépend.
    // Le plus simple est de modifier la colonne id_covoiturage pour la supprimer.
    
    // On vérifie si la colonne id_covoiturage existe
    $stmt = $pdo->query("SHOW COLUMNS FROM ETUDIANT LIKE 'id_covoiturage'");
    if ($stmt->fetch()) {
        // On doit d'abord dropper la FK. 
        // Comme on ne connait pas le nom exact de la FK, on peut essayer de dropper la table COVOITURAGE
        // Mais si la FK est active, ça va bloquer.
        
        // Astuce: Récupérer le nom de la FK
        $sql = "SELECT CONSTRAINT_NAME 
                FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                WHERE TABLE_NAME = 'ETUDIANT' 
                AND COLUMN_NAME = 'id_covoiturage' 
                AND TABLE_SCHEMA = DATABASE()";
        $stmt = $pdo->query($sql);
        $fkName = $stmt->fetchColumn();
        
        if ($fkName) {
            echo "Suppression de la FK $fkName...\n";
            $pdo->exec("ALTER TABLE ETUDIANT DROP FOREIGN KEY $fkName");
        }

        echo "Suppression de la colonne id_covoiturage...\n";
        $pdo->exec("ALTER TABLE ETUDIANT DROP COLUMN id_covoiturage");
    }

    // 2. Supprimer la table COVOITURAGE
    echo "Suppression de la table COVOITURAGE...\n";
    $pdo->exec("DROP TABLE IF EXISTS COVOITURAGE");

    // 3. Ajouter la colonne id_binome_souhaite
    $stmt = $pdo->query("SHOW COLUMNS FROM ETUDIANT LIKE 'id_binome_souhaite'");
    if (!$stmt->fetch()) {
        echo "Ajout de la colonne id_binome_souhaite...\n";
        $pdo->exec("ALTER TABLE ETUDIANT ADD COLUMN id_binome_souhaite INT NULL");
        // On peut ajouter une FK vers ETUDIANT lui-même
        $pdo->exec("ALTER TABLE ETUDIANT ADD CONSTRAINT fk_binome_souhaite FOREIGN KEY (id_binome_souhaite) REFERENCES ETUDIANT(id_etudiant) ON DELETE SET NULL");
    }

    echo "Mise à jour terminée avec succès.\n";

} catch (PDOException $e) {
    echo "Erreur SQL : " . $e->getMessage() . "\n";
}
?>
