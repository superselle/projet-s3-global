package vue;

import java.awt.*;
import javax.swing.*;
import modele.Utilisateur;
import utils.Config;
import utils.RoundedPanel;
import utils.RoundedButton;
import utils.SessionManager;

/**
 * Vue pour afficher les informations personnelles de l'utilisateur
 */
public class VueMesInformations extends JFrame {
    
    private Utilisateur utilisateur;
    
    public VueMesInformations() {
        this.utilisateur = SessionManager.getInstance().getCurrentUser();
        initialiserInterface();
    }
    
    private void initialiserInterface() {
        setTitle(Config.APP_TITLE + " - Mes informations");
        setSize(900, 700);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        
        JPanel panelPrincipal = new JPanel(new BorderLayout(20, 20));
        panelPrincipal.setBackground(new Color(245, 247, 250));
        panelPrincipal.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));
        
        // En-té©Âªte
        RoundedPanel panelEnTete = new RoundedPanel(15);
        panelEnTete.setLayout(new BorderLayout());
        panelEnTete.setBackgroundColor(Color.WHITE);
        panelEnTete.setDrawShadow(true);
        panelEnTete.setBorder(BorderFactory.createEmptyBorder(25, 30, 25, 30));
        
        JLabel titre = new JLabel("Mes informations");
        titre.setFont(new Font("Segoe UI", Font.BOLD, 28));
        titre.setForeground(new Color(28, 53, 94));
        
        panelEnTete.add(titre, BorderLayout.WEST);
        panelPrincipal.add(panelEnTete, BorderLayout.NORTH);
        
        // Contenu avec scroll
        JPanel panelContenu = new JPanel();
        panelContenu.setLayout(new BoxLayout(panelContenu, BoxLayout.Y_AXIS));
        panelContenu.setBackground(new Color(245, 247, 250));
        
        // Section Identité
        panelContenu.add(creerSectionIdentite());
        panelContenu.add(Box.createVerticalStrut(20));
        
        // Section Contact
        panelContenu.add(creerSectionContact());
        panelContenu.add(Box.createVerticalStrut(20));
        
        // Section Informations complémentaires
        panelContenu.add(creerSectionInfosComplementaires());
        
        JScrollPane scrollPane = new JScrollPane(panelContenu);
        scrollPane.setBorder(null);
        scrollPane.getVerticalScrollBar().setUnitIncrement(16);
        panelPrincipal.add(scrollPane, BorderLayout.CENTER);
        
        // Bouton retour
        JPanel panelBoutons = new JPanel(new FlowLayout(FlowLayout.LEFT));
        panelBoutons.setBackground(new Color(245, 247, 250));
        
        RoundedButton btnRetour = new RoundedButton("Retour au Menu", new Color(149, 165, 166));
        btnRetour.setPreferredSize(new Dimension(160, 45));
        btnRetour.setFont(new Font("Segoe UI", Font.BOLD, 14));
        btnRetour.setForeground(Color.WHITE);
        btnRetour.addActionListener(e -> retourMenu());
        
        panelBoutons.add(btnRetour);
        panelPrincipal.add(panelBoutons, BorderLayout.SOUTH);
        
        add(panelPrincipal);
    }
    
    private RoundedPanel creerSectionIdentite() {
        RoundedPanel panel = new RoundedPanel(15);
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackgroundColor(Color.WHITE);
        panel.setDrawShadow(true);
        panel.setBorder(BorderFactory.createEmptyBorder(25, 30, 25, 30));
        panel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 200));
        
        JLabel titre = new JLabel("Identité");
        titre.setFont(new Font("Segoe UI", Font.BOLD, 20));
        titre.setForeground(new Color(28, 53, 94));
        titre.setAlignmentX(Component.LEFT_ALIGNMENT);
        
        panel.add(titre);
        panel.add(Box.createVerticalStrut(15));
        
        // Rôle
        String role = SessionManager.getInstance().getUserRole();
        if (role != null) {
            role = role.replace("_", " ").toLowerCase();
            role = Character.toUpperCase(role.charAt(0)) + role.substring(1);
        } else {
            role = "Non renseigné";
        }
        panel.add(creerLigneInfo("Rôle :", role));
        panel.add(Box.createVerticalStrut(10));
        
        // Nom
        String nom = utilisateur.getNom() != null ? utilisateur.getNom().toUpperCase() : "Non renseigné";
        panel.add(creerLigneInfo("Nom :", nom));
        panel.add(Box.createVerticalStrut(10));
        
        // Prénom
        String prenom = utilisateur.getPrenom() != null ? utilisateur.getPrenom() : "Non renseigné";
        panel.add(creerLigneInfo("Prénom :", prenom));
        
        return panel;
    }
    
    private RoundedPanel creerSectionContact() {
        RoundedPanel panel = new RoundedPanel(15);
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackgroundColor(Color.WHITE);
        panel.setDrawShadow(true);
        panel.setBorder(BorderFactory.createEmptyBorder(25, 30, 25, 30));
        panel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 220));
        
        JLabel titre = new JLabel("Contact");
        titre.setFont(new Font("Segoe UI", Font.BOLD, 20));
        titre.setForeground(new Color(28, 53, 94));
        titre.setAlignmentX(Component.LEFT_ALIGNMENT);
        
        panel.add(titre);
        panel.add(Box.createVerticalStrut(15));
        
        // Email
        String email = utilisateur.getEmail();
        if (email == null || email.equals("null") || email.isEmpty()) {
            email = "Non renseigné";
        }
        panel.add(creerLigneInfo("Email :", email));
        panel.add(Box.createVerticalStrut(10));
        
        // Téléphone
        String telephone = utilisateur.getTelephone();
        if (telephone == null || telephone.equals("null") || telephone.isEmpty()) {
            telephone = "Non renseigné";
        }
        panel.add(creerLigneInfo("Téléphone :", telephone));
        panel.add(Box.createVerticalStrut(10));
        
        // Adresse
        String adresse = utilisateur.getAdresse();
        if (adresse == null || adresse.equals("null") || adresse.isEmpty()) {
            adresse = "Non renseigné";
        }
        panel.add(creerLigneInfo("Adresse :", adresse));
        
        return panel;
    }
    
    private RoundedPanel creerSectionInfosComplementaires() {
        RoundedPanel panel = new RoundedPanel(15);
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackgroundColor(Color.WHITE);
        panel.setDrawShadow(true);
        panel.setBorder(BorderFactory.createEmptyBorder(25, 30, 25, 30));
        panel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 180));
        
        JLabel titre = new JLabel("Informations complémentaires");
        titre.setFont(new Font("Segoe UI", Font.BOLD, 20));
        titre.setForeground(new Color(28, 53, 94));
        titre.setAlignmentX(Component.LEFT_ALIGNMENT);
        
        panel.add(titre);
        panel.add(Box.createVerticalStrut(15));
        
        // Genre
        String genre = utilisateur.getGenre();
        if (genre == null || genre.equals("null") || genre.isEmpty()) {
            genre = "Non renseigné";
        }
        panel.add(creerLigneInfo("Genre :", genre));
        panel.add(Box.createVerticalStrut(10));
        
        // Date de naissance
        String dateNaissance = utilisateur.getDateNaissance();
        if (dateNaissance == null || dateNaissance.equals("null") || dateNaissance.isEmpty()) {
            dateNaissance = "Non renseigné";
        }
        panel.add(creerLigneInfo("Date de naissance :", dateNaissance));
        
        return panel;
    }
    
    private JPanel creerLigneInfo(String label, String valeur) {
        JPanel ligne = new JPanel(new FlowLayout(FlowLayout.LEFT, 0, 0));
        ligne.setBackground(Color.WHITE);
        ligne.setAlignmentX(Component.LEFT_ALIGNMENT);
        ligne.setMaximumSize(new Dimension(Integer.MAX_VALUE, 30));
        
        JLabel lblLabel = new JLabel(label);
        lblLabel.setFont(new Font("Segoe UI", Font.BOLD, 14));
        lblLabel.setForeground(new Color(28, 53, 94));
        lblLabel.setPreferredSize(new Dimension(180, 25));
        
        JLabel lblValeur = new JLabel(valeur);
        lblValeur.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        lblValeur.setForeground(new Color(60, 60, 60));
        
        ligne.add(lblLabel);
        ligne.add(Box.createHorizontalStrut(10));
        ligne.add(lblValeur);
        
        return ligne;
    }
    
    private void retourMenu() {
        new VueDashboard().setVisible(true);
        this.dispose();
    }
}
