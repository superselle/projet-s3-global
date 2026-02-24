<?php
require_once 'config/connexion.php';
require_once 'Utilisateur.php';

class Enseignant extends Utilisateur {
    
    private $id_enseignant;
    private $id_role; 
    
    // Attributs récupérés via jointures (pour l'affichage)
    private $nom;
    private $prenom;
    private $email;
    private $libelle_role;

    public function get($attribut) {
        return $this->$attribut;
    }

    /**
     * Crée une entrée enseignant liée à un utilisateur existant.
     * À utiliser juste après Utilisateur::create()
     */
    public static function createEnseignant($idUtilisateur, $idRole) {
        $sql = "INSERT INTO ENSEIGNANT (id_utilisateur, id_role) VALUES (:id, :role)";
        $stmt = Connexion::pdo()->prepare($sql);
        return $stmt->execute(['id' => $idUtilisateur, 'role' => $idRole]);
    }

    /**
     * Récupère un enseignant par son ID Utilisateur (pour la modification).
     * Inclut les infos personnelles (Nom, Prénom, Email).
     */
    public static function getByIdUtilisateur($idUtilisateur) {
        $sql = "SELECT e.*, u.nom_utilisateur as nom, u.prenom_utilisateur as prenom, 
                       u.mail_utilisateur as email 
                FROM ENSEIGNANT e 
                JOIN UTILISATEUR u ON e.id_utilisateur = u.id_utilisateur 
                WHERE e.id_utilisateur = :id";
        
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $idUtilisateur]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Enseignant');
        return $stmt->fetch();
    }

    /**
     * Récupère la liste complète pour le tableau de gestion.
     * Inclut le libellé du rôle (ex: "Responsable Formation").
     */
    public static function getListeGestion() {
        $sql = "SELECT e.*, u.nom_utilisateur as nom, u.prenom_utilisateur as prenom, 
                       u.mail_utilisateur as email, r.libelle_role 
                FROM ENSEIGNANT e 
                JOIN UTILISATEUR u ON e.id_utilisateur = u.id_utilisateur 
                LEFT JOIN ROLE r ON e.id_role = r.id_role 
                ORDER BY u.nom_utilisateur ASC";
        
        $stmt = Connexion::pdo()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Enseignant');
    }

    /**
     * Met à jour le rôle d'un enseignant.
     */
    public static function updateRole($idUtilisateur, $idRole) {
        $sql = "UPDATE ENSEIGNANT SET id_role = :role WHERE id_utilisateur = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        return $stmt->execute(['role' => $idRole, 'id' => $idUtilisateur]);
    }

    /**
     * Supprime un enseignant.
     * IMPORTANT : Appeler cette méthode AVANT Utilisateur::delete().
     */
    public static function delete($idUtilisateur) {
        $sql = "DELETE FROM ENSEIGNANT WHERE id_utilisateur = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        return $stmt->execute(['id' => $idUtilisateur]);
    }
}
?>