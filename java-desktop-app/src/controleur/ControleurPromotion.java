package controleur;

import java.util.ArrayList;
import java.util.List;

import api.ApiClient;
import modele.Etudiant;
import modele.Groupe;
import modele.Promotion;

/**
 * Contré©Âƒé‚Â´leur pour la gestion des promotions
 */
public class ControleurPromotion {
    
    private ApiClient apiClient;
    
    public ControleurPromotion() {
        this.apiClient = ApiClient.getInstance();
    }
    
    /**
     * Ré©Âƒé‚Â©cupé©Âƒé‚Â¨re la liste de toutes les promotions
     */
    public List<Promotion> getPromotions() {
        try {
            return apiClient.getPromotions();
        } catch (Exception e) {
            System.err.println("Erreur lors de la ré©Âƒé‚Â©cupé©Âƒé‚Â©ration des promotions: " + e.getMessage());
            return new ArrayList<>();
        }
    }
    
    /**
     * Ré©Âƒé‚Â©cupé©Âƒé‚Â¨re les groupes d'une promotion
     */
    public List<Groupe> getGroupes(String idPromotion) {
        try {
            return apiClient.getGroupes(idPromotion);
        } catch (Exception e) {
            System.err.println("Erreur lors de la ré©Âƒé‚Â©cupé©Âƒé‚Â©ration des groupes: " + e.getMessage());
            return new ArrayList<>();
        }
    }
    
    /**
     * Ré©Âƒé‚Â©cupé©Âƒé‚Â¨re les é©Âƒé‚Â©tudiants d'une promotion
     */
    public List<Etudiant> getEtudiants(String idPromotion) {
        try {
            System.out.println("=== DEBUG GET ETUDIANTS ===");
            System.out.println("ID Promotion: " + idPromotion);
            
            List<Etudiant> etudiants = apiClient.getEtudiants(idPromotion);
            
            System.out.println("Nombre d'é©Âƒé‚Â©tudiants ré©Âƒé‚Â©cupé©Âƒé‚Â©ré©Âƒé‚Â©s: " + etudiants.size());
            if (!etudiants.isEmpty()) {
                System.out.println("Premier é©Âƒé‚Â©tudiant: " + etudiants.get(0).getNomComplet());
            }
            
            return etudiants;
        } catch (Exception e) {
            System.err.println("Erreur lors de la ré©Âƒé‚Â©cupé©Âƒé‚Â©ration des é©Âƒé‚Â©tudiants: " + e.getMessage());
            e.printStackTrace();
            return new ArrayList<>();
        }
    }
    
    /**
     * Obtient les statistiques d'une promotion
     */
    public StatistiquesPromotion getStatistiques(String idPromotion) {
        List<Etudiant> etudiants = getEtudiants(idPromotion);
        return new StatistiquesPromotion(etudiants);
    }
    
    /**
     * Classe interne pour les statistiques d'une promotion
     */
    public static class StatistiquesPromotion {
        public int total;
        public int nbFilles;
        public int nbGarcons;
        public int nbRedoublants;
        public int nbAnglophones;
        public int nbApprentis;
        public int nbAvecCovoiturage;
        public int nbSansGroupe;
        
        public StatistiquesPromotion(List<Etudiant> etudiants) {
            this.total = etudiants.size();
            this.nbFilles = 0;
            this.nbGarcons = 0;
            this.nbRedoublants = 0;
            this.nbAnglophones = 0;
            this.nbApprentis = 0;
            this.nbAvecCovoiturage = 0;
            this.nbSansGroupe = 0;
            
            for (Etudiant e : etudiants) {
                if (e.isFille()) nbFilles++;
                else nbGarcons++;
                
                if (e.isEstRedoublant()) nbRedoublants++;
                if (e.isEstAnglophone()) nbAnglophones++;
                if (e.isEstApprenti()) nbApprentis++;
                if (e.getIdCovoiturage() > 0) nbAvecCovoiturage++;
                if (e.getIdGroupe() == 0) nbSansGroupe++;
            }
        }
        
        @Override
        public String toString() {
            return String.format(
                "Total: %d | Filles: %d | Garé©Âƒé‚Â§ons: %d | Redoublants: %d | Anglophones: %d | Apprentis: %d",
                total, nbFilles, nbGarcons, nbRedoublants, nbAnglophones, nbApprentis
            );
        }
    }
}
