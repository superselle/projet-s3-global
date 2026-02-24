<?php
require_once 'config/connexion.php';
require_once 'Utilisateur.php';

class Etudiant extends Utilisateur {
    
    // Champs spécifiques à ETUDIANT
    private $id_etudiant;
    private $id_groupe;
    private $id_binome_souhaite;
    private $id_parcours;
    private $id_type;
    private $id_mention;
    private $semestre;
    private $est_redoublant;
    private $est_anglophone;
    private $est_apprenti;

    // Champs "Bonus" (Jointures)
    private $nom_groupe;
    private $libelle_type;
    private $libelle_mention;
    private $nom_parcours;

    // Champs hérités ou renommés depuis Utilisateur
    private $nom;       // Alias pour nom_utilisateur
    private $prenom;    // Alias pour prenom_utilisateur
    private $email;     // Alias pour mail_utilisateur
    private $login_utilisateur;
    private $genre_utilisateur;
    private $tel_utilisateur;
    private $date_naissance;
    private $mdp_hash_utilisateur;


    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    private function hydrate($data) {
        foreach ($data as $key => $value) {
            // Gestion des alias
            if ($key === 'nom_utilisateur') $this->nom = $value;
            elseif ($key === 'prenom_utilisateur') $this->prenom = $value;
            elseif ($key === 'mail_utilisateur') $this->email = $value;
            
            // Remplissage standard
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    // Getter générique
    public function get($attribut) {
        return property_exists($this, $attribut) ? $this->$attribut : null;
    }

    // Setter générique
    public function set($attribut, $valeur) {
        if (property_exists($this, $attribut)) {
            $this->$attribut = $valeur;
        }
    }

    // --- REQUÊTES SQL ---

    // Requête de base pour récupérer un étudiant avec toutes ses infos (Utilisateur + Groupe + Parcours)
    private static function getBaseQuery() {
        return "SELECT E.*, 
                       U.nom_utilisateur AS nom, U.nom_utilisateur,
                       U.prenom_utilisateur AS prenom, U.prenom_utilisateur,
                       U.mail_utilisateur AS email, U.mail_utilisateur,
                       U.login_utilisateur, U.mdp_hash_utilisateur, 
                       U.tel_utilisateur, U.genre_utilisateur, U.date_naissance,
                       P.nom_parcours, G.nom_groupe, 
                       TB.libelle_type, MB.libelle_mention
                FROM ETUDIANT E
                JOIN UTILISATEUR U ON E.id_utilisateur = U.id_utilisateur
                LEFT JOIN PARCOURS P ON E.id_parcours = P.id_parcours
                LEFT JOIN GROUPE G ON E.id_groupe = G.id_groupe
                LEFT JOIN TYPE_BAC TB ON E.id_type = TB.id_type
                LEFT JOIN MENTION_BAC MB ON E.id_mention = MB.id_mention";
    }

    public static function getAll() {
        $sql = self::getBaseQuery() . " ORDER BY U.nom_utilisateur";
        return Connexion::pdo()->query($sql)->fetchAll(PDO::FETCH_CLASS, "Etudiant");
    }

    public static function getById($idEtudiant) {
        $sql = self::getBaseQuery() . " WHERE E.id_etudiant = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $idEtudiant]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Etudiant");
        return $stmt->fetch();
    }

    public static function getByIdUtilisateur($idUtilisateur) {
        $sql = self::getBaseQuery() . " WHERE E.id_utilisateur = :id";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $idUtilisateur]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, "Etudiant");
        return $stmt->fetch();
    }

    public static function getAllByPromo($annee, $parcours, $semestre = null) {
        if ($semestre !== null) {
            $sql = self::getBaseQuery() . " WHERE E.id_parcours = :parc AND E.semestre = :sem ORDER BY U.nom_utilisateur";
            $stmt = Connexion::pdo()->prepare($sql);
            $stmt->execute(['parc' => $parcours, 'sem' => $semestre]);
        } else {
            $sql = self::getBaseQuery() . " WHERE E.id_parcours = :parc AND (annee_scolaire = :annee OR E.id_groupe IS NULL) ORDER BY U.nom_utilisateur";
            $stmt = Connexion::pdo()->prepare($sql);
            $stmt->execute(['parc' => $parcours, 'annee' => $annee]);
        }
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Etudiant");
    }

    /**
     * Récupère la liste pédagogique des étudiants d'une promotion (format: annee|semestre|parcours)
     * @param string $idPromotion Format: annee|semestre|parcours
     * @return array Liste des étudiants de la promotion
     */
    public static function getListePedagogique($idPromotion) {
        $parts = explode('|', $idPromotion);
        if (count($parts) !== 3) return [];
        list($annee, $sem, $parc) = $parts;
        
        $sql = self::getBaseQuery() . " WHERE E.id_parcours = :parc AND E.semestre = :sem ORDER BY U.nom_utilisateur";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['parc' => $parc, 'sem' => $sem]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Etudiant");
    }

    /**
     * Récupère l'export complet (données personnelles) d'une promotion
     * Retourne un tableau avec 'rows' (objets Etudiant) et 'columns' (noms des colonnes)
     * @param string $idPromotion Format: annee|semestre|parcours
     * @return array ['rows' => array, 'columns' => array]
     */
    public static function getExportCompletPromotion($idPromotion) {
        $rows = self::getListePedagogique($idPromotion);
        $columns = [
            "numero",
            "login",
            "nom",
            "prenom",
            "genre",
            "email",
            "telephone",
            "date_naissance",
            "type_bac",
            "mention_bac",
            "est_redoublant",
            "est_anglophone",
            "est_apprenti",
            "groupe",
            "parcours"
        ];
        
        return [
            "rows" => $rows,
            "columns" => $columns
        ];
    }

    /**
     * Récupère la liste des étudiants d'un groupe spécifique avec leurs informations utilisateur
     * Utilisé pour l'affichage du détail d'un groupe dans une promotion 
     */ 
    public static function getListePedagogiqueByGroupe($idGroupe) {
        $sql = self::getBaseQuery() . " WHERE E.id_groupe = :id ORDER BY U.nom_utilisateur";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $idGroupe]);
        
        // On retourne des objets Etudiant pour rester cohérent avec le reste du projet
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Etudiant");
    }

    // --- MISES À JOUR ---

    public static function updateGroupe($idEtu, $idGrp) {
        $stmt = Connexion::pdo()->prepare("UPDATE ETUDIANT SET id_groupe = ? WHERE id_etudiant = ?");
        $stmt->execute([$idGrp, $idEtu]);
    }

    /**
     * Sauvegarde les choix de binôme (jusqu'à 3)
     * @param int $idEtu ID de l'étudiant qui fait les choix
     * @param array $idsBinomes Tableau d'IDs des étudiants choisis (max 3)
     */
    public static function updateBinomes($idEtu, $idsBinomes) {
        $pdo = Connexion::pdo();
        
        // Supprimer les anciens choix
        $stmt = $pdo->prepare("DELETE FROM CHOIX_BINOME WHERE id_etudiant = ?");
        $stmt->execute([$idEtu]);
        
        // Insérer les nouveaux choix (max 3)
        $ordre = 1;
        foreach ($idsBinomes as $idBinome) {
            if ($ordre > 3) break; // Limiter à 3 choix
            if ($idBinome != $idEtu && !empty($idBinome)) { // Ne pas se choisir soi-même
                $stmt = $pdo->prepare("INSERT INTO CHOIX_BINOME (id_etudiant, id_etudiant_choisi, ordre_preference) VALUES (?, ?, ?)");
                $stmt->execute([$idEtu, $idBinome, $ordre]);
                $ordre++;
            }
        }
    }

    /**
     * Récupère l'état des choix de binôme de l'étudiant
     * @param int $idEtu ID de l'étudiant
     * @return array Contient 'mesChoix' (array d'étudiants), 'reciproques' (array d'IDs), 'quiMaChoisi' (array d'étudiants)
     */
    public static function getEtatBinome($idEtu) {
        $pdo = Connexion::pdo();
        
        // Mes choix (jusqu'à 3)
        $sql = "SELECT E.*, U.nom_utilisateur, U.prenom_utilisateur, CB.ordre_preference
                FROM CHOIX_BINOME CB
                JOIN ETUDIANT E ON CB.id_etudiant_choisi = E.id_etudiant
                JOIN UTILISATEUR U ON E.id_utilisateur = U.id_utilisateur
                WHERE CB.id_etudiant = ?
                ORDER BY CB.ordre_preference";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$idEtu]);
        $mesChoix = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $etudiant = new Etudiant($row);
            $etudiant->set('ordre_preference', $row['ordre_preference']);
            $mesChoix[] = $etudiant;
        }
        
        // Vérifier les choix réciproques
        $reciproques = [];
        foreach ($mesChoix as $choix) {
            $sql2 = "SELECT 1 FROM CHOIX_BINOME WHERE id_etudiant = ? AND id_etudiant_choisi = ?";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->execute([$choix->get('id_etudiant'), $idEtu]);
            if ($stmt2->fetch()) {
                $reciproques[] = $choix->get('id_etudiant');
            }
        }
        
        // Qui m'a choisi (pour information)
        $sql3 = "SELECT E.*, U.nom_utilisateur, U.prenom_utilisateur
                 FROM CHOIX_BINOME CB
                 JOIN ETUDIANT E ON CB.id_etudiant = E.id_etudiant
                 JOIN UTILISATEUR U ON E.id_utilisateur = U.id_utilisateur
                 WHERE CB.id_etudiant_choisi = ?
                 ORDER BY CB.ordre_preference";
        $stmt3 = $pdo->prepare($sql3);
        $stmt3->execute([$idEtu]);
        $quiMaChoisi = [];
        while ($row = $stmt3->fetch(PDO::FETCH_ASSOC)) {
            $quiMaChoisi[] = new Etudiant($row);
        }
        
        return [
            'mesChoix' => $mesChoix,
            'reciproques' => $reciproques,
            'quiMaChoisi' => $quiMaChoisi
        ];
    }

    public static function getCamarades($idParcours) {
        $sql = self::getBaseQuery() . " WHERE E.id_parcours = :parc ORDER BY U.nom_utilisateur";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['parc' => $idParcours]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Etudiant");
    }

    // --- NOTES & STATS ---

    public static function getNotes($idEtu) {
        $stmt = Connexion::pdo()->prepare("SELECT valeur_note, nom_matiere FROM NOTE n JOIN MATIERE m ON n.id_matiere = m.id_matiere WHERE id_etudiant = ?");
        $stmt->execute([$idEtu]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getNotesAvecStats($idEtu) {
        $sql = "SELECT nom_matiere, valeur_note, commentaire_note FROM NOTE n JOIN MATIERE m ON n.id_matiere = m.id_matiere 
                WHERE id_etudiant = :id ORDER BY nom_matiere";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $idEtu]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllByPromoAndSemestre($annee, $id_parcours, $semestre) {
        $sql = "SELECT e.*, u.nom_utilisateur AS nom, u.prenom_utilisateur AS prenom, u.mail_utilisateur AS email
            FROM ETUDIANT e
            JOIN UTILISATEUR u ON e.id_utilisateur = u.id_utilisateur
            LEFT JOIN GROUPE g ON e.id_groupe = g.id_groupe
            WHERE e.id_parcours = :parcours 
            AND (g.semestre = :semestre OR e.id_groupe IS NULL)";
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['parcours' => $id_parcours, 'semestre' => $semestre]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Etudiant');
    }
}
?>