package api;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.nio.charset.StandardCharsets;
import java.util.ArrayList;
import java.util.Base64;
import java.util.List;

import com.google.gson.Gson;
import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;

import modele.Etudiant;
import modele.Groupe;
import modele.Promotion;
import modele.Utilisateur;
import utils.Config;
import utils.SessionManager;

/**
 * Client API REST pour communiquer avec le serveur PHP
 * Gère toutes les requêtes HTTP vers l'API
 * Singleton pour partager la session entre toutes les requtes
 */
public class ApiClient {
    
    private static ApiClient instance;
    private static final Gson gson = new Gson();
    private String sessionCookie = null;
    
    private ApiClient() {
        // Constructeur privé pour le singleton
    }
    
    public static ApiClient getInstance() {
        if (instance == null) {
            instance = new ApiClient();
        }
        return instance;
    }
    
    /**
     * Classe interne pour la réponse API standardisée
     */
    private static class ApiResponse {
        boolean success;
        JsonElement data;
        String message;
    }
    
    /**
     * Effectue une requête HTTP GET
     */
    private JsonElement doGet(String endpoint) throws Exception {
        URL url = new URL(Config.API_URL + endpoint);
        HttpURLConnection conn = (HttpURLConnection) url.openConnection();
        conn.setRequestMethod("GET");
        conn.setRequestProperty("Content-Type", "application/json");
        
        // Authentification HTTP Basic
        addHttpBasicAuth(conn);
        
        // Ajouter le cookie de session si disponible
        if (sessionCookie != null) {
            conn.setRequestProperty("Cookie", sessionCookie);
        }
        
        conn.setConnectTimeout(Config.HTTP_TIMEOUT);
        conn.setReadTimeout(Config.HTTP_TIMEOUT);
        
        return handleResponse(conn);
    }
    
    /**
     * Effectue une requête HTTP POST
     */
    private JsonElement doPost(String endpoint, JsonObject body) throws Exception {
        URL url = new URL(Config.API_URL + endpoint);
        HttpURLConnection conn = (HttpURLConnection) url.openConnection();
        conn.setRequestMethod("POST");
        conn.setRequestProperty("Content-Type", "application/json");
        conn.setDoOutput(true);
        
        // Authentification HTTP Basic
        addHttpBasicAuth(conn);
        
        // Ajouter le cookie de session si disponible
        if (sessionCookie != null) {
            conn.setRequestProperty("Cookie", sessionCookie);
        }
        
        conn.setConnectTimeout(Config.HTTP_TIMEOUT);
        conn.setReadTimeout(Config.HTTP_TIMEOUT);
        
        // Envoyer le body JSON
        if (body != null) {
            try (OutputStream os = conn.getOutputStream()) {
                byte[] input = body.toString().getBytes(StandardCharsets.UTF_8);
                os.write(input, 0, input.length);
            }
        }
        
        return handleResponse(conn);
    }
    
    /**
     * Ajoute l'authentification HTTP Basic aux en-têtes de la requête
    /**
     * Ajoute l'authentification HTTP Basic aux en-têtes de la requête
     */
    private void addHttpBasicAuth(HttpURLConnection conn) {
        if (Config.HTTP_AUTH_USER != null && !Config.HTTP_AUTH_USER.equals("VOTRE_LOGIN_UNIV")) {
            String auth = Config.HTTP_AUTH_USER + ":" + Config.HTTP_AUTH_PASSWORD;
            String encodedAuth = Base64.getEncoder().encodeToString(auth.getBytes(StandardCharsets.UTF_8));
            conn.setRequestProperty("Authorization", "Basic " + encodedAuth);
        }
    }
    
