<?php
require_once __DIR__ . '/../config/connexion.php';

class Utilisateur {
    private $id_utilisateur;
    private $prenom_utilisateur;
    private $nom_utilisateur;
    private $mail_utilisateur;
    private $tel_utilisateur;
    private $adresse_utilisateur;
    private $genre_utilisateur;
    private $date_naissance;
    private $login_utilisateur;
    private $mdp_hash_utilisateur;
    private $statut_utilisateur;

    public function get($attribut) {
        return $this->$attribut;
    }

    public static function getById($id) {
        $sql = "SELECT * FROM UTILISATEUR WHERE id_utilisateur = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        $stmt->setFetchmode(PDO::FETCH_CLASS, 'Utilisateur');
        return $stmt->fetch();
    }

    public static function getByLogin($identifiant) {
        $sql = "SELECT * FROM UTILISATEUR WHERE login_utilisateur = :id OR mail_utilisateur = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $identifiant]);
        
        $stmt->setFetchmode(PDO::FETCH_CLASS, 'Utilisateur');
        return $stmt->fetch();
    }

    public static function getByEmail($email) {
        $sql = "SELECT * FROM UTILISATEUR WHERE mail_utilisateur = :email";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['email' => $email]);
        
        $stmt->setFetchmode(PDO::FETCH_CLASS, 'Utilisateur');
        return $stmt->fetch();
    }

    public static function create($prenom, $nom, $email, $password, $statut = 'ETUDIANT', $tel = null) {
        $pdo = Connexion::pdo();
        
        $login = strtolower($prenom . '.' . $nom);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO UTILISATEUR (prenom_utilisateur, nom_utilisateur, mail_utilisateur, tel_utilisateur, login_utilisateur, mdp_hash_utilisateur, statut_utilisateur) 
                VALUES (:prenom, :nom, :mail, :tel, :login, :mdp, :statut)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'prenom' => $prenom,
            'nom'    => $nom,
            'mail'   => $email,
            'tel'    => $tel,
            'login'  => $login,
            'mdp'    => $hash,
            'statut' => $statut
        ]);
        
        return $pdo->lastInsertId();
    }

    public static function updatePassword($id, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE UTILISATEUR SET mdp_hash_utilisateur = :hash WHERE id_utilisateur = :id";
        
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['hash' => $hash, 'id' => $id]);
        
        return $stmt->rowCount();
    }

    public static function delete($id) {
        $sql = "DELETE FROM UTILISATEUR WHERE id_utilisateur = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $id]);
        
        return $stmt->rowCount();
    }
    
    /**
     * Cherche un utilisateur par email
     */
    public static function findByEmail($email) {
        $sql = "SELECT * FROM UTILISATEUR WHERE mail_utilisateur = :email";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cherche un utilisateur par login
     */
    public static function findByLogin($login) {
        $sql = "SELECT * FROM UTILISATEUR WHERE login_utilisateur = :login";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['login' => $login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function update($idUtilisateur, $data) {
        // On prépare la requête dynamiquement ou en dur selon vos champs
        $sql = "UPDATE UTILISATEUR SET 
                nom_utilisateur = :nom, 
                prenom_utilisateur = :prenom, 
                mail_utilisateur = :email, 
                tel_utilisateur = :tel
                WHERE id_utilisateur = :id";
        
        $stmt = Connexion::pdo()->prepare($sql);
        return $stmt->execute([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'tel' => $data['tel'],
            'id' => $idUtilisateur
        ]);
    }
}

?>