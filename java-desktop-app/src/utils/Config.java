package utils;

/**
 * Configuration globale de l'application
 */
public class Config {
    
    // URL de l'API REST PHP
    public static final String API_URL = "https://EXEMPLE.fr/projet/api/";
    
    // Authentification HTTP Basic (serveur universitaire)
    public static final String HTTP_AUTH_USER = "XXX";
    public static final String HTTP_AUTH_PASSWORD = "XXX?";
    
    // Timeout des requêtes HTTP (millisecondes)
    public static final int HTTP_TIMEOUT = 10000;
    
    // Constantes UI
    public static final int WINDOW_WIDTH = 1200;
    public static final int WINDOW_HEIGHT = 800;
    public static final String APP_TITLE = "SAE S301 - Constitution de Groupes";
    
    // Couleurs de la charte graphique (à adapter selon votre charte)
    public static final String COLOR_PRIMARY = "#2C3E50";
    public static final String COLOR_SECONDARY = "#3498DB";
    public static final String COLOR_SUCCESS = "#27AE60";
    public static final String COLOR_DANGER = "#E74C3C";
    public static final String COLOR_WARNING = "#F39C12";
    
    // Tailles par défaut pour la constitution des groupes
    public static final int DEFAULT_GROUP_MIN = 17;
    public static final int DEFAULT_GROUP_MAX = 20;
    public static final int DEFAULT_GROUP_TARGET = 18;
    
    private Config() {
        // Constructeur privé pour empêcher l'instanciation
    }
}
