<?php
require_once 'config/connexion.php';

class Sondage {
    
    private $id_sondage;
    private $nom_sondage;
    private $contenu_sondage;
    private $annee_scolaire;
    private $semestre;
    private $id_parcours;
    private $mode_sondage; 

    public function get($attribut) {
        return $this->$attribut;
    }

    
    public static function getAll() {
        $sql = "SELECT * FROM SONDAGE ORDER BY id_sondage DESC";
        $stmt = Connexion::pdo()->query($sql);
        
        $stmt->setFetchmode(PDO::FETCH_CLASS, "Sondage");
        return $stmt->fetchAll();
    }

    public static function getById($id) {
        $sql = "SELECT * FROM SONDAGE WHERE id_sondage = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $stmt->setFetchmode(PDO::FETCH_CLASS, "Sondage");
        return $stmt->fetch();
    }

    public static function getChoix($idSondage) {
        $sql = "SELECT * FROM REPONSE WHERE id_sondage = :id ORDER BY id_reponse";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $idSondage]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getReponsesEtudiant($idSondage, $idEtudiant) {
        $sql = "SELECT id_reponse FROM ETUDIANT_REPONSE WHERE id_etudiant = :etu AND id_sondage = :sondage";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['etu' => $idEtudiant, 'sondage' => $idSondage]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function sauverReponses($idSondage, $idEtudiant, $idsReponses) {
        $pdo = Connexion::pdo();
        
        $sqlDel = "DELETE FROM ETUDIANT_REPONSE WHERE id_etudiant = :etu AND id_sondage = :sondage";
        $stmtDel = $pdo->prepare($sqlDel);
        $stmtDel->execute(['etu' => $idEtudiant, 'sondage' => $idSondage]);

        $sqlIns = "INSERT INTO ETUDIANT_REPONSE (id_etudiant, id_sondage, rang, id_reponse) 
                   VALUES (:etu, :sondage, :rang, :rep)";
        $stmtIns = $pdo->prepare($sqlIns);
        
        $rang = 1;
        foreach ($idsReponses as $idRep) {
            $stmtIns->execute([
                'etu' => $idEtudiant, 
                'sondage' => $idSondage, 
                'rang' => $rang, 
                'rep' => $idRep
            ]);
            $rang++; 
        }
        
        return true;
    }
    public static function create($nom, $contenu, $annee, $semestre, $idParcours, $mode) {
        $pdo = Connexion::pdo();
        $sql = "INSERT INTO SONDAGE (nom_sondage, contenu_sondage, annee_scolaire, semestre, id_parcours, mode_sondage) 
                VALUES (:nom, :contenu, :annee, :sem, :parc, :mode)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom' => $nom,
            'contenu' => $contenu,
            'annee' => $annee,
            'sem' => $semestre,
            'parc' => $idParcours,
            'mode' => $mode
        ]);
        
        return $pdo->lastInsertId();
    }
}
?>