<?php
// Gestion du chemin pour l'API et le site web
if (defined('ROOT_PATH')) {
    require_once ROOT_PATH . 'config/connexion.php';
} else {
    require_once 'config/connexion.php';
}

class MentionBac {
    
    private $id_mention;
    private $libelle_mention;

    public function get($attribut) {
        return $this->$attribut;
    }
    
    public static function getAll() {
        $sql = "SELECT * FROM MENTION_BAC ORDER BY libelle_mention";
        $stmt = Connexion::pdo()->query($sql);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'MentionBac');
        return $stmt->fetchAll();
    }
    
    public static function findByNom($nom) {
        $sql = "SELECT id_mention AS idMentionBac, libelle_mention FROM MENTION_BAC WHERE libelle_mention = :nom";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['nom' => $nom]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function findById($id) {
        $sql = "SELECT id_mention AS idMentionBac, libelle_mention FROM MENTION_BAC WHERE id_mention = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>