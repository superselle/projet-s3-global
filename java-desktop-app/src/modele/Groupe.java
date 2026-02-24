package modele;

import java.util.ArrayList;
import java.util.List;

/**
 * Classe représentant un groupe d'étudiants
 */
public class Groupe {
    private int idGroupe;
    private String nomGroupe;
    private int effectif;
    private int effectifMax;
    private int nbFilles;
    private int nbGarcons;
    private int nbRedoublants;
    private String anneeScolaire;
    private int semestre;
    private List<Etudiant> etudiants; // Liste des étudiants du groupe
    
    public Groupe() {
        this.etudiants = new ArrayList<>();
    }
    
    public Groupe(int id, String nom) {
        this.idGroupe = id;
        this.nomGroupe = nom;
        this.etudiants = new ArrayList<>();
    }
    
    // Getters et Setters
    public int getIdGroupe() {
        return idGroupe;
    }
    
    public void setIdGroupe(int idGroupe) {
        this.idGroupe = idGroupe;
    }
    
    public String getNomGroupe() {
        return nomGroupe;
    }
    
    public void setNomGroupe(String nomGroupe) {
        this.nomGroupe = nomGroupe;
    }
    
    public int getEffectif() {
        return effectif;
    }
    
    public void setEffectif(int effectif) {
        this.effectif = effectif;
    }
    
    public int getEffectifMax() {
        return effectifMax;
    }
    
    public void setEffectifMax(int effectifMax) {
        this.effectifMax = effectifMax;
    }
    
    public int getNbFilles() {
        return nbFilles;
    }
    
    public void setNbFilles(int nbFilles) {
        this.nbFilles = nbFilles;
    }
    
    public int getNbGarcons() {
        return nbGarcons;
    }
    
    public void setNbGarcons(int nbGarcons) {
        this.nbGarcons = nbGarcons;
    }
    
    public int getNbRedoublants() {
        return nbRedoublants;
    }
    
    public void setNbRedoublants(int nbRedoublants) {
        this.nbRedoublants = nbRedoublants;
    }
    
    public String getAnneeScolaire() {
        return anneeScolaire;
    }
    
    public void setAnneeScolaire(String anneeScolaire) {
        this.anneeScolaire = anneeScolaire;
    }
    
    public int getSemestre() {
        return semestre;
    }
    
    public void setSemestre(int semestre) {
        this.semestre = semestre;
    }
    
    // Méthodes pour gérer la liste d'étudiants
    public List<Etudiant> getEtudiants() {
        return etudiants;
    }
    
    public void setEtudiants(List<Etudiant> etudiants) {
        this.etudiants = etudiants;
        this.effectif = etudiants != null ? etudiants.size() : 0;
    }
    
    public void ajouterEtudiant(Etudiant e) {
        if (etudiants == null) {
            etudiants = new ArrayList<>();
        }
        etudiants.add(e);
        this.effectif = etudiants.size();
    }
    
    public void ajouterEtudiants(List<Etudiant> liste) {
        if (etudiants == null) {
            etudiants = new ArrayList<>();
        }
        if (liste != null) {
            etudiants.addAll(liste);
            this.effectif = etudiants.size();
        }
    }
    
    public void retirerEtudiant(Etudiant e) {
        if (etudiants != null) {
            etudiants.remove(e);
            this.effectif = etudiants.size();
        }
    }
    
    // Méthode alias pour compatibilité avec les algorithmes
    public void setId(int id) {
        this.idGroupe = id;
    }
    
    @Override
    public String toString() {
        return nomGroupe + " (" + effectif + " étudiants)";
    }
}
