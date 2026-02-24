package controleur;

import api.ApiClient;
import modele.Utilisateur;
import utils.SessionManager;

/**
 * Contrôleur pour l'authentification
 */
public class ControleurAuth {
    
    private ApiClient apiClient;
    
    public ControleurAuth() {
        this.apiClient = ApiClient.getInstance();
    }
    
    /**
     * Tente de connecter un utilisateur
     * @return true si la connexion réussit, false sinon
     */
    public boolean login(String identifiant, String motdepasse) {
        try {
            Utilisateur user = apiClient.login(identifiant, motdepasse);
            return true;
        } catch (Exception e) {
            System.err.println("Erreur de connexion: " + e.getMessage());
            return false;
        }
    }
    
    /**
     * Déconnecte l'utilisateur actuel
     */
    public void logout() {
        try {
            apiClient.logout();
        } catch (Exception e) {
            System.err.println("Erreur lors de la déconnexion: " + e.getMessage());
        }
    }
    
    /**
     * Vérifie si l'utilisateur est connecté
     */
    public boolean isConnected() {
        return SessionManager.getInstance().isConnected();
    }
    
    /**
     * Obtient l'utilisateur actuellement connecté
     */
    public Utilisateur getCurrentUser() {
        return SessionManager.getInstance().getCurrentUser();
    }
    
    /**
     * Obtient le rôle de l'utilisateur connecté
     */
    public String getUserRole() {
        return SessionManager.getInstance().getUserRole();
    }
}
