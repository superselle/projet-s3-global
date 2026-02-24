<?php
require_once 'config/connexion.php';

class Commentaire {
    
    private $id_commentaire;
    private $id_promotion;
    private $id_groupe;
    private $id_enseignant;
    private $commentaire;
    private $date_creation;
    
    // Champs jointure
    private $nom_enseignant;
    private $prenom_enseignant;
    private $nom_groupe;
    
    public function get($attribut) {
        return property_exists($this, $attribut) ? $this->$attribut : null;
    }
    
    /**
     * Créer un nouveau commentaire
     */
    public static function create($idPromotion, $idGroupe, $idEnseignant, $commentaire) {
        $sql = "INSERT INTO COMMENTAIRE_GROUPE (id_promotion, id_groupe, id_enseignant, commentaire, date_creation) 
                VALUES (:promo, :groupe, :ens, :comm, NOW())";
        
        $stmt = Connexion::pdo()->prepare($sql);
        return $stmt->execute([
            'promo' => $idPromotion,
            'groupe' => $idGroupe,
            'ens' => $idEnseignant,
            'comm' => $commentaire
        ]);
    }
    
    /**
     * Récupérer tous les commentaires d'une promotion
     */
    public static function getByPromotion($idPromotion) {
        $sql = "SELECT C.*, 
                       U.nom_utilisateur AS nom_enseignant,
                       U.prenom_utilisateur AS prenom_enseignant,
                       G.nom_groupe
                FROM COMMENTAIRE_GROUPE C
                JOIN ENSEIGNANT E ON C.id_enseignant = E.id_enseignant
                JOIN UTILISATEUR U ON E.id_utilisateur = U.id_utilisateur
                LEFT JOIN GROUPE G ON C.id_groupe = G.id_groupe
                WHERE C.id_promotion = :promo
                ORDER BY C.date_creation DESC";
        
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['promo' => $idPromotion]);
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Commentaire');
    }
    
    /**
     * Récupérer les commentaires d'un groupe spécifique
     */
    public static function getByGroupe($idGroupe) {
        $sql = "SELECT C.*, 
                       U.nom_utilisateur AS nom_enseignant,
                       U.prenom_utilisateur AS prenom_enseignant
                FROM COMMENTAIRE_GROUPE C
                JOIN ENSEIGNANT E ON C.id_enseignant = E.id_enseignant
                JOIN UTILISATEUR U ON E.id_utilisateur = U.id_utilisateur
                WHERE C.id_groupe = :groupe
                ORDER BY C.date_creation DESC";
        
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['groupe' => $idGroupe]);
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Commentaire');
    }
    
    /**
     * Supprimer un commentaire
     */
    public static function delete($idCommentaire) {
        $sql = "DELETE FROM COMMENTAIRE_GROUPE WHERE id_commentaire = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        return $stmt->execute(['id' => $idCommentaire]);
    }
    
    /**
     * Compter les commentaires d'une promotion
     */
    public static function countByPromotion($idPromotion) {
        $sql = "SELECT COUNT(*) FROM COMMENTAIRE_GROUPE WHERE id_promotion = :promo";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['promo' => $idPromotion]);
        return $stmt->fetchColumn();
    }
}
?>
