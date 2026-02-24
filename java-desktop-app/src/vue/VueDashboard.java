package vue;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Cursor;
import java.awt.Dimension;
import java.awt.Font;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;

import javax.swing.BorderFactory;
import javax.swing.Box;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;

import controleur.ControleurAuth;
import utils.Config;
import utils.RoundedButton;
import utils.RoundedPanel;
import utils.SessionManager;

/**
 * Vue principale du dashboard (tableau de bord)
 */
public class VueDashboard extends JFrame {
    
    private ControleurAuth controleurAuth;
    
    public VueDashboard() {
        this.controleurAuth = new ControleurAuth();
        initialiserInterface();
    }
    
    private void initialiserInterface() {
        setTitle(Config.APP_TITLE);
        setSize(Config.WINDOW_WIDTH, Config.WINDOW_HEIGHT);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        
        JPanel panelPrincipal = new JPanel(new BorderLayout());
        panelPrincipal.setBackground(new Color(245, 245, 245));
        
        // En-tête universitaire
        JPanel panelEnTete = creerEnTeteUniversitaire();
        panelPrincipal.add(panelEnTete, BorderLayout.NORTH);
        
        // Menu latéral
        JPanel panelMenu = creerMenuLateral();
        panelPrincipal.add(panelMenu, BorderLayout.WEST);
        
        // Zone centrale avec bienvenue
        JPanel panelCentre = creerPanelCentre();
        panelPrincipal.add(panelCentre, BorderLayout.CENTER);
        
        add(panelPrincipal);
    }
    
    private JPanel creerEnTeteUniversitaire() {
        JPanel panel = new JPanel(new BorderLayout());
        panel.setBackground(new Color(28, 53, 94)); // Bleu universitaire
        panel.setPreferredSize(new Dimension(0, 100));
        panel.setBorder(BorderFactory.createEmptyBorder(15, 30, 15, 30));
        
        // Logo et titre université (gauche)
        JPanel panelGauche = new JPanel();
        panelGauche.setLayout(new BoxLayout(panelGauche, BoxLayout.Y_AXIS));
        panelGauche.setBackground(new Color(28, 53, 94));
        
        JLabel lblUniversite = new JLabel("UNIVERSITé");
        lblUniversite.setFont(new Font("Arial", Font.PLAIN, 12));
        lblUniversite.setForeground(Color.WHITE);
        lblUniversite.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        JLabel lblParisSaclay = new JLabel("PARIS-SACLAY");
        lblParisSaclay.setFont(new Font("Arial", Font.BOLD, 20));
        lblParisSaclay.setForeground(Color.WHITE);
        lblParisSaclay.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        JLabel lblIUT = new JLabel("IUT D'ORSAY");
        lblIUT.setFont(new Font("Arial", Font.PLAIN, 12));
        lblIUT.setForeground(Color.WHITE);
        lblIUT.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        panelGauche.add(lblUniversite);
        panelGauche.add(lblParisSaclay);
        panelGauche.add(lblIUT);
        
        // Infos utilisateur et déconnexion (droite)
        JPanel panelDroite = new JPanel();
        panelDroite.setLayout(new BoxLayout(panelDroite, BoxLayout.Y_AXIS));
        panelDroite.setBackground(new Color(28, 53, 94));
        
        String nomComplet = SessionManager.getInstance().getCurrentUser().getPrenom() + " " + 
                           SessionManager.getInstance().getCurrentUser().getNom().toUpperCase();
        
        JLabel lblNom = new JLabel("Bienvenue " + nomComplet);
        lblNom.setFont(new Font("Arial", Font.BOLD, 14));
        lblNom.setForeground(Color.WHITE);
        lblNom.setAlignmentX(JLabel.RIGHT_ALIGNMENT);
        
        JButton btnRole = new JButton("Responsable filière");
        btnRole.setFont(new Font("Arial", Font.PLAIN, 12));
        btnRole.setBackground(Color.WHITE);
        btnRole.setForeground(new Color(28, 53, 94));
        btnRole.setFocusPainted(false);
        btnRole.setBorderPainted(false);
        btnRole.setAlignmentX(JButton.RIGHT_ALIGNMENT);
        btnRole.setEnabled(false);
        
        JButton btnDeconnexion = new JButton("Déconnexion");
        btnDeconnexion.setFont(new Font("Arial", Font.PLAIN, 12));
        btnDeconnexion.setForeground(Color.WHITE);
        btnDeconnexion.setBackground(new Color(28, 53, 94));
        btnDeconnexion.setBorder(BorderFactory.createEmptyBorder(5, 10, 5, 10));
        btnDeconnexion.setFocusPainted(false);
        btnDeconnexion.setCursor(new Cursor(Cursor.HAND_CURSOR));
        btnDeconnexion.setAlignmentX(JButton.RIGHT_ALIGNMENT);
        btnDeconnexion.addActionListener(e -> deconnecter());
        
        panelDroite.add(lblNom);
        panelDroite.add(Box.createVerticalStrut(5));
        panelDroite.add(btnRole);
        panelDroite.add(Box.createVerticalStrut(5));
        panelDroite.add(btnDeconnexion);
        
        panel.add(panelGauche, BorderLayout.WEST);
        panel.add(panelDroite, BorderLayout.EAST);
        
        return panel;
    }
    
