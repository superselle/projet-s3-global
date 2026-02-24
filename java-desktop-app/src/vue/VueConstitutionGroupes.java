package vue;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.Font;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.util.List;

import javax.swing.BorderFactory;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JComboBox;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;

import controleur.ControleurPromotion;
import modele.Promotion;
import utils.Config;
import utils.RoundedButton;
import utils.RoundedPanel;

/**
 * Vue pour la constitution des groupes
 * Permet de choisir une promotion et le mode de répartition
 */
public class VueConstitutionGroupes extends JFrame {
    
    private ControleurPromotion controleurPromotion;
    private JComboBox<Promotion> comboPromotions;
    private JButton boutonManuel;
    private JButton boutonAutomatique;
    private JButton boutonRetour;
    
    public VueConstitutionGroupes() {
        this.controleurPromotion = new ControleurPromotion();
        initialiserInterface();
        chargerPromotions();
    }
    
    private void initialiserInterface() {
        setTitle(Config.APP_TITLE + " - Constitution des Groupes");
        setSize(900, 750);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        
        JPanel panelPrincipal = new JPanel(new BorderLayout(20, 20));
        panelPrincipal.setBackground(new Color(245, 247, 250));
        panelPrincipal.setBorder(BorderFactory.createEmptyBorder(40, 40, 40, 40));
        
        // En-tête
        JPanel panelEnTete = creerEnTete();
        panelPrincipal.add(panelEnTete, BorderLayout.NORTH);
        
        // Centre - Sélection et options
        JPanel panelCentre = creerPanelCentre();
        panelPrincipal.add(panelCentre, BorderLayout.CENTER);
        
        // Boutons
        JPanel panelBoutons = creerPanelBoutons();
        panelPrincipal.add(panelBoutons, BorderLayout.SOUTH);
        
        add(panelPrincipal);
    }
    
