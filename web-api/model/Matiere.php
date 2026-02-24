<?php
require_once 'config/connexion.php';

class Matiere {
    
    private $id_matiere;
    private $code_matiere;
    private $nom_matiere;
    private $semestre;
    private $type_matiere;

    public function get($attribut) {
        return $this->$attribut;
    }

    public static function getAll() {
        return Connexion::pdo()->query("SELECT * FROM MATIERE ORDER BY nom_matiere")
                               ->fetchAll(PDO::FETCH_CLASS, 'Matiere');
    }

    public static function getById($id) {
        $stmt = Connexion::pdo()->prepare("SELECT * FROM MATIERE WHERE id_matiere = :id");
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Matiere');
        return $stmt->fetch();
    }

    /**
     * Cherche une matière par son nom OU son code (insensible à la casse)
     * Ne crée JAMAIS de matière - retourne null si non trouvée
     */
    public static function findByNameOrCode($header) {
        $nom = trim($header);
        if ($nom === '') return null;

        $pdo = Connexion::pdo();
        
        // 1. Chercher par nom exact
        $stmt = $pdo->prepare("SELECT id_matiere FROM MATIERE WHERE LOWER(nom_matiere) = LOWER(:nom)");
        $stmt->execute(['nom' => $nom]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) return $row['id_matiere'];
        
        // 2. Chercher par code exact
        $stmt = $pdo->prepare("SELECT id_matiere FROM MATIERE WHERE LOWER(code_matiere) = LOWER(:code)");
        $stmt->execute(['code' => $nom]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) return $row['id_matiere'];
        
        // 3. Chercher si le header CONTIENT le code (ex: "SAE 2.04 BD" contient "SAE 2.04")
        $stmt = $pdo->prepare("SELECT id_matiere FROM MATIERE WHERE LOWER(:nom) LIKE CONCAT('%', LOWER(code_matiere), '%') ORDER BY LENGTH(code_matiere) DESC LIMIT 1");
        $stmt->execute(['nom' => $nom]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) return $row['id_matiere'];

        // Pas trouvé = on ignore cette colonne
        return null;
    }
}
?>