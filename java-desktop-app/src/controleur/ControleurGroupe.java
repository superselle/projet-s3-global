package controleur;

import java.util.ArrayList;
import java.util.List;

import api.ApiClient;
import api.ApiClient.Affectation;
import modele.Etudiant;
import modele.Groupe;

/**
 * Contrôleur pour la constitution des groupes
 */
public class ControleurGroupe {
    
    private ApiClient apiClient;
    
    public ControleurGroupe() {
        this.apiClient = ApiClient.getInstance();
    }
    
    /**
     * Enregistre les affectations d'étudiants aux groupes
     */
    public boolean enregistrerAffectations(List<Etudiant> etudiants) {
        try {
            List<Affectation> affectations = new ArrayList<>();
            
            for (Etudiant e : etudiants) {
                if (e.getIdGroupe() > 0) {
                    affectations.add(new Affectation(e.getIdEtudiant(), e.getIdGroupe()));
                }
            }
            
            return apiClient.saveAffectations(affectations);
        } catch (Exception e) {
            System.err.println("Erreur lors de l'enregistrement des affectations: " + e.getMessage());
            return false;
        }
    }
    
    /**
     * Enregistre les affectations à partir d'une liste de maps
     */
    public boolean saveAffectations(List<java.util.Map<String, Object>> affectations) {
        try {
            System.out.println("=== SAUVEGARDE AFFECTATIONS ===");
            System.out.println("Nombre d'affectations: " + affectations.size());
            
            List<Affectation> affectationsList = new ArrayList<>();
            
            for (java.util.Map<String, Object> aff : affectations) {
                int idEtudiant = (Integer) aff.get("idEtudiant");
                int idGroupe = (Integer) aff.get("idGroupe");
                System.out.println("Affectation: Etudiant " + idEtudiant + " -> Groupe " + idGroupe);
                affectationsList.add(new Affectation(idEtudiant, idGroupe));
            }
            
            boolean result = apiClient.saveAffectations(affectationsList);
            System.out.println("Résultat sauvegarde: " + result);
            return result;
        } catch (Exception e) {
            System.err.println("=== ERREUR SAUVEGARDE ===");
            System.err.println("Message: " + e.getMessage());
            System.err.println("Type: " + e.getClass().getName());
            e.printStackTrace();
            return false;
        }
    }
    
    /**
     * Récupère les groupes d'une promotion
     */
    public List<Groupe> getGroupes(String idPromotion) throws Exception {
        return apiClient.getGroupes(idPromotion);
    }
    
    /**
     * Initialise des groupes vides
     */
    public List<Groupe> initialiserGroupes(int nombre, String prefixe) {
        List<Groupe> groupes = new ArrayList<>();
        for (int i = 1; i <= nombre; i++) {
            Groupe g = new Groupe();
            g.setIdGroupe(i);
            g.setNomGroupe(prefixe + i);
            g.setEffectif(0);
            g.setNbFilles(0);
            g.setNbGarcons(0);
            groupes.add(g);
        }
        return groupes;
    }
    
    /**
     * Affecte un étudiant à un groupe
     */
    public void affecterEtudiant(Etudiant etudiant, Groupe groupe) {
        etudiant.setIdGroupe(groupe.getIdGroupe());
        etudiant.setNomGroupe(groupe.getNomGroupe());
        
        // Mettre à jour les statistiques du groupe
        groupe.setEffectif(groupe.getEffectif() + 1);
        if (etudiant.isFille()) {
            groupe.setNbFilles(groupe.getNbFilles() + 1);
        } else {
            groupe.setNbGarcons(groupe.getNbGarcons() + 1);
        }
        if (etudiant.isEstRedoublant()) {
            groupe.setNbRedoublants(groupe.getNbRedoublants() + 1);
        }
    }
    
    /**
     * Retire un étudiant d'un groupe
     */
    public void retirerEtudiant(Etudiant etudiant, Groupe groupe) {
        if (etudiant.getIdGroupe() == groupe.getIdGroupe()) {
            etudiant.setIdGroupe(0);
            etudiant.setNomGroupe(null);
            
            // Mettre à jour les statistiques du groupe
            groupe.setEffectif(Math.max(0, groupe.getEffectif() - 1));
            if (etudiant.isFille()) {
                groupe.setNbFilles(Math.max(0, groupe.getNbFilles() - 1));
            } else {
                groupe.setNbGarcons(Math.max(0, groupe.getNbGarcons() - 1));
            }
            if (etudiant.isEstRedoublant()) {
                groupe.setNbRedoublants(Math.max(0, groupe.getNbRedoublants() - 1));
            }
        }
    }
    
    /**
     * Calcule les statistiques globales des groupes
     */
    public String getStatistiquesGroupes(List<Groupe> groupes) {
        int totalEtudiants = 0;
        int totalFilles = 0;
        int totalGarcons = 0;
        int totalRedoublants = 0;
        
        for (Groupe g : groupes) {
            totalEtudiants += g.getEffectif();
            totalFilles += g.getNbFilles();
            totalGarcons += g.getNbGarcons();
            totalRedoublants += g.getNbRedoublants();
        }
        
        return String.format(
            "%d groupes | %d étudiants | %d filles (%.1f%%) | %d garçons (%.1f%%) | %d redoublants",
            groupes.size(), totalEtudiants,
            totalFilles, totalEtudiants > 0 ? (totalFilles * 100.0 / totalEtudiants) : 0,
            totalGarcons, totalEtudiants > 0 ? (totalGarcons * 100.0 / totalEtudiants) : 0,
            totalRedoublants
        );
    }
}
