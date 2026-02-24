<?php
/**
 * Script de migration pour passer du système 1 binôme au système 3 binômes
 * À exécuter UNE SEULE FOIS via le navigateur
 */

require_once __DIR__ . '/../config/connexion.php';

// Initialiser la connexion
Connexion::connect();

try {
    $pdo = Connexion::pdo();
    
    echo "<h2>Migration du système de binôme</h2>";
    echo "<pre>";
    
    // 1. Créer la table CHOIX_BINOME
    echo "Étape 1: Création de la table CHOIX_BINOME...\n";
    $sql = "CREATE TABLE IF NOT EXISTS CHOIX_BINOME (
        id_choix_binome INT AUTO_INCREMENT PRIMARY KEY,
        id_etudiant INT NOT NULL,
        id_etudiant_choisi INT NOT NULL,
        ordre_preference INT NOT NULL DEFAULT 1,
        date_choix DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_etudiant) REFERENCES ETUDIANT(id_etudiant) ON DELETE CASCADE,
        FOREIGN KEY (id_etudiant_choisi) REFERENCES ETUDIANT(id_etudiant) ON DELETE CASCADE,
        UNIQUE KEY unique_choix (id_etudiant, id_etudiant_choisi),
        CONSTRAINT check_ordre CHECK (ordre_preference BETWEEN 1 AND 3),
        CONSTRAINT check_pas_soi_meme CHECK (id_etudiant != id_etudiant_choisi)
    )";
    $pdo->exec($sql);
    echo "✓ Table CHOIX_BINOME créée avec succès\n\n";
    
    // 2. Vérifier si la colonne id_binome_souhaite existe
    echo "Étape 2: Vérification de la colonne id_binome_souhaite...\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM ETUDIANT LIKE 'id_binome_souhaite'");
    $colonneExiste = $stmt->fetch();
    
    if ($colonneExiste) {
        echo "✓ Colonne id_binome_souhaite trouvée\n\n";
        
        // 3. Migrer les données existantes (uniquement si aucune donnée n'existe)
        echo "Étape 3: Migration des données existantes...\n";
        $nbExistants = $pdo->query("SELECT COUNT(*) FROM CHOIX_BINOME")->fetchColumn();
        
        if ($nbExistants > 0) {
            echo "⚠ $nbExistants choix déjà présents dans CHOIX_BINOME, migration ignorée\n\n";
        } else {
            $sql = "INSERT INTO CHOIX_BINOME (id_etudiant, id_etudiant_choisi, ordre_preference)
                    SELECT id_etudiant, id_binome_souhaite, 1
                    FROM ETUDIANT
                    WHERE id_binome_souhaite IS NOT NULL";
            $pdo->exec($sql);
            $nbMigrees = $pdo->query("SELECT COUNT(*) FROM CHOIX_BINOME")->fetchColumn();
            echo "✓ $nbMigrees choix de binôme migrés\n\n";
        }
    } else {
        echo "⚠ Colonne id_binome_souhaite n'existe pas (peut-être déjà migrée)\n\n";
    }
    
    // 4. Créer les index
    echo "Étape 4: Création des index...\n";
    try {
        $pdo->exec("CREATE INDEX idx_etudiant ON CHOIX_BINOME(id_etudiant)");
        echo "✓ Index idx_etudiant créé\n";
    } catch (PDOException $e) {
        echo "⚠ Index idx_etudiant existe déjà\n";
    }
    
    try {
        $pdo->exec("CREATE INDEX idx_etudiant_choisi ON CHOIX_BINOME(id_etudiant_choisi)");
        echo "✓ Index idx_etudiant_choisi créé\n";
    } catch (PDOException $e) {
        echo "⚠ Index idx_etudiant_choisi existe déjà\n";
    }
    
    echo "\n";
    echo "===========================================\n";
    echo "✓✓✓ MIGRATION RÉUSSIE ✓✓✓\n";
    echo "===========================================\n";
    echo "Les étudiants peuvent maintenant choisir jusqu'à 3 binômes.\n";
    echo "</pre>";
    
    echo "<p><strong>Note:</strong> Si vous souhaitez supprimer l'ancienne colonne id_binome_souhaite, ";
    echo "décommentez les lignes correspondantes dans le fichier sql_update_binome_3choix.sql</p>";
    
} catch (PDOException $e) {
    echo "</pre>";
    echo "<div style='color: red; font-weight: bold;'>";
    echo "ERREUR lors de la migration: " . htmlspecialchars($e->getMessage());
    echo "</div>";
    echo "<p>Les commandes DDL (CREATE TABLE, CREATE INDEX) effectuent des commits implicites en MySQL.</p>";
}
?>
