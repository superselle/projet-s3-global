<?php
require_once 'config/connexion.php';

class Contrainte {

    private $id_contrainte;
    private $annee_scolaire;
    private $semestre;
    private $id_parcours;
    private $type_contrainte;
    private $id_etudiant1;
    private $id_etudiant2;
    private $created_at;

    public function get($attribut) {
        return $this->$attribut;
    }

    public static function listByPromotion($idPromo) {
        $parts = explode('|', $idPromo);
        if (count($parts) !== 3) return [];
        list($annee, $sem, $parc) = $parts;

        $sql = "SELECT * FROM PROMOTION_CONTRAINTE 
                WHERE annee_scolaire = :annee AND semestre = :sem AND id_parcours = :parc 
                ORDER BY type_contrainte, id_contrainte DESC";
        
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['annee' => $annee, 'sem' => $sem, 'parc' => $parc]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Contrainte');
    }

}
?>