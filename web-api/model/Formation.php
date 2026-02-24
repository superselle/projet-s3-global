<?php
require_once 'config/connexion.php';

class Formation {
    
    private $id_formation;
    private $nom_formation;
    private $type_formation;
    
    public function get($attribut) {
        return $this->$attribut;
    }
    public static function getById($id) {
        $sql = "SELECT * FROM FORMATION WHERE id_formation = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Formation');
        return $stmt->fetch();
    }
    public static function getAll() {
        $sql = "SELECT * FROM FORMATION ORDER BY nom_formation";
        $stmt = Connexion::pdo()->query($sql);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Formation');
        return $stmt->fetchAll();
    }
}
?>