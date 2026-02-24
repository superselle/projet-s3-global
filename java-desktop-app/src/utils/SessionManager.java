package utils;

import modele.Utilisateur;

/**
 * Gestionnaire de session utilisateur
 * Singleton pour maintenir l'état de connexion dans toute l'application
 */
public class SessionManager {
    
    private static SessionManager instance;
    
    private boolean isConnected = false;
    private Utilisateur currentUser;
    private String userRole;
    private String sessionCookie;
    
    private SessionManager() {
        // Constructeur privé pour le pattern Singleton
    }
    
    /**
     * Obtient l'instance unique du SessionManager
     */
    public static SessionManager getInstance() {
        if (instance == null) {
            instance = new SessionManager();
        }
        return instance;
    }
    
    /**
     * Démarre une session après connexion réussie
     */
    public void login(Utilisateur user, String role, String cookie) {
        this.currentUser = user;
        this.userRole = role;
        this.sessionCookie = cookie;
        this.isConnected = true;
    }
    
    /**
     * Termine la session
     */
    public void logout() {
        this.currentUser = null;
        this.userRole = null;
        this.sessionCookie = null;
        this.isConnected = false;
    }
    
    /**
     * Vérifie si un utilisateur est connecté
     */
    public boolean isConnected() {
        return isConnected;
    }
    
    /**
     * Obtient l'utilisateur actuellement connecté
     */
    public Utilisateur getCurrentUser() {
        return currentUser;
    }
    
    /**
     * Obtient le rôle de l'utilisateur
     */
    public String getUserRole() {
        return userRole;
    }
    
    /**
     * Obtient le cookie de session pour les requêtes API
     */
    public String getSessionCookie() {
        return sessionCookie;
    }
    
    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     */
    public boolean hasRole(String role) {
        return userRole != null && userRole.equals(role);
    }
    
    /**
     * Vérifie si l'utilisateur est responsable
     */
    public boolean isResponsable() {
        return hasRole("responsable_filiere") || hasRole("responsable_formation");
    }
}