    private JPanel creerEnTete() {
        RoundedPanel panel = new RoundedPanel(15);
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackgroundColor(Color.WHITE);
        panel.setDrawShadow(true);
        panel.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));
        
        JLabel titre = new JLabel("Constitution des Groupes");
        titre.setFont(new Font("Segoe UI", Font.BOLD, 32));
        titre.setForeground(new Color(28, 53, 94));
        titre.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        JLabel sousTitre = new JLabel("Choisissez une promotion et un mode de répartition");
        sousTitre.setFont(new Font("Segoe UI", Font.PLAIN, 16));
        sousTitre.setForeground(new Color(127, 140, 141));
        sousTitre.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        panel.add(titre);
        panel.add(javax.swing.Box.createVerticalStrut(10));
        panel.add(sousTitre);
        
        return panel;
    }
    
    private JPanel creerPanelCentre() {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);
        
        // Sélection de la promotion
        RoundedPanel panelPromotion = new RoundedPanel(15);
        panelPromotion.setLayout(new BorderLayout(10, 10));
        panelPromotion.setBackgroundColor(Color.WHITE);
        panelPromotion.setDrawShadow(true);
        panelPromotion.setBorder(BorderFactory.createEmptyBorder(25, 25, 25, 25));
        panelPromotion.setMaximumSize(new Dimension(Integer.MAX_VALUE, 120));
        
        JLabel labelPromotion = new JLabel("Sélectionner une promotion :");
        labelPromotion.setFont(new Font("Segoe UI", Font.BOLD, 16));
        
        comboPromotions = new JComboBox<>();
        comboPromotions.setFont(new Font("Arial", Font.PLAIN, 14));
        comboPromotions.setPreferredSize(new Dimension(500, 35));
        
        panelPromotion.add(labelPromotion, BorderLayout.NORTH);
        panelPromotion.add(comboPromotions, BorderLayout.CENTER);
        
        panel.add(panelPromotion);
        panel.add(javax.swing.Box.createVerticalStrut(30));
        
        // Choix du mode de répartition
        JLabel labelMode = new JLabel("Choisir le mode de répartition :");
        labelMode.setFont(new Font("Segoe UI", Font.BOLD, 18));
        labelMode.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        panel.add(labelMode);
        panel.add(javax.swing.Box.createVerticalStrut(20));
        
        // Boutons des modes
        JPanel panelModes = new JPanel(new GridLayout(1, 2, 20, 20));
        panelModes.setBackground(new Color(245, 247, 250));
        panelModes.setMaximumSize(new Dimension(Integer.MAX_VALUE, 150));
        
        RoundedPanel carteManuel = creerCarteMode(
            "Répartition Manuelle",
            "Glisser-déposer les étudiants dans les groupes",
            new Color(52, 152, 219),
            () -> ouvrirRepartitionManuelle()
        );
        
        RoundedPanel carteAuto = creerCarteMode(
            "Répartition Automatique",
            "Utiliser les algorithmes de répartition automatique",
            new Color(46, 204, 113),
            () -> ouvrirRepartitionAutomatique()
        );
        
        panelModes.add(carteManuel);
        panelModes.add(carteAuto);
        
        panel.add(panelModes);
        
        return panel;
    }
    
    private RoundedPanel creerCarteMode(String titre, String description, Color couleur, Runnable action) {
        RoundedPanel carte = new RoundedPanel(15);
        carte.setLayout(new BoxLayout(carte, BoxLayout.Y_AXIS));
        carte.setBackgroundColor(couleur);
        carte.setDrawShadow(true);
        carte.setBorder(BorderFactory.createEmptyBorder(30, 25, 30, 25));
        carte.setCursor(new java.awt.Cursor(java.awt.Cursor.HAND_CURSOR));
        
        JLabel labelTitre = new JLabel(titre);
        labelTitre.setFont(new Font("Segoe UI", Font.BOLD, 18));
        labelTitre.setForeground(Color.WHITE);
        labelTitre.setAlignmentX(JLabel.CENTER_ALIGNMENT);
        
        JLabel labelDesc = new JLabel("<html><center>" + description + "</center></html>");
        labelDesc.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        labelDesc.setForeground(new Color(255, 255, 255, 220));
        labelDesc.setAlignmentX(JLabel.CENTER_ALIGNMENT);
        
        carte.add(labelTitre);
        carte.add(javax.swing.Box.createVerticalStrut(15));
        carte.add(labelDesc);
        
        // Effet hover et clic
        final Color baseColor = couleur;
        final Color hoverColor = brightenColor(couleur, 0.15f);
        
        carte.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                action.run();
            }
            
            public void mouseEntered(java.awt.event.MouseEvent evt) {
                carte.setBackgroundColor(hoverColor);
                carte.repaint();
            }
            
            public void mouseExited(java.awt.event.MouseEvent evt) {
                carte.setBackgroundColor(baseColor);
                carte.repaint();
            }
        });
        
        return carte;
    }
    
    private Color brightenColor(Color color, float factor) {
        int r = Math.min(255, (int) (color.getRed() * (1 + factor)));
        int g = Math.min(255, (int) (color.getGreen() * (1 + factor)));
        int b = Math.min(255, (int) (color.getBlue() * (1 + factor)));
        return new Color(r, g, b);
    }
    
    private JPanel creerPanelBoutons() {
        JPanel panel = new JPanel(new java.awt.FlowLayout(java.awt.FlowLayout.RIGHT, 10, 0));
        panel.setBackground(new Color(245, 247, 250));
        
        boutonRetour = new RoundedButton("Retour au Menu", new Color(149, 165, 166));
        boutonRetour.setPreferredSize(new Dimension(160, 45));
        boutonRetour.setFont(new Font("Segoe UI", Font.BOLD, 14));
        boutonRetour.setForeground(Color.WHITE);
        boutonRetour.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                retourMenu();
            }
        });
        
        panel.add(boutonRetour);
        
        return panel;
    }
    
    private void chargerPromotions() {
        List<Promotion> promotions = controleurPromotion.getPromotions();
        for (Promotion promo : promotions) {
            comboPromotions.addItem(promo);
        }
    }
    
    private void ouvrirRepartitionManuelle() {
        Promotion promo = (Promotion) comboPromotions.getSelectedItem();
        if (promo == null) {
            JOptionPane.showMessageDialog(
                this,
                "Veuillez sélectionner une promotion.",
                "Aucune promotion",
                JOptionPane.WARNING_MESSAGE
            );
            return;
        }
        
        new VueRepartitionManuelleComplete(promo).setVisible(true);
        this.dispose();
    }
    
    private void ouvrirRepartitionAutomatique() {
        Promotion promo = (Promotion) comboPromotions.getSelectedItem();
        if (promo == null) {
            JOptionPane.showMessageDialog(
                this,
                "Veuillez sélectionner une promotion.",
                "Aucune promotion",
                JOptionPane.WARNING_MESSAGE
            );
            return;
        }
        
        new VueRepartitionAutomatique(promo).setVisible(true);
        this.dispose();
    }
    
    private void retourMenu() {
        new VueDashboard().setVisible(true);
        this.dispose();
    }
}
