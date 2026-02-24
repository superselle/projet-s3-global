package vue;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.Font;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.util.List;

import javax.swing.BorderFactory;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTable;
import javax.swing.table.DefaultTableModel;

import controleur.ControleurPromotion;
import modele.Promotion;
import utils.Config;
import utils.RoundedButton;
import utils.RoundedPanel;

/**
 * Vue pour consulter les promotions
 */
public class VuePromotions extends JFrame {
    
    private ControleurPromotion controleurPromotion;
    private JTable tablePromotions;
    private DefaultTableModel modeleTable;
    private JButton boutonRetour;
    private JButton boutonVoirDetails;
    
    public VuePromotions() {
        this.controleurPromotion = new ControleurPromotion();
        initialiserInterface();
        chargerPromotions();
    }
    
    private void initialiserInterface() {
        setTitle(Config.APP_TITLE + " - Promotions");
        setSize(Config.WINDOW_WIDTH, Config.WINDOW_HEIGHT);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        
        JPanel panelPrincipal = new JPanel(new BorderLayout(20, 20));
        panelPrincipal.setBackground(new Color(245, 247, 250));
        panelPrincipal.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));
        
        // En-té©Âƒé‚Âªte
        JPanel panelEnTete = creerEnTete();
        panelPrincipal.add(panelEnTete, BorderLayout.NORTH);
        
        // Table des promotions
        String[] colonnes = {"Parcours", "Semestre", "Année", "Nb étudiants", "Nb Groupes"};
        modeleTable = new DefaultTableModel(colonnes, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return false;
            }
        };
        
        tablePromotions = new JTable(modeleTable);
        tablePromotions.setFont(new Font("Arial", Font.PLAIN, 14));
        tablePromotions.setRowHeight(30);
        tablePromotions.getTableHeader().setFont(new Font("Arial", Font.BOLD, 14));
        tablePromotions.setSelectionMode(javax.swing.ListSelectionModel.SINGLE_SELECTION);
        
        JScrollPane scrollPane = new JScrollPane(tablePromotions);
        panelPrincipal.add(scrollPane, BorderLayout.CENTER);
        
        // Pied de page avec boutons
        JPanel panelBoutons = creerPanelBoutons();
        panelPrincipal.add(panelBoutons, BorderLayout.SOUTH);
        
        add(panelPrincipal);
    }
    
    private JPanel creerEnTete() {
        RoundedPanel panel = new RoundedPanel(15);
        panel.setLayout(new BorderLayout());
        panel.setBackgroundColor(Color.WHITE);
        panel.setDrawShadow(true);
        panel.setBorder(BorderFactory.createEmptyBorder(25, 30, 25, 30));
        
        JLabel titre = new JLabel("Liste des Promotions");
        titre.setFont(new Font("Segoe UI", Font.BOLD, 28));
        titre.setForeground(new Color(28, 53, 94));
        
        panel.add(titre, BorderLayout.WEST);
        
        return panel;
    }
    
    private JPanel creerPanelBoutons() {
        JPanel panel = new JPanel(new BorderLayout(10, 10));
        panel.setBackground(Color.WHITE);
        panel.setBorder(BorderFactory.createEmptyBorder(20, 0, 0, 0));
        
        // Boutons à gauche
        JPanel panelGauche = new JPanel(new java.awt.FlowLayout(java.awt.FlowLayout.LEFT, 10, 0));
        panelGauche.setBackground(Color.WHITE);
        
        boutonVoirDetails = new RoundedButton("Voir les étudiants", new Color(52, 152, 219));
        boutonVoirDetails.setPreferredSize(new Dimension(180, 45));
        boutonVoirDetails.setFont(new Font("Segoe UI", Font.BOLD, 14));
        boutonVoirDetails.setForeground(Color.WHITE);
        boutonVoirDetails.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                voirDetails();
            }
        });
        
        panelGauche.add(boutonVoirDetails);
        
        // Boutons à droite
        JPanel panelDroite = new JPanel(new java.awt.FlowLayout(java.awt.FlowLayout.RIGHT, 10, 0));
        panelDroite.setBackground(Color.WHITE);
        
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
        
        panelDroite.add(boutonRetour);
        
        panel.add(panelDroite, BorderLayout.WEST);
        panel.add(panelGauche, BorderLayout.EAST);
        
        return panel;
    }
    
    private void chargerPromotions() {
        modeleTable.setRowCount(0);
        
        try {
            List<Promotion> promotions = controleurPromotion.getPromotions();
            
            System.out.println("=== DEBUG PROMOTIONS ===");
            System.out.println("Nombre de promotions: " + promotions.size());
            
            for (Promotion promo : promotions) {
                System.out.println("Promotion: " + promo.getLibelle());
                Object[] ligne = {
                    promo.getNomParcours(),
                    "S" + promo.getSemestre(),
                    promo.getAnneeScolaire(),
                    promo.getNbEtudiants(),
                    promo.getNbGroupes()
                };
                modeleTable.addRow(ligne);
            }
        } catch (Exception e) {
            System.err.println("Erreur lors du chargement des promotions: " + e.getMessage());
            e.printStackTrace();
        }
    }
    
    private void voirDetails() {
        int selectedRow = tablePromotions.getSelectedRow();
        if (selectedRow == -1) {
            JOptionPane.showMessageDialog(
                this,
                "Veuillez sélectionner une promotion.",
                "Aucune sélection",
                JOptionPane.WARNING_MESSAGE
            );
            return;
        }
        
        // Récupérer la promotion sélectionnée
        List<Promotion> promotions = controleurPromotion.getPromotions();
        Promotion promo = promotions.get(selectedRow);
        
        // Ouvrir la vue des détails
        new VueDetailsPromotion(promo).setVisible(true);
        this.dispose();
    }
    
    private void retourMenu() {
        new VueDashboard().setVisible(true);
        this.dispose();
    }
}
