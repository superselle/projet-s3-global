<?php
require_once 'config/connexion.php';

class Parcours {
    
    private $id_parcours;
    private $nom_parcours;

    public function get($attribut) {
        return $this->$attribut;
    }
    public static function getAll() {
        $sql = "SELECT * FROM PARCOURS ORDER BY nom_parcours";
        $stmt = Connexion::pdo()->query($sql);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Parcours');
        return $stmt->fetchAll();
    }
}
?>