package vue;

import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.util.List;
import javax.swing.*;
import javax.swing.table.*;

import controleur.ControleurPromotion;
import modele.*;
import utils.Config;
import utils.RoundedButton;
import utils.RoundedPanel;

/**
 * Vue pour la gestion des étudiants (CRUD)
 */
public class VueGestionEtudiants extends JFrame {
    
    private ControleurPromotion controleurPromotion;
    private List<Etudiant> etudiants;
    private Promotion promotionSelectionnee;
    
    private JComboBox<Promotion> comboPromotions;
    private JTable tableEtudiants;
    private DefaultTableModel modeleTable;
    private JButton btnAjouter;
    
    public VueGestionEtudiants() {
        this.controleurPromotion = new ControleurPromotion();
        initialiserInterface();
        chargerPromotions();
    }
    
    private void initialiserInterface() {
        setTitle(Config.APP_TITLE + " - Gestion des étudiants");
        setSize(Config.WINDOW_WIDTH, Config.WINDOW_HEIGHT);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        
        JPanel panelPrincipal = new JPanel(new BorderLayout(15, 15));
        panelPrincipal.setBackground(new Color(245, 247, 250));
        panelPrincipal.setBorder(BorderFactory.createEmptyBorder(30, 30, 30, 30));
        
        // En-té©Âƒé‚Âªte avec titre et sélection de promotion
        JPanel panelEnTete = creerEnTete();
        panelPrincipal.add(panelEnTete, BorderLayout.NORTH);
        
        // Bouton d'ajout
        JPanel panelBoutons = new JPanel(new FlowLayout(FlowLayout.LEFT));
        panelBoutons.setBackground(new Color(245, 247, 250));
        
        btnAjouter = new RoundedButton("+ Ajouter un étudiant", new Color(39, 174, 96));
        btnAjouter.setPreferredSize(new Dimension(220, 45));
        btnAjouter.setForeground(Color.WHITE);
        btnAjouter.setFont(new Font("Segoe UI", Font.BOLD, 14));
        btnAjouter.addActionListener(e -> ajouterEtudiant());
        
        panelBoutons.add(btnAjouter);
        panelPrincipal.add(panelBoutons, BorderLayout.CENTER);
        
        // Table des étudiants
        JPanel panelTable = creerPanelTable();
        panelPrincipal.add(panelTable, BorderLayout.SOUTH);
        
        // Bouton retour
        JPanel panelRetour = new JPanel(new FlowLayout(FlowLayout.LEFT));
        panelRetour.setBackground(new Color(245, 247, 250));
        
        RoundedButton btnRetour = new RoundedButton("Retour au menu", new Color(149, 165, 166));
        btnRetour.setForeground(Color.WHITE);
        btnRetour.setFont(new Font("Segoe UI", Font.BOLD, 14));
        btnRetour.setPreferredSize(new Dimension(160, 45));
        btnRetour.addActionListener(e -> retourMenu());
        
        panelRetour.add(btnRetour);
        
        // Ajouter le panneau table et le panneau retour
        JPanel panelSud = new JPanel(new BorderLayout());
        panelSud.setBackground(Color.WHITE);
        panelSud.add(panelTable, BorderLayout.CENTER);
        panelSud.add(panelRetour, BorderLayout.SOUTH);
        
        panelPrincipal.remove(panelTable); // Retirer l'ancien
        panelPrincipal.add(panelSud, BorderLayout.SOUTH);
        
        add(panelPrincipal);
    }
    
    private JPanel creerEnTete() {
        RoundedPanel panel = new RoundedPanel(15);
        panel.setLayout(new BorderLayout(10, 10));
        panel.setBackgroundColor(Color.WHITE);
        panel.setDrawShadow(true);
        panel.setBorder(BorderFactory.createEmptyBorder(25, 30, 25, 30));
        
        JLabel titre = new JLabel("Gestion des étudiants");
        titre.setFont(new Font("Segoe UI", Font.BOLD, 28));
        titre.setForeground(new Color(28, 53, 94));
        
        // Sélecteur de promotion
        JPanel panelSelection = new JPanel(new FlowLayout(FlowLayout.LEFT));
        panelSelection.setBackground(Color.WHITE);
        
        JLabel lblPromotion = new JLabel("Promotion : ");
        lblPromotion.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        
        comboPromotions = new JComboBox<>();
        comboPromotions.setPreferredSize(new Dimension(300, 30));
        comboPromotions.addActionListener(e -> chargerEtudiants());
        
        panelSelection.add(lblPromotion);
        panelSelection.add(comboPromotions);
        
        JPanel panelTitreEtSelection = new JPanel(new BorderLayout());
        panelTitreEtSelection.setBackground(Color.WHITE);
        panelTitreEtSelection.add(titre, BorderLayout.NORTH);
        panelTitreEtSelection.add(panelSelection, BorderLayout.SOUTH);
        
        panel.add(panelTitreEtSelection, BorderLayout.WEST);
        
        return panel;
    }
    