    private JPanel creerMenuLateral() {
        RoundedPanel panel = new RoundedPanel(new BoxLayout(new JPanel(), BoxLayout.Y_AXIS), 0);
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackgroundColor(new Color(250, 250, 252));
        panel.setPreferredSize(new Dimension(270, 0));
        panel.setBorder(BorderFactory.createEmptyBorder(30, 20, 20, 20));
        
        Color couleurBouton = new Color(139, 28, 58); // Rouge bordeaux
        
        panel.add(creerBoutonMenuModerne("Consulter promotions", couleurBouton, () -> ouvrirPromotions()));
        panel.add(Box.createVerticalStrut(15));
        panel.add(creerBoutonMenuModerne("Constitution groupes", couleurBouton, () -> ouvrirConstitution()));
        panel.add(Box.createVerticalStrut(15));
        panel.add(creerBoutonMenuModerne("Gestion étudiants", couleurBouton, () -> ouvrirGestionEtudiants()));
        panel.add(Box.createVerticalStrut(15));
        panel.add(creerBoutonMenuModerne("Gestion Sondages", couleurBouton, () -> {}));
        panel.add(Box.createVerticalStrut(15));
        panel.add(creerBoutonMenuModerne("Mes informations", couleurBouton, () -> ouvrirMesInformations()));
        
        return panel;
    }
    
    private RoundedButton creerBoutonMenuModerne(String texte, Color couleur, Runnable action) {
        RoundedButton bouton = new RoundedButton(texte, couleur);
        bouton.setPreferredSize(new Dimension(230, 60));
        bouton.setMaximumSize(new Dimension(230, 60));
        bouton.setFont(new Font("Segoe UI", Font.BOLD, 14));
        bouton.setForeground(Color.WHITE);
        
        bouton.addActionListener(e -> action.run());
        
        return bouton;
    }
    
    private JPanel creerBoutonMenu(String texte, Color couleur, Color couleurHover, Runnable action) {
        JPanel bouton = new JPanel();
        bouton.setLayout(new BorderLayout());
        bouton.setBackground(couleur);
        bouton.setPreferredSize(new Dimension(230, 60));
        bouton.setMaximumSize(new Dimension(230, 60));
        bouton.setBorder(BorderFactory.createEmptyBorder(15, 20, 15, 20));
        bouton.setCursor(new Cursor(Cursor.HAND_CURSOR));
        
        JLabel label = new JLabel(texte);
        label.setFont(new Font("Arial", Font.BOLD, 15));
        label.setForeground(Color.WHITE);
        
        bouton.add(label, BorderLayout.CENTER);
        
        bouton.addMouseListener(new MouseAdapter() {
            public void mouseClicked(MouseEvent e) {
                action.run();
            }
            
            public void mouseEntered(MouseEvent e) {
                bouton.setBackground(couleurHover);
            }
            
            public void mouseExited(MouseEvent e) {
                bouton.setBackground(couleur);
            }
        });
        
        return bouton;
    }
    