    /**
     * Traite la réponse HTTP et extrait le JSON
     */
    private JsonElement handleResponse(HttpURLConnection conn) throws Exception {
        // Récupérer le cookie de session
        String cookieHeader = conn.getHeaderField("Set-Cookie");
        if (cookieHeader != null) {
            System.out.println("=== COOKIE REÇU ===");
            System.out.println(cookieHeader);
            
            // Extraire le PHPSESSID
            if (cookieHeader.contains("PHPSESSID=")) {
                int start = cookieHeader.indexOf("PHPSESSID=");
                int end = cookieHeader.indexOf(";", start);
                if (end == -1) end = cookieHeader.length();
                sessionCookie = cookieHeader.substring(start, end);
                System.out.println("Cookie sauvegardé: " + sessionCookie);
            }
        }
        
        // Debug: afficher le cookie utilisé
        if (sessionCookie != null) {
            System.out.println("Cookie actuel: " + sessionCookie);
        }
        
        // Lire la réponse
        int responseCode = conn.getResponseCode();
        BufferedReader br;
        
        if (responseCode >= 200 && responseCode < 300) {
            br = new BufferedReader(new InputStreamReader(conn.getInputStream(), StandardCharsets.UTF_8));
        } else {
            br = new BufferedReader(new InputStreamReader(conn.getErrorStream(), StandardCharsets.UTF_8));
        }
        
        StringBuilder response = new StringBuilder();
        String line;
        while ((line = br.readLine()) != null) {
            response.append(line);
        }
        br.close();
        
        // Debug: afficher la réponse brute
        String responseText = response.toString();
        System.out.println("=== RÉPONSE API ===");
        System.out.println("Code HTTP: " + responseCode);
        System.out.println("Réponse: " + responseText);
        System.out.println("===================");
        
        // Vérifier si la réponse est vide
        if (responseText == null || responseText.trim().isEmpty()) {
            throw new ApiException("Réponse vide du serveur");
        }
        
        // Vérifier si la réponse commence par un JSON valide
        if (!responseText.trim().startsWith("{") && !responseText.trim().startsWith("[")) {
            System.err.println("ERREUR: La réponse n'est pas du JSON valide");
            System.err.println("Contenu reçu: " + responseText);
            throw new ApiException("Réponse invalide du serveur (non-JSON): " + 
                (responseText.length() > 200 ? responseText.substring(0, 200) + "..." : responseText));
        }
        
        // Parser la réponse JSON
        ApiResponse apiResponse;
        try {
            apiResponse = gson.fromJson(responseText, ApiResponse.class);
        } catch (Exception e) {
            System.err.println("ERREUR DE PARSING JSON: " + e.getMessage());
            throw new ApiException("Erreur de parsing JSON: " + e.getMessage() + " - Réponse: " + 
                (responseText.length() > 200 ? responseText.substring(0, 200) + "..." : responseText));
        }
        
        if (!apiResponse.success) {
            throw new ApiException(apiResponse.message != null ? apiResponse.message : "Erreur API inconnue");
        }
        
        return apiResponse.data;
    }
    
    /**
     * Connexion (login)
     * @return Utilisateur connecté
     */
    public Utilisateur login(String identifiant, String motdepasse) throws Exception {
        JsonObject body = new JsonObject();
        body.addProperty("login", identifiant);
        body.addProperty("password", motdepasse);
        
        JsonElement data = doPost("?endpoint=login", body);
        JsonObject userData = data.getAsJsonObject();
        
        // Créer l'utilisateur à partir des données
        Utilisateur user = new Utilisateur();
        user.setIdUtilisateur(userData.get("id").getAsInt());
        user.setLogin(userData.get("login").getAsString());
        user.setPrenom(userData.get("prenom").getAsString());
        user.setNom(userData.get("nom").getAsString());
        
        // Récupérer l'email s'il existe
        if (userData.has("email") && !userData.get("email").isJsonNull()) {
            user.setEmail(userData.get("email").getAsString());
        }
        
        // Récupérer le téléphone s'il existe
        if (userData.has("telephone") && !userData.get("telephone").isJsonNull()) {
            user.setTelephone(userData.get("telephone").getAsString());
        }
        
        // Récupérer l'adresse s'il existe
        if (userData.has("adresse") && !userData.get("adresse").isJsonNull()) {
            user.setAdresse(userData.get("adresse").getAsString());
        }
        
        // Récupérer le genre s'il existe
        if (userData.has("genre") && !userData.get("genre").isJsonNull()) {
            user.setGenre(userData.get("genre").getAsString());
        }
        
        // Récupérer la date de naissance s'il existe
        if (userData.has("dateNaissance") && !userData.get("dateNaissance").isJsonNull()) {
            user.setDateNaissance(userData.get("dateNaissance").getAsString());
        }
        
        String role = userData.get("role").getAsString();
        
        // Mettre à jour la session
        SessionManager.getInstance().login(user, role, sessionCookie);
        
        return user;
    }
    
