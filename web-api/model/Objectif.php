<?php
require_once 'config/connexion.php';

class Objectif {
    
    private $annee_scolaire;
    private $semestre;
    private $id_parcours;
    private $id_groupe;
    private $critere;
    private $valeur;
    private $objectif;
    private $updated_at;
    public function get($attribut) {
        return $this->$attribut;
    }
    public static function listByPromotion($idPromo) {
        $parts = explode('|', $idPromo);
        if (count($parts) !== 3) return [];
        list($annee, $sem, $parc) = $parts;

        $sql = "SELECT * FROM PROMOTION_OBJECTIF 
                WHERE annee_scolaire = :annee AND semestre = :sem AND id_parcours = :parc";
        
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['annee' => $annee, 'sem' => $sem, 'parc' => $parc]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Objectif');
    }
}
?>