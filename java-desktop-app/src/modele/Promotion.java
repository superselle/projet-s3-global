package modele;

/**
 * Classe représentant une promotion (année, semestre, parcours)
 */
public class Promotion {
    private String id; // Format: "ANNEE|SEMESTRE|PARCOURS"
    private String anneeScolaire;
    private int semestre;
    private String idParcours;
    private String nomParcours;
    private int nbEtudiants;
    private int nbGroupes;
    
    public Promotion() {}
    
    public Promotion(String id, String annee, int semestre, String parcours) {
        this.id = id;
        this.anneeScolaire = annee;
        this.semestre = semestre;
        this.nomParcours = parcours;
    }
    
    // Getters et Setters
    public String getId() {
        return id;
    }
    
    public void setId(String id) {
        this.id = id;
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
    
    public String getIdParcours() {
        return idParcours;
    }
    
    public void setIdParcours(String idParcours) {
        this.idParcours = idParcours;
    }
    
    public String getNomParcours() {
        return nomParcours;
    }
    
    public void setNomParcours(String nomParcours) {
        this.nomParcours = nomParcours;
    }
    
    public int getNbEtudiants() {
        return nbEtudiants;
    }
    
    public void setNbEtudiants(int nbEtudiants) {
        this.nbEtudiants = nbEtudiants;
    }
    
    public int getNbGroupes() {
        return nbGroupes;
    }
    
    public void setNbGroupes(int nbGroupes) {
        this.nbGroupes = nbGroupes;
    }
    
    public String getLibelle() {
        return nomParcours + " - S" + semestre + " (" + anneeScolaire + ")";
    }
    
    @Override
    public String toString() {
        return getLibelle() + " - " + nbEtudiants + " étudiants";
    }
}