    /**
     * Déconnexion (logout)
     */
    public void logout() throws Exception {
        doPost("?endpoint=logout", null);
        sessionCookie = null;
        SessionManager.getInstance().logout();
    }
    
    /**
     * Récupère la liste des promotions
     */
    public List<Promotion> getPromotions() throws Exception {
        JsonElement data = doGet("?endpoint=promotions");
        
        System.out.println("=== DEBUG API PROMOTIONS ===");
        System.out.println("Type de data: " + data.getClass().getName());
        System.out.println("Contenu: " + data.toString());
        
        JsonArray array = data.getAsJsonArray();
        
        List<Promotion> promotions = new ArrayList<>();
        for (JsonElement elem : array) {
            JsonObject obj = elem.getAsJsonObject();
            Promotion promo = new Promotion();
            promo.setId(obj.get("id").getAsString());
            promo.setAnneeScolaire(obj.get("annee").getAsString());
            promo.setSemestre(obj.get("semestre").getAsInt());
            promo.setNomParcours(obj.get("nomParcours").getAsString());
            promo.setIdParcours(obj.get("parcours").getAsString());
            
            // Statistiques (nombre d'étudiants et groupes)
            if (obj.has("nbEtudiants")) {
                promo.setNbEtudiants(obj.get("nbEtudiants").getAsInt());
            }
            if (obj.has("nbGroupes")) {
                promo.setNbGroupes(obj.get("nbGroupes").getAsInt());
            }
            
            promotions.add(promo);
        }
        
        System.out.println("Nombre de promotions parsées: " + promotions.size());
        
        return promotions;
    }
    
    /**
     * Récupère les groupes d'une promotion
     */
    public List<Groupe> getGroupes(String idPromotion) throws Exception {
        JsonElement data = doGet("?endpoint=groupes&promotion=" + idPromotion);
        JsonArray array = data.getAsJsonArray();
        
        List<Groupe> groupes = new ArrayList<>();
        for (JsonElement elem : array) {
            JsonObject obj = elem.getAsJsonObject();
            Groupe groupe = new Groupe();
            groupe.setIdGroupe(obj.get("id").getAsInt());
            groupe.setNomGroupe(obj.get("nom").getAsString());
            if (obj.has("effectif")) {
                groupe.setEffectif(obj.get("effectif").getAsInt());
            }
            if (obj.has("effectifMax")) {
                groupe.setEffectifMax(obj.get("effectifMax").getAsInt());
            }
            groupes.add(groupe);
        }
        
        return groupes;
    }
    
    /**
     * Récupère les étudiants d'une promotion
     */
    public List<Etudiant> getEtudiants(String idPromotion) throws Exception {
        JsonElement data = doGet("?endpoint=etudiants&promotion=" + idPromotion);
        JsonArray array = data.getAsJsonArray();
        
        List<Etudiant> etudiants = new ArrayList<>();
        for (JsonElement elem : array) {
            JsonObject obj = elem.getAsJsonObject();
            Etudiant etudiant = new Etudiant();
            etudiant.setIdEtudiant(obj.get("idEtudiant").getAsInt());
            etudiant.setIdUtilisateur(obj.get("idUtilisateur").getAsInt());
            etudiant.setNom(obj.get("nom").getAsString());
            etudiant.setPrenom(obj.get("prenom").getAsString());
            etudiant.setEmail(obj.get("email").getAsString());
            
            // Gestion du genre qui peut être null
            if (obj.has("genre") && !obj.get("genre").isJsonNull()) {
                String genre = obj.get("genre").getAsString();
                etudiant.setGenre(genre.isEmpty() ? 'M' : genre.charAt(0));
            } else {
                etudiant.setGenre('M'); // Genre par défaut
            }
            
            if (obj.has("idGroupe") && !obj.get("idGroupe").isJsonNull()) {
                etudiant.setIdGroupe(obj.get("idGroupe").getAsInt());
            }
            if (obj.has("nomGroupe") && !obj.get("nomGroupe").isJsonNull()) {
                etudiant.setNomGroupe(obj.get("nomGroupe").getAsString());
            }
            if (obj.has("idCovoiturage") && !obj.get("idCovoiturage").isJsonNull()) {
                etudiant.setIdCovoiturage(obj.get("idCovoiturage").getAsInt());
            }
            if (obj.has("estRedoublant")) {
                etudiant.setEstRedoublant(obj.get("estRedoublant").getAsBoolean());
            }
            if (obj.has("estAnglophone")) {
                etudiant.setEstAnglophone(obj.get("estAnglophone").getAsBoolean());
            }
            if (obj.has("estApprenti")) {
                etudiant.setEstApprenti(obj.get("estApprenti").getAsBoolean());
            }
            if (obj.has("typeBac") && !obj.get("typeBac").isJsonNull()) {
                etudiant.setTypeBac(obj.get("typeBac").getAsString());
            }
            if (obj.has("mentionBac") && !obj.get("mentionBac").isJsonNull()) {
                etudiant.setMentionBac(obj.get("mentionBac").getAsString());
            }
            
            etudiants.add(etudiant);
        }
        
        return etudiants;
    }
    