    private JPanel creerPanelTable() {
        JPanel panel = new JPanel(new BorderLayout());
        panel.setBackground(Color.WHITE);
        
        // Créer le modé©Âƒé‚Â¨le de table
        String[] colonnes = {"Nom", "Prénom", "Email", "Groupe", "Actions"};
        modeleTable = new DefaultTableModel(colonnes, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return column == 4; // Seule la colonne Actions est éditable
            }
        };
        
        tableEtudiants = new JTable(modeleTable);
        tableEtudiants.setRowHeight(50);
        tableEtudiants.setFont(new Font("Arial", Font.PLAIN, 13));
        tableEtudiants.getTableHeader().setFont(new Font("Arial", Font.BOLD, 14));
        tableEtudiants.getTableHeader().setBackground(new Color(245, 245, 245));
        tableEtudiants.setGridColor(new Color(230, 230, 230));
        
        // Renderer personnalisé pour la colonne Groupe
        tableEtudiants.getColumnModel().getColumn(3).setCellRenderer(new GroupeBadgeRenderer());
        
        // Renderer personnalisé pour la colonne Actions
        tableEtudiants.getColumnModel().getColumn(4).setCellRenderer(new ButtonRenderer());
        tableEtudiants.getColumnModel().getColumn(4).setCellEditor(new ButtonEditor(new JCheckBox()));
        
        // Largeurs des colonnes
        tableEtudiants.getColumnModel().getColumn(0).setPreferredWidth(150);
        tableEtudiants.getColumnModel().getColumn(1).setPreferredWidth(150);
        tableEtudiants.getColumnModel().getColumn(2).setPreferredWidth(250);
        tableEtudiants.getColumnModel().getColumn(3).setPreferredWidth(100);
        tableEtudiants.getColumnModel().getColumn(4).setPreferredWidth(120);
        
        JScrollPane scrollPane = new JScrollPane(tableEtudiants);
        scrollPane.setPreferredSize(new Dimension(0, 400));
        panel.add(scrollPane, BorderLayout.CENTER);
        
