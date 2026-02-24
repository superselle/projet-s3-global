package modele;

/**
 * Classe représentant un étudiant
 */
public class Etudiant {
    private int idEtudiant;
    private int idUtilisateur;
    private String nom;
    private String prenom;
    private String email;
    private char genre; // 'M' ou 'F'
    private int idGroupe;
    private String nomGroupe;
    private int idCovoiturage;
    private boolean estRedoublant;
    private boolean estAnglophone;
    private boolean estApprenti;
    private String typeBac;
    private String mentionBac;
    
    public Etudiant() {}
    
    // Getters et Setters
    public int getIdEtudiant() {
        return idEtudiant;
    }
    
    public void setIdEtudiant(int idEtudiant) {
        this.idEtudiant = idEtudiant;
    }
    
    public int getIdUtilisateur() {
        return idUtilisateur;
    }
    
    public void setIdUtilisateur(int idUtilisateur) {
        this.idUtilisateur = idUtilisateur;
    }
    
    public String getNom() {
        return nom;
    }
    
    public void setNom(String nom) {
        this.nom = nom;
    }
    
    public String getPrenom() {
        return prenom;
    }
    
    public void setPrenom(String prenom) {
        this.prenom = prenom;
    }
    
    public String getEmail() {
        return email;
    }
    
    public void setEmail(String email) {
        this.email = email;
    }
    
    public char getGenre() {
        return genre;
    }
    
    public void setGenre(char genre) {
        this.genre = genre;
    }
    
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
    
    public int getIdCovoiturage() {
        return idCovoiturage;
    }
    
    public void setIdCovoiturage(int idCovoiturage) {
        this.idCovoiturage = idCovoiturage;
    }
    
    public boolean isEstRedoublant() {
        return estRedoublant;
    }
    
    public void setEstRedoublant(boolean estRedoublant) {
        this.estRedoublant = estRedoublant;
    }
    
    public boolean isEstAnglophone() {
        return estAnglophone;
    }
    
    public void setEstAnglophone(boolean estAnglophone) {
        this.estAnglophone = estAnglophone;
    }
    
    public boolean isEstApprenti() {
        return estApprenti;
    }
    
    public void setEstApprenti(boolean estApprenti) {
        this.estApprenti = estApprenti;
    }
    
    public String getTypeBac() {
        return typeBac;
    }
    
    public void setTypeBac(String typeBac) {
        this.typeBac = typeBac;
    }
    
    public String getMentionBac() {
        return mentionBac;
    }
    
    public void setMentionBac(String mentionBac) {
        this.mentionBac = mentionBac;
    }
    
    public String getNomComplet() {
        return prenom + " " + nom;
    }
    
    public boolean isFille() {
        return genre == 'F';
    }
    
    // Méthodes alias pour compatibilité avec les algorithmes
    public boolean isRedoublant() {
        return estRedoublant;
    }
    
    public boolean aOptionAnglais() {
        return estAnglophone;
    }
    
    @Override
    public String toString() {
        return getNomComplet() + (nomGroupe != null ? " (" + nomGroupe + ")" : "");
    }
}