    /**
     * Enregistre les affectations d'étudiants aux groupes
     */
    public boolean saveAffectations(List<Affectation> affectations) throws Exception {
        JsonObject body = new JsonObject();
        JsonArray items = new JsonArray();
        
        for (Affectation aff : affectations) {
            JsonObject item = new JsonObject();
            item.addProperty("idEtudiant", aff.idEtudiant);
            item.addProperty("idGroupe", aff.idGroupe);
            items.add(item);
        }
        
        body.add("affectations", items);
        
        // Pour l'endpoint affectations, on récupère directement la réponse complète
        // car il n'y a pas de champ "data", juste "success", "message" et "count"
        URL url = new URL(Config.API_URL + "?endpoint=affectations");
        HttpURLConnection conn = (HttpURLConnection) url.openConnection();
        conn.setRequestMethod("POST");
        conn.setRequestProperty("Content-Type", "application/json");
        conn.setDoOutput(true);
        
        addHttpBasicAuth(conn);
        if (sessionCookie != null) {
            conn.setRequestProperty("Cookie", sessionCookie);
        }
        
        conn.setConnectTimeout(Config.HTTP_TIMEOUT);
        conn.setReadTimeout(Config.HTTP_TIMEOUT);
        
        try (OutputStream os = conn.getOutputStream()) {
            byte[] input = body.toString().getBytes(StandardCharsets.UTF_8);
            os.write(input, 0, input.length);
        }
        
        int responseCode = conn.getResponseCode();
        BufferedReader br;
        
        if (responseCode >= 200 && responseCode < 300) {
            br = new BufferedReader(new InputStreamReader(conn.getInputStream(), StandardCharsets.UTF_8));
        } else {
            br = new BufferedReader(new InputStreamReader(conn.getErrorStream(), StandardCharsets.UTF_8));
        }
        
        StringBuilder response = new StringBuilder();
        String line;
        while ((line = br.readLine()) != null) {
            response.append(line);
        }
        br.close();
        
        String responseText = response.toString();
        System.out.println("Réponse affectations: " + responseText);
        
        JsonObject result = gson.fromJson(responseText, JsonObject.class);
        
        if (!result.get("success").getAsBoolean()) {
            String message = result.has("message") ? result.get("message").getAsString() : "Erreur inconnue";
            throw new ApiException(message);
        }
        
        return result.has("count") && result.get("count").getAsInt() > 0;
    }
    
