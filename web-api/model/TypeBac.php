<?php
// Gestion du chemin pour l'API et le site web
if (defined('ROOT_PATH')) {
    require_once ROOT_PATH . 'config/connexion.php';
} else {
    require_once 'config/connexion.php';
}

class TypeBac {
    
    private $id_type_bac;
    private $libelle_type;

    public function get($attribut) {
        return $this->$attribut;
    }
    
    public static function getAll() {
        $sql = "SELECT * FROM TYPE_BAC ORDER BY libelle_type";
        $stmt = Connexion::pdo()->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'TypeBac');
        return $stmt->fetchAll();
    }
    
    public static function findByNom($nom) {
        $sql = "SELECT id_type AS idTypeBac, libelle_type FROM TYPE_BAC WHERE libelle_type = :nom";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['nom' => $nom]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function findById($id) {
        $sql = "SELECT id_type AS idTypeBac, libelle_type FROM TYPE_BAC WHERE id_type = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>