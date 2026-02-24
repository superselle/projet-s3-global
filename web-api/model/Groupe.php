<?php
require_once 'config/connexion.php';

class Groupe {
    
    private $id_groupe;
    private $nom_groupe;
    private $effectif; 
    private $id_parcours;
    private $semestre;
    private $annee_scolaire;
    private $effectif_max;
    private $lettre; 

    public function get($attribut) {
        return $this->$attribut;
    }

    public static function getById($id) {
        $sql = "SELECT g.*, (SELECT COUNT(*) FROM ETUDIANT e WHERE e.id_groupe = g.id_groupe) AS effectif 
                FROM GROUPE g WHERE id_groupe = :id";
        
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Groupe');
        return $stmt->fetch();
    }
    
    public static function getByPromotion($idPromo) {
        $parts = explode('|', $idPromo);
        if (count($parts) !== 3) return [];
        list($annee, $sem, $parc) = $parts;

        $sql = "SELECT g.*, (SELECT COUNT(*) FROM ETUDIANT e WHERE e.id_groupe = g.id_groupe) AS effectif
                FROM GROUPE g
                WHERE annee_scolaire = :annee 
                AND semestre = :sem 
                AND id_parcours = :parc
                ORDER BY nom_groupe";
        
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute(['annee' => $annee, 'sem' => $sem, 'parc' => $parc]);
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Groupe');
    }
    
    public static function getAll() {
        $sql = "SELECT g.*, (SELECT COUNT(*) FROM ETUDIANT e WHERE e.id_groupe = g.id_groupe) AS effectif 
                FROM GROUPE g ORDER BY nom_groupe";
        return Connexion::pdo()->query($sql)->fetchAll(PDO::FETCH_CLASS, 'Groupe');
    }
    

    
    public static function updateEffectif($idGroupe, $effectif) {
        return 0; // Placeholder conservé pour compatibilité
    }
    
    /**
     * Calcule les statistiques d'un groupe pour le contrôle qualité
     * @param int $idGroupe
     * @return array Statistiques détaillées du groupe
     */
    public static function getStatistiques($idGroupe) {
        $pdo = Connexion::pdo();
        
        $stats = [
            'id_groupe' => $idGroupe,
            'total' => 0,
            'nb_hommes' => 0,
            'nb_femmes' => 0,
            'nb_redoublants' => 0,
            'nb_apprentis' => 0,
            'nb_anglophones' => 0,
            'types_bac' => [],
            'covoit_respectes' => 0,
            'covoit_total' => 0,
            'moyenne' => null,
            'ecart_type' => null
        ];
        
        // Récupérer tous les étudiants du groupe
        $sql = "SELECT E.*, U.genre_utilisateur 
                FROM ETUDIANT E 
                JOIN UTILISATEUR U ON E.id_utilisateur = U.id_utilisateur
                WHERE E.id_groupe = :id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $idGroupe]);
        $etudiants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stats['total'] = count($etudiants);
        
        if ($stats['total'] === 0) {
            return $stats;
        }
        
        // Statistiques de base
        foreach ($etudiants as $etu) {
            // Genre
            $genre = $etu['genre_utilisateur'];
            if ($genre === 'Homme' || $genre === 'M') {
                $stats['nb_hommes']++;
            } else {
                $stats['nb_femmes']++;
            }
            
            // Redoublants
            if ($etu['est_redoublant']) {
                $stats['nb_redoublants']++;
            }
            
            // Apprentis
            if ($etu['est_apprenti']) {
                $stats['nb_apprentis']++;
            }
            
            // Anglophones
            if ($etu['est_anglophone']) {
                $stats['nb_anglophones']++;
            }
            
            // Types de bac
            $idType = $etu['id_type'];
            if (!isset($stats['types_bac'][$idType])) {
                $stats['types_bac'][$idType] = 0;
            }
            $stats['types_bac'][$idType]++;
        }
        
        // Statistiques covoiturage
        $idsEtudiants = array_column($etudiants, 'id_etudiant');
        
        foreach ($idsEtudiants as $idEtu) {
            // Récupérer les choix de covoiturage de cet étudiant
            $sqlChoix = "SELECT id_etudiant_choisi FROM CHOIX_BINOME WHERE id_etudiant = :id";
            $stmtChoix = $pdo->prepare($sqlChoix);
            $stmtChoix->execute(['id' => $idEtu]);
            $choix = $stmtChoix->fetchAll(PDO::FETCH_COLUMN);
            
            foreach ($choix as $idChoisi) {
                $stats['covoit_total']++;
                
                // Vérifier si le choisi est dans le même groupe
                if (in_array($idChoisi, $idsEtudiants)) {
                    $stats['covoit_respectes']++;
                }
            }
        }
        
        // Calcul pourcentages et indicateurs
        $stats['pct_hommes'] = $stats['total'] > 0 ? round(($stats['nb_hommes'] / $stats['total']) * 100, 1) : 0;
        $stats['pct_femmes'] = $stats['total'] > 0 ? round(($stats['nb_femmes'] / $stats['total']) * 100, 1) : 0;
        $stats['pct_redoublants'] = $stats['total'] > 0 ? round(($stats['nb_redoublants'] / $stats['total']) * 100, 1) : 0;
        $stats['pct_covoit'] = $stats['covoit_total'] > 0 ? round(($stats['covoit_respectes'] / $stats['covoit_total']) * 100, 1) : 100;
        
        // Score de conformité (basé sur équilibre genre et respect covoiturage)
        $scoreGenre = 100 - abs(50 - $stats['pct_hommes']);
        $scoreCovoit = $stats['pct_covoit'];
        $stats['score_conformite'] = round(($scoreGenre + $scoreCovoit) / 2, 1);
        
        return $stats;
    }
    
    /**
     * Calcule les statistiques pour tous les groupes d'une promotion
     * @param string $idPromotion Format: annee|semestre|parcours
     * @return array Tableau de statistiques par groupe
     */
    public static function getStatistiquesPromotion($idPromotion) {
        $groupes = self::getByPromotion($idPromotion);
        $statsPromo = [];
        
        foreach ($groupes as $groupe) {
            $stats = self::getStatistiques($groupe->get('id_groupe'));
            $stats['nom_groupe'] = $groupe->get('nom_groupe');
            $statsPromo[] = $stats;
        }
        
        return $statsPromo;
    }
    
    public static function delete($id) {
        $stmt = Connexion::pdo()->prepare("DELETE FROM GROUPE WHERE id_groupe = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}
?>