<?php
require_once 'config/connexion.php';

class Promotion {
    
    private $annee_scolaire;
    private $semestre;
    private $id_parcours;
    private $nom_parcours;
    
    public function get($attribut) { 
        if ($attribut === 'id') {
            return $this->annee_scolaire . '|' . $this->semestre . '|' . $this->id_parcours;
        }
        return $this->$attribut; 
    }
    public function getLabel() {
        return $this->id_parcours . ' - Semestre ' . $this->semestre . ' (' . $this->annee_scolaire . ')';
    }

    public static function getAll() {
        $sql = "SELECT p.annee_scolaire, p.semestre, p.id_parcours, parcours.nom_parcours
                FROM PROMOTION p
                LEFT JOIN PARCOURS parcours ON parcours.id_parcours = p.id_parcours
                ORDER BY p.annee_scolaire DESC, p.id_parcours, p.semestre";
            $sql = "SELECT DISTINCT g.annee_scolaire, g.semestre, g.id_parcours, p.nom_parcours
                FROM GROUPE g
                LEFT JOIN PARCOURS p ON p.id_parcours = g.id_parcours
                GROUP BY g.annee_scolaire, g.semestre, g.id_parcours
                ORDER BY g.annee_scolaire DESC, g.id_parcours, g.semestre";
            $stmt = Connexion::pdo()->query($sql);
            return $stmt->fetchAll(PDO::FETCH_CLASS, 'Promotion');
    }
    public static function getById($idComposite) {
        $parts = explode('|', $idComposite);
        if (count($parts) !== 3) return null;
        
        $sql = "SELECT :annee as annee_scolaire, :sem as semestre, :parc as id_parcours, p.nom_parcours
                FROM PARCOURS p WHERE p.id_parcours = :parc";
        
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['annee' => $parts[0], 'sem' => $parts[1], 'parc' => $parts[2]]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Promotion');
        return $stmt->fetch();
    }
    public static function areGroupesPublies($idComposite) {
        $parts = explode('|', $idComposite);
        if (count($parts) !== 3) return false;

        $sql = "SELECT COUNT(*) FROM GROUPE 
                WHERE annee_scolaire = :a AND semestre = :s AND id_parcours = :p AND est_publie = 1";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['a' => $parts[0], 's' => $parts[1], 'p' => $parts[2]]);
        return $stmt->fetchColumn() > 0;
    }
    public static function setPublication($idComposite, $etat) {
        $parts = explode('|', $idComposite);
        if (count($parts) !== 3) return false;

        $sql = "UPDATE GROUPE SET est_publie = :etat 
                WHERE annee_scolaire = :a AND semestre = :s AND id_parcours = :p";
        
        $stmt = Connexion::pdo()->prepare($sql);
        return $stmt->execute(['etat' => $etat, 'a' => $parts[0], 's' => $parts[1], 'p' => $parts[2]]);
    }
}
?>