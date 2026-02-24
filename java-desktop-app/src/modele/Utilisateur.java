package modele;

/**
 * Classe représentant un utilisateur du système
 */
public class Utilisateur {
    private int idUtilisateur;
    private String nom;
    private String prenom;
    private String email;
    private String login;
    private String telephone;
    private String adresse;
    private String genre;
    private String dateNaissance;
    
    public Utilisateur() {}
    
    public Utilisateur(int id, String nom, String prenom, String email, String login) {
        this.idUtilisateur = id;
        this.nom = nom;
        this.prenom = prenom;
        this.email = email;
        this.login = login;
    }
    
    // Getters et Setters
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
    
    public String getLogin() {
        return login;
    }
    
    public void setLogin(String login) {
        this.login = login;
    }
    
    public String getNomComplet() {
        return prenom + " " + nom;
    }
    
    public String getTelephone() {
        return telephone;
    }
    
    public void setTelephone(String telephone) {
        this.telephone = telephone;
    }
    
    public String getAdresse() {
        return adresse;
    }
    
    public void setAdresse(String adresse) {
        this.adresse = adresse;
    }
    
    public String getGenre() {
        return genre;
    }
    
    public void setGenre(String genre) {
        this.genre = genre;
    }
    
    public String getDateNaissance() {
        return dateNaissance;
    }
    
    public void setDateNaissance(String dateNaissance) {
        this.dateNaissance = dateNaissance;
    }
    
    @Override
    public String toString() {
        return getNomComplet();
    }
}
