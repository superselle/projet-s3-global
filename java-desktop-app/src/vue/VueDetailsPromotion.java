package vue;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.Font;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.util.List;

import javax.swing.BorderFactory;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTable;
import javax.swing.table.DefaultTableModel;

import controleur.ControleurPromotion;
import controleur.ControleurPromotion.StatistiquesPromotion;
import modele.Etudiant;
import modele.Promotion;
import utils.Config;

/**
 * Vue détaillée d'une promotion avec liste des étudiants
 */
public class VueDetailsPromotion extends JFrame {
    
    private Promotion promotion;
    private ControleurPromotion controleurPromotion;
    private JTable tableEtudiants;
    private DefaultTableModel modeleTable;
    private JLabel labelStatistiques;
    
    public VueDetailsPromotion(Promotion promotion) {
        this.promotion = promotion;
        this.controleurPromotion = new ControleurPromotion();
        initialiserInterface();
        chargerEtudiants();
    }
    
    private void initialiserInterface() {
        setTitle(Config.APP_TITLE + " - " + promotion.getLibelle());
        setSize(Config.WINDOW_WIDTH, Config.WINDOW_HEIGHT);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        
        JPanel panelPrincipal = new JPanel(new BorderLayout(10, 10));
        panelPrincipal.setBackground(Color.WHITE);
        panelPrincipal.setBorder(BorderFactory.createEmptyBorder(20, 20, 20, 20));
        
        // En-tête avec titre et statistiques
        JPanel panelEnTete = creerEnTete();
        panelPrincipal.add(panelEnTete, BorderLayout.NORTH);
        
        // Table des étudiants
        String[] colonnes = {"Nom", "Prénom", "Genre", "Email", "Groupe", "Redoublant", "Anglophone", "Covoiturage"};
        modeleTable = new DefaultTableModel(colonnes, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return false;
            }
        };
        
        tableEtudiants = new JTable(modeleTable);
        tableEtudiants.setFont(new Font("Arial", Font.PLAIN, 13));
        tableEtudiants.setRowHeight(28);
        tableEtudiants.getTableHeader().setFont(new Font("Arial", Font.BOLD, 13));
        
        JScrollPane scrollPane = new JScrollPane(tableEtudiants);
        panelPrincipal.add(scrollPane, BorderLayout.CENTER);
        
        // Boutons
        JPanel panelBoutons = creerPanelBoutons();
        panelPrincipal.add(panelBoutons, BorderLayout.SOUTH);
        
        add(panelPrincipal);
    }
    
    private JPanel creerEnTete() {
        JPanel panel = new JPanel();
        panel.setLayout(new BoxLayout(panel, BoxLayout.Y_AXIS));
        panel.setBackground(Color.WHITE);
        panel.setBorder(BorderFactory.createEmptyBorder(0, 0, 20, 0));
        
        JLabel titre = new JLabel(promotion.getLibelle());
        titre.setFont(new Font("Arial", Font.BOLD, 24));
        titre.setForeground(new Color(44, 62, 80));
        titre.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        labelStatistiques = new JLabel("Chargement...");
        labelStatistiques.setFont(new Font("Arial", Font.PLAIN, 14));
        labelStatistiques.setForeground(new Color(127, 140, 141));
        labelStatistiques.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        panel.add(titre);
        panel.add(javax.swing.Box.createVerticalStrut(10));
        panel.add(labelStatistiques);
        
        return panel;
    }
    
    private JPanel creerPanelBoutons() {
        JPanel panel = new JPanel(new java.awt.FlowLayout(java.awt.FlowLayout.RIGHT, 10, 0));
        panel.setBackground(Color.WHITE);
        panel.setBorder(BorderFactory.createEmptyBorder(20, 0, 0, 0));
        
        JButton boutonRetour = new JButton("Retour");
        boutonRetour.setPreferredSize(new Dimension(120, 40));
        boutonRetour.setFont(new Font("Arial", Font.BOLD, 14));
        boutonRetour.setBackground(new Color(149, 165, 166));
        boutonRetour.setForeground(Color.WHITE);
        boutonRetour.setFocusPainted(false);
        boutonRetour.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                retour();
            }
        });
        
        panel.add(boutonRetour);
        
        return panel;
    }
    
    private void chargerEtudiants() {
        modeleTable.setRowCount(0);
        
        System.out.println("=== DEBUG VUE DETAILS ===");
        System.out.println("Chargement des étudiants pour: " + promotion.getId());
        
        List<Etudiant> etudiants = controleurPromotion.getEtudiants(promotion.getId());
        
        System.out.println("étudiants reçus dans la vue: " + etudiants.size());
        
        for (Etudiant etu : etudiants) {
            Object[] ligne = {
                etu.getNom(),
                etu.getPrenom(),
                etu.getGenre(),
                etu.getEmail(),
                etu.getNomGroupe() != null ? etu.getNomGroupe() : "-",
                etu.isEstRedoublant() ? "Oui" : "Non",
                etu.isEstAnglophone() ? "Oui" : "Non",
                etu.getIdCovoiturage() > 0 ? "Oui (#" + etu.getIdCovoiturage() + ")" : "Non"
            };
            modeleTable.addRow(ligne);
        }
        
        // Mettre é©Âƒé‚Â  jour les statistiques
        StatistiquesPromotion stats = controleurPromotion.getStatistiques(promotion.getId());
        labelStatistiques.setText(stats.toString());
    }
    
    private void retour() {
        new VuePromotions().setVisible(true);
        this.dispose();
    }
}