    private JPanel creerPanelCentre() {
        JPanel panel = new JPanel(new BorderLayout(20, 20));
        panel.setBackground(new Color(245, 247, 250));
        panel.setBorder(BorderFactory.createEmptyBorder(40, 40, 40, 40));
        
        // Message de bienvenue
        RoundedPanel panelBienvenue = new RoundedPanel(15);
        panelBienvenue.setLayout(new BoxLayout(panelBienvenue, BoxLayout.Y_AXIS));
        panelBienvenue.setBackgroundColor(Color.WHITE);
        panelBienvenue.setDrawShadow(true);
        panelBienvenue.setBorder(BorderFactory.createEmptyBorder(35, 35, 35, 35));
        
        String nomComplet = SessionManager.getInstance().getCurrentUser().getPrenom() + " " + 
                           SessionManager.getInstance().getCurrentUser().getNom().toUpperCase();
        
        JLabel lblBienvenue = new JLabel("Bienvenue " + nomComplet + " !");
        lblBienvenue.setFont(new Font("Segoe UI", Font.BOLD, 28));
        lblBienvenue.setForeground(new Color(28, 53, 94));
        lblBienvenue.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        JLabel lblSoustitre = new JLabel("Espace de pilotage de la filière.");
        lblSoustitre.setFont(new Font("Segoe UI", Font.PLAIN, 16));
        lblSoustitre.setForeground(new Color(100, 100, 100));
        lblSoustitre.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        panelBienvenue.add(lblBienvenue);
        panelBienvenue.add(Box.createVerticalStrut(15));
        panelBienvenue.add(lblSoustitre);
        
        // Informations
        RoundedPanel panelInfos = new RoundedPanel(15);
        panelInfos.setLayout(new BoxLayout(panelInfos, BoxLayout.Y_AXIS));
        panelInfos.setBackgroundColor(Color.WHITE);
        panelInfos.setDrawShadow(true);
        panelInfos.setBorder(BorderFactory.createEmptyBorder(18, 30, 18, 30));
        
        JLabel lblTitreInfos = new JLabel("Mes informations");
        lblTitreInfos.setFont(new Font("Segoe UI", Font.BOLD, 16));
        lblTitreInfos.setForeground(new Color(28, 53, 94));
        lblTitreInfos.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        String email = SessionManager.getInstance().getCurrentUser().getEmail();
        if (email == null || email.equals("null") || email.isEmpty()) {
            email = "Non renseigné";
        }
        JLabel lblEmail = new JLabel("• Email : " + email);
        lblEmail.setFont(new Font("Segoe UI", Font.PLAIN, 13));
        lblEmail.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        JLabel lblRole = new JLabel("• Rôle : Responsable Filière");
        lblRole.setFont(new Font("Segoe UI", Font.PLAIN, 13));
        lblRole.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        panelInfos.add(lblTitreInfos);
        panelInfos.add(Box.createVerticalStrut(10));
        panelInfos.add(lblEmail);
        panelInfos.add(Box.createVerticalStrut(5));
        panelInfos.add(lblRole);
        
        panel.add(panelBienvenue, BorderLayout.NORTH);
        panel.add(panelInfos, BorderLayout.CENTER);
        
        return panel;
    }
    
    private void ouvrirPromotions() {
        new VuePromotions().setVisible(true);
        this.dispose();
    }
    
    private void ouvrirConstitution() {
        new VueConstitutionGroupes().setVisible(true);
        this.dispose();
    }
    
    private void ouvrirGestionEtudiants() {
        new VueGestionEtudiants().setVisible(true);
        this.dispose();
    }
    
    private void ouvrirMesInformations() {
        new VueMesInformations().setVisible(true);
        this.dispose();
    }
    
    private void deconnecter() {
        try {
            controleurAuth.logout();
            new VueConnexion().setVisible(true);
            this.dispose();
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}
