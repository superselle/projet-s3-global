<?php
require_once 'config/connexion.php';

class Note {
    
    private $id_note;
    private $valeur_note;
    private $commentaire_note;
    private $id_etudiant;
    private $id_matiere;

    public function get($attribut) {
        return $this->$attribut;
    }

    /**
     * Insère ou met à jour une note
     */
    public static function upsert($idEtudiant, $idMatiere, $valeur, $commentaire = null) {
        $pdo = Connexion::pdo();

        // 1. Vérifie si une note existe déjà pour ce couple étudiant/matière
        $stmt = $pdo->prepare("SELECT id_note FROM NOTE WHERE id_etudiant = :ide AND id_matiere = :idm");
        $stmt->execute(['ide' => $idEtudiant, 'idm' => $idMatiere]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // 2. Mise à jour
            $sql = "UPDATE NOTE SET valeur_note = :val, commentaire_note = :comm WHERE id_note = :id";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                'val' => $valeur, 
                'comm' => $commentaire, 
                'id' => $existing['id_note']
            ]);
        } else {
            // 3. Insertion
            $sql = "INSERT INTO NOTE (valeur_note, commentaire_note, id_etudiant, id_matiere) 
                    VALUES (:val, :comm, :ide, :idm)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([
                'val' => $valeur, 
                'comm' => $commentaire, 
                'ide' => $idEtudiant, 
                'idm' => $idMatiere
            ]);
        }
    }
}
?>