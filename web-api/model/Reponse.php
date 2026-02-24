<?php
require_once 'config/connexion.php';

class Reponse {
    
    private $id_reponse;
    private $id_sondage;
    private $libelle_reponse;

    public function get($attribut) {
        return $this->$attribut;
    }

    public static function create($idSondage, $libelle) {
        $sql = "INSERT INTO REPONSE (id_sondage, libelle_reponse) VALUES (:id, :lib)";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $idSondage, 'lib' => $libelle]);
        return Connexion::pdo()->lastInsertId();
    }
    

}
?>