        return panel;
    }
    
    private void chargerPromotions() {
        try {
            List<Promotion> promotions = controleurPromotion.getPromotions();
            comboPromotions.removeAllItems();
            for (Promotion promo : promotions) {
                comboPromotions.addItem(promo);
            }
        } catch (Exception e) {
            JOptionPane.showMessageDialog(this,
                "Erreur lors du chargement des promotions : " + e.getMessage(),
                "Erreur",
                JOptionPane.ERROR_MESSAGE);
        }
    }
    
    private void chargerEtudiants() {
        promotionSelectionnee = (Promotion) comboPromotions.getSelectedItem();
        if (promotionSelectionnee == null) {
            return;
        }
        
        try {
            etudiants = controleurPromotion.getEtudiants(promotionSelectionnee.getId());
            rafraichirTable();
        } catch (Exception e) {
            JOptionPane.showMessageDialog(this,
                "Erreur lors du chargement des étudiants : " + e.getMessage(),
                "Erreur",
                JOptionPane.ERROR_MESSAGE);
        }
    }
    
    private void rafraichirTable() {
        modeleTable.setRowCount(0);
        
        if (etudiants == null) {
            return;
        }
        
        for (Etudiant etu : etudiants) {
            String groupe = (etu.getNomGroupe() != null && !etu.getNomGroupe().isEmpty()) 
                ? etu.getNomGroupe() : "";
            
            modeleTable.addRow(new Object[]{
                etu.getNom(),
                etu.getPrenom(),
                etu.getEmail(),
                groupe,
                etu // On passe l'objet complet pour les actions
            });
        }
    }
    
    private void ajouterEtudiant() {
        if (promotionSelectionnee == null) {
            JOptionPane.showMessageDialog(this,
                "Veuillez sélectionner une promotion",
                "Aucune promotion",
                JOptionPane.WARNING_MESSAGE);
            return;
        }
        
        // Ouvrir le dialogue d'ajout
        DialogueAjoutEtudiant dialogue = new DialogueAjoutEtudiant((Frame) SwingUtilities.getWindowAncestor(this));
        dialogue.setVisible(true);
        
        // Si l'utilisateur a confirmé
        if (dialogue.estConfirme()) {
            try {
                // Appeler l'API pour ajouter l'étudiant
                api.ApiClient apiClient = api.ApiClient.getInstance();
                modele.Etudiant nouvelEtudiant = apiClient.ajouterEtudiant(
                    dialogue.getNom(),
                    dialogue.getPrenom(),
                    dialogue.getEmail(),
                    dialogue.getLogin(),
                    dialogue.getMotDePasse(),
                    dialogue.getGenre(),
                    dialogue.getTypeBac(),
                    dialogue.getMentionBac(),
                    dialogue.estRedoublant(),
                    dialogue.estAnglophone(),
                    promotionSelectionnee.getId()
                );
                
                // Rafraé©Âƒé‚Â®chir la liste
                chargerEtudiants();
                
                JOptionPane.showMessageDialog(this,
                    "L'étudiant " + nouvelEtudiant.getNomComplet() + " a été ajouté avec succès !",
                    "Succès",
                    JOptionPane.INFORMATION_MESSAGE);
                    
            } catch (Exception e) {
                String errorMsg = e.getMessage();
                if (errorMsg == null || errorMsg.isEmpty()) {
                    errorMsg = "Erreur inconnue";
                }
                
                // Afficher un message plus détaillé
                JOptionPane.showMessageDialog(this,
                    "Erreur lors de l'ajout de l'étudiant :\n" + errorMsg + "\n\n" +
                    "Vérifiez que :\n" +
                    "- Le serveur est accessible\n" +
                    "- Les endpoints API sont déployés\n" +
                    "- Les données sont valides",
                    "Erreur",
                    JOptionPane.ERROR_MESSAGE);
                e.printStackTrace();
            }
        }
    }
    
    private void modifierEtudiant(Etudiant etudiant) {
        // Ouvrir le dialogue de modification avec les données existantes
        DialogueAjoutEtudiant dialogue = new DialogueAjoutEtudiant(
            (Frame) SwingUtilities.getWindowAncestor(this), 
            etudiant
        );
        dialogue.setVisible(true);
        
        // Si l'utilisateur a confirmé
        if (dialogue.estConfirme()) {
            try {
                // Appeler l'API pour modifier l'étudiant
                api.ApiClient apiClient = api.ApiClient.getInstance();
                apiClient.modifierEtudiant(
                    etudiant.getIdEtudiant(),
                    dialogue.getNom(),
                    dialogue.getPrenom(),
                    dialogue.getEmail(),
                    dialogue.getLogin(),
                    dialogue.getMotDePasse(),
                    dialogue.getGenre(),
                    dialogue.getTypeBac(),
                    dialogue.getMentionBac(),
                    dialogue.estRedoublant(),
                    dialogue.estAnglophone()
                );
                
                // Rafraîchir la liste
                chargerEtudiants();
                
                JOptionPane.showMessageDialog(this,
                    "L'étudiant " + dialogue.getNom() + " " + dialogue.getPrenom() + " a été modifié avec succès !",
                    "Succès",
                    JOptionPane.INFORMATION_MESSAGE);
                    
            } catch (Exception e) {
                String errorMsg = e.getMessage();
                if (errorMsg == null || errorMsg.isEmpty()) {
                    errorMsg = "Erreur inconnue";
                }
                
                JOptionPane.showMessageDialog(this,
                    "Erreur lors de la modification de l'étudiant :\n" + errorMsg + "\n\n" +
                    "Vérifiez que :\n" +
                    "- Le serveur est accessible\n" +
                    "- L'endpoint 'modifier_etudiant' est déployé\n" +
                    "- Les données sont valides",
                    "Erreur",
                    JOptionPane.ERROR_MESSAGE);
                e.printStackTrace();
            }
        }
    }
    
    private void supprimerEtudiant(Etudiant etudiant) {
        int confirm = JOptionPane.showConfirmDialog(this,
            "Voulez-vous vraiment supprimer " + etudiant.getNomComplet() + " ?\n" +
            "Cette action est irréversible.",
            "Confirmer la suppression",
            JOptionPane.YES_NO_OPTION,
            JOptionPane.WARNING_MESSAGE);
        
        if (confirm == JOptionPane.YES_OPTION) {
            try {
                // Appeler l'API pour supprimer l'étudiant
                api.ApiClient apiClient = api.ApiClient.getInstance();
                apiClient.supprimerEtudiant(etudiant.getIdEtudiant());
                
                // Rafraîchir la liste
                chargerEtudiants();
                
                JOptionPane.showMessageDialog(this,
                    "L'étudiant " + etudiant.getNomComplet() + " a été supprimé avec succès !",
                    "Succès",
                    JOptionPane.INFORMATION_MESSAGE);
                    
            } catch (Exception e) {
                String errorMsg = e.getMessage();
                if (errorMsg == null || errorMsg.isEmpty()) {
                    errorMsg = "Erreur inconnue";
                }
                
                JOptionPane.showMessageDialog(this,
                    "Erreur lors de la suppression de l'étudiant :\n" + errorMsg + "\n\n" +
                    "Vérifiez que :\n" +
                    "- Le serveur est accessible\n" +
                    "- L'endpoint 'supprimer_etudiant' est déployé\n" +
                    "- L'étudiant existe toujours",
                    "Erreur",
                    JOptionPane.ERROR_MESSAGE);
                e.printStackTrace();
            }
        }
    }
    
    private void retourMenu() {
        new VueDashboard().setVisible(true);
        this.dispose();
    }
    
    // Renderer pour afficher le groupe dans un badge
    class GroupeBadgeRenderer extends DefaultTableCellRenderer {
        @Override
        public Component getTableCellRendererComponent(JTable table, Object value,
                boolean isSelected, boolean hasFocus, int row, int column) {
            
            String groupe = (String) value;
            
            if (groupe == null || groupe.isEmpty()) {
                JLabel label = new JLabel("");
                label.setOpaque(true);
                label.setBackground(isSelected ? table.getSelectionBackground() : table.getBackground());
                return label;
            }
            
            JLabel badge = new JLabel(groupe);
            badge.setOpaque(true);
            badge.setBackground(new Color(139, 28, 58)); // Bordeaux
            badge.setForeground(Color.WHITE);
            badge.setFont(new Font("Arial", Font.BOLD, 12));
            badge.setHorizontalAlignment(JLabel.CENTER);
            badge.setBorder(BorderFactory.createEmptyBorder(5, 15, 5, 15));
            
            JPanel panel = new JPanel(new FlowLayout(FlowLayout.CENTER));
            panel.setBackground(isSelected ? table.getSelectionBackground() : table.getBackground());
            panel.add(badge);
            
            return panel;
        }
    }
    
    // Renderer pour les boutons d'action
    class ButtonRenderer extends JPanel implements TableCellRenderer {
        private JButton btnModifier;
        private JButton btnSupprimer;
        
        public ButtonRenderer() {
            setLayout(new FlowLayout(FlowLayout.CENTER, 5, 5));
            setOpaque(true);
            
            btnModifier = new JButton("Modifier");
            btnModifier.setPreferredSize(new Dimension(40, 30));
            btnModifier.setBackground(new Color(241, 196, 15));
            btnModifier.setForeground(Color.WHITE);
            btnModifier.setFocusPainted(false);
            btnModifier.setBorderPainted(false);
            btnModifier.setToolTipText("Modifier");
            
            btnSupprimer = new JButton("Supprimer");
            btnSupprimer.setPreferredSize(new Dimension(40, 30));
            btnSupprimer.setBackground(new Color(231, 76, 60));
            btnSupprimer.setForeground(Color.WHITE);
            btnSupprimer.setFocusPainted(false);
            btnSupprimer.setBorderPainted(false);
            btnSupprimer.setToolTipText("Supprimer");
            
            add(btnModifier);
            add(btnSupprimer);
        }
        
        @Override
        public Component getTableCellRendererComponent(JTable table, Object value,
                boolean isSelected, boolean hasFocus, int row, int column) {
            setBackground(isSelected ? table.getSelectionBackground() : table.getBackground());
            return this;
        }
    }
    
    // Editor pour gérer les clics sur les boutons
    class ButtonEditor extends DefaultCellEditor {
        private JPanel panel;
        private JButton btnModifier;
        private JButton btnSupprimer;
        private Etudiant etudiantCourant;
        
        public ButtonEditor(JCheckBox checkBox) {
            super(checkBox);
            
            panel = new JPanel(new FlowLayout(FlowLayout.CENTER, 5, 5));
            
            btnModifier = new JButton("Modifier");
            btnModifier.setPreferredSize(new Dimension(40, 30));
            btnModifier.setBackground(new Color(241, 196, 15));
            btnModifier.setForeground(Color.WHITE);
            btnModifier.setFocusPainted(false);
            btnModifier.setBorderPainted(false);
            btnModifier.addActionListener(e -> {
                fireEditingStopped();
                modifierEtudiant(etudiantCourant);
            });
            
            btnSupprimer = new JButton("Supprimer");
            btnSupprimer.setPreferredSize(new Dimension(40, 30));
            btnSupprimer.setBackground(new Color(231, 76, 60));
            btnSupprimer.setForeground(Color.WHITE);
            btnSupprimer.setFocusPainted(false);
            btnSupprimer.setBorderPainted(false);
            btnSupprimer.addActionListener(e -> {
                fireEditingStopped();
                supprimerEtudiant(etudiantCourant);
            });
            
            panel.add(btnModifier);
            panel.add(btnSupprimer);
        }
        
        @Override
        public Component getTableCellEditorComponent(JTable table, Object value,
                boolean isSelected, int row, int column) {
            etudiantCourant = (Etudiant) value;
            panel.setBackground(table.getSelectionBackground());
            return panel;
        }
        
        @Override
        public Object getCellEditorValue() {
            return etudiantCourant;
        }
    }
}