    /**
     * Ajouter un nouvel étudiant
     */
    public Etudiant ajouterEtudiant(String nom, String prenom, String email, String login, 
                                    String motDePasse, String genre, String typeBac, 
                                    String mentionBac, boolean estRedoublant, 
                                    boolean estAnglophone, String idPromotion) throws Exception {
        JsonObject body = new JsonObject();
        body.addProperty("nom", nom);
        body.addProperty("prenom", prenom);
        body.addProperty("email", email);
        body.addProperty("login", login);
        body.addProperty("motDePasse", motDePasse);
        body.addProperty("genre", genre);
        body.addProperty("typeBac", typeBac);
        if (mentionBac != null && !mentionBac.isEmpty()) {
            body.addProperty("mentionBac", mentionBac);
        }
        body.addProperty("estRedoublant", estRedoublant);
        body.addProperty("estAnglophone", estAnglophone);
        body.addProperty("idPromotion", idPromotion);
        
        JsonElement data = doPost("?endpoint=ajouter_etudiant", body);
        JsonObject etudiantJson = data.getAsJsonObject();
        
        Etudiant etudiant = new Etudiant();
        etudiant.setIdEtudiant(etudiantJson.get("idEtudiant").getAsInt());
        etudiant.setNom(nom);
        etudiant.setPrenom(prenom);
        etudiant.setEmail(email);
        etudiant.setGenre(genre.charAt(0)); // Convertir String en char
        
        if (etudiantJson.has("idGroupe") && !etudiantJson.get("idGroupe").isJsonNull()) {
            etudiant.setIdGroupe(etudiantJson.get("idGroupe").getAsInt());
        } else {
            etudiant.setIdGroupe(0);
        }
        
        if (etudiantJson.has("nomGroupe") && !etudiantJson.get("nomGroupe").isJsonNull()) {
            etudiant.setNomGroupe(etudiantJson.get("nomGroupe").getAsString());
        }
        
        etudiant.setEstRedoublant(estRedoublant);
        etudiant.setEstAnglophone(estAnglophone);
        etudiant.setTypeBac(typeBac);
        if (mentionBac != null) {
            etudiant.setMentionBac(mentionBac);
        }
        
        return etudiant;
    }
    
    /**
     * Modifier un étudiant existant
     */
    public Etudiant modifierEtudiant(int idEtudiant, String nom, String prenom, String email, 
                                     String login, String motDePasse, String genre, 
                                     String typeBac, String mentionBac, boolean estRedoublant, 
                                     boolean estAnglophone) throws Exception {
        JsonObject body = new JsonObject();
        body.addProperty("idEtudiant", idEtudiant);
        body.addProperty("nom", nom);
        body.addProperty("prenom", prenom);
        body.addProperty("email", email);
        body.addProperty("login", login);
        if (motDePasse != null && !motDePasse.isEmpty()) {
            body.addProperty("motDePasse", motDePasse);
        }
        body.addProperty("genre", genre);
        body.addProperty("typeBac", typeBac);
        if (mentionBac != null && !mentionBac.isEmpty()) {
            body.addProperty("mentionBac", mentionBac);
        }
        body.addProperty("estRedoublant", estRedoublant);
        body.addProperty("estAnglophone", estAnglophone);
        
        JsonElement data = doPost("?endpoint=modifier_etudiant", body);
        JsonObject etudiantJson = data.getAsJsonObject();
        
        Etudiant etudiant = new Etudiant();
        etudiant.setIdEtudiant(idEtudiant);
        etudiant.setNom(nom);
        etudiant.setPrenom(prenom);
        etudiant.setEmail(email);
        etudiant.setGenre(genre.charAt(0));
        
        if (etudiantJson.has("idGroupe") && !etudiantJson.get("idGroupe").isJsonNull()) {
            etudiant.setIdGroupe(etudiantJson.get("idGroupe").getAsInt());
        } else {
            etudiant.setIdGroupe(0);
        }
        
        if (etudiantJson.has("nomGroupe") && !etudiantJson.get("nomGroupe").isJsonNull()) {
            etudiant.setNomGroupe(etudiantJson.get("nomGroupe").getAsString());
        }
        
        etudiant.setEstRedoublant(estRedoublant);
        etudiant.setEstAnglophone(estAnglophone);
        etudiant.setTypeBac(typeBac);
        if (mentionBac != null) {
            etudiant.setMentionBac(mentionBac);
        }
        
        return etudiant;
    }
    
    /**
     * Supprimer un étudiant
     */
    public void supprimerEtudiant(int idEtudiant) throws Exception {
        JsonObject body = new JsonObject();
        body.addProperty("idEtudiant", idEtudiant);
        
        doPost("?endpoint=supprimer_etudiant", body);
    }
    
    /**
     * Classe pour représenter une affectation étudiant-groupe
     */
    public static class Affectation {
        public int idEtudiant;
        public int idGroupe;
        
        public Affectation(int idEtudiant, int idGroupe) {
            this.idEtudiant = idEtudiant;
            this.idGroupe = idGroupe;
        }
    }
    
    /**
     * Exception personnalisée pour les erreurs API
     */
    public static class ApiException extends Exception {
        public ApiException(String message) {
            super(message);
        }
    }
}
