<?php
require_once 'config/connexion.php';

class RepondreSondage {

    public static function getStatsBySondage($idSondage) {
        $sql = "SELECT r.id_reponse, libelle_reponse as contenu_reponse, COUNT(id_etudiant) as nb
                FROM REPONSE r
                LEFT JOIN ETUDIANT_REPONSE er ON r.id_reponse = er.id_reponse
                WHERE r.id_sondage = :id
                GROUP BY r.id_reponse, libelle_reponse
                ORDER BY nb DESC";
        
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $idSondage]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getReponsesEtudiantsBySondage($idSondage) {

        $sql = "SELECT e.id_etudiant as numero, 
                       nom_utilisateur as nom, 
                       prenom_utilisateur as prenom, 
                       login_utilisateur as login,
                       rang,
                       libelle_reponse as contenu_reponse
                FROM ETUDIANT_REPONSE er
                JOIN ETUDIANT e ON er.id_etudiant = e.id_etudiant
                JOIN UTILISATEUR u ON e.id_utilisateur = u.id_utilisateur
                JOIN REPONSE r ON er.id_reponse = r.id_reponse
                WHERE er.id_sondage = :id
                ORDER BY nom_utilisateur, rang";

        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $idSondage]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>