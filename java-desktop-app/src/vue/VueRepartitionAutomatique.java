package vue;

import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.util.List;
import javax.swing.*;
import javax.swing.table.*;

import algo.*;
import controleur.*;
import modele.*;
import utils.Config;

/**
 * Vue pour la répartition automatique avec les algorithmes
 */
public class VueRepartitionAutomatique extends JFrame {
    
    private Promotion promotion;
    private ControleurPromotion controleurPromotion;
    private ControleurGroupe controleurGroupe;
    
    private List<Etudiant> etudiants;
    private List<Groupe> groupesGeneres;
    
    // Composants de configuration
    private JSpinner spinnerTailleMin;
    private JSpinner spinnerTailleMax;
    private JSpinner spinnerTailleCible;
    private JComboBox<String> comboAlgorithme;
    private JCheckBox checkMixite;
    private JCheckBox checkCovoiturage;
    private JCheckBox checkRedoublants;
    private JCheckBox checkAnglais;
    
    // Résultats
    private JTable tableResultats;
    private DefaultTableModel modeleTableResultats;
    private JLabel lblStatistiques;
    
    public VueRepartitionAutomatique(Promotion promotion) {
        this.promotion = promotion;
        this.controleurPromotion = new ControleurPromotion();
        this.controleurGroupe = new ControleurGroupe();
        
        initialiserInterface();
        chargerEtudiants();
    }
    
    private void initialiserInterface() {
        setTitle(Config.APP_TITLE + " - Répartition Automatique");
        setSize(Config.WINDOW_WIDTH, Config.WINDOW_HEIGHT);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        
        JPanel panelPrincipal = new JPanel(new BorderLayout(10, 10));
        panelPrincipal.setBackground(Color.WHITE);
        panelPrincipal.setBorder(BorderFactory.createEmptyBorder(20, 20, 20, 20));
        
        // En-tête
        JPanel panelEnTete = creerEnTete();
        panelPrincipal.add(panelEnTete, BorderLayout.NORTH);
        
        // Split : Configuration | Résultats
        JSplitPane splitPane = new JSplitPane(JSplitPane.HORIZONTAL_SPLIT);
        splitPane.setResizeWeight(0.35);
        
        JPanel panelConfiguration = creerPanelConfiguration();
        splitPane.setLeftComponent(panelConfiguration);
        
        JPanel panelResultats = creerPanelResultats();
        splitPane.setRightComponent(panelResultats);
        
        panelPrincipal.add(splitPane, BorderLayout.CENTER);
        
        // Boutons
        JPanel panelBoutons = creerPanelBoutons();
        panelPrincipal.add(panelBoutons, BorderLayout.SOUTH);
        
        add(panelPrincipal);
    }
    
    private JPanel creerEnTete() {
        JPanel panel = new JPanel(new BorderLayout());
        panel.setBackground(Color.WHITE);
        panel.setBorder(BorderFactory.createEmptyBorder(0, 0, 20, 0));
        
        JLabel titre = new JLabel("Répartition Automatique - " + promotion.getLibelle());
        titre.setFont(new Font("Arial", Font.BOLD, 24));
        titre.setForeground(new Color(28, 53, 94));
        
        panel.add(titre, BorderLayout.WEST);
        
        return panel;
    }
    
    private JPanel creerPanelConfiguration() {
        JPanel panel = new JPanel(new BorderLayout(10, 10));
        panel.setBackground(Color.WHITE);
        panel.setBorder(BorderFactory.createTitledBorder("Configuration des contraintes et algorithmes"));
        
        JPanel panelForm = new JPanel();
        panelForm.setLayout(new BoxLayout(panelForm, BoxLayout.Y_AXIS));
        panelForm.setBackground(Color.WHITE);
        
        // Tailles des groupes
        panelForm.add(creerSection("Taille des groupes"));
        panelForm.add(creerChamp("Taille minimum:", spinnerTailleMin = new JSpinner(new SpinnerNumberModel(17, 1, 30, 1))));
        panelForm.add(creerChamp("Taille maximum:", spinnerTailleMax = new JSpinner(new SpinnerNumberModel(20, 1, 30, 1))));
        panelForm.add(creerChamp("Taille cible:", spinnerTailleCible = new JSpinner(new SpinnerNumberModel(18, 1, 30, 1))));
        
        panelForm.add(Box.createVerticalStrut(20));
        
        // Algorithme
        panelForm.add(creerSection("Algorithme"));
        String[] algorithmes = {
            "Glouton - Redoublants équilibrés",
            "Glouton - Score Local",
            "Force Brute - Backtracking (lent)"
        };
        comboAlgorithme = new JComboBox<>(algorithmes);
        panelForm.add(creerChamp("Sélectionner:", comboAlgorithme));
        
        panelForm.add(Box.createVerticalStrut(20));
        
        // Contraintes optionnelles
        panelForm.add(creerSection("Contraintes optionnelles"));
        checkMixite = new JCheckBox("Équilibrer la mixité (filles/garçons)");
        checkCovoiturage = new JCheckBox("Respecter les groupes de covoiturage");
        checkRedoublants = new JCheckBox("Regrouper les redoublants (S3)");
        checkAnglais = new JCheckBox("Regrouper les anglophones (S3)");
        
        panelForm.add(checkMixite);
        panelForm.add(checkCovoiturage);
        panelForm.add(checkRedoublants);
        panelForm.add(checkAnglais);
        
        panelForm.add(Box.createVerticalStrut(20));
        
        // Bouton d'exécution
        JButton btnExecuter = new JButton("Générer les groupes");
        btnExecuter.setBackground(new Color(52, 152, 219));
        btnExecuter.setForeground(Color.WHITE);
        btnExecuter.setFont(new Font("Arial", Font.BOLD, 14));
        btnExecuter.setMaximumSize(new Dimension(250, 40));
        btnExecuter.setAlignmentX(Component.CENTER_ALIGNMENT);
        btnExecuter.setFocusPainted(false);
        btnExecuter.addActionListener(e -> executerAlgorithme());
        
        panelForm.add(btnExecuter);
        
        JScrollPane scrollPane = new JScrollPane(panelForm);
        scrollPane.setBorder(null);
        panel.add(scrollPane, BorderLayout.CENTER);
        
        return panel;
    }
    
    private JPanel creerSection(String titre) {
        JPanel panel = new JPanel(new FlowLayout(FlowLayout.LEFT));
        panel.setBackground(Color.WHITE);
        panel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 30));
        
        JLabel label = new JLabel(titre);
        label.setFont(new Font("Arial", Font.BOLD, 14));
        label.setForeground(new Color(44, 62, 80));
        panel.add(label);
        
        return panel;
    }
    
    private JPanel creerChamp(String label, JComponent composant) {
        JPanel panel = new JPanel(new BorderLayout(10, 5));
        panel.setBackground(Color.WHITE);
        panel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 35));
        
        JLabel lbl = new JLabel(label);
        lbl.setPreferredSize(new Dimension(120, 25));
        panel.add(lbl, BorderLayout.WEST);
        panel.add(composant, BorderLayout.CENTER);
        
        return panel;
    }
    
    private JPanel creerPanelResultats() {
        JPanel panel = new JPanel(new BorderLayout(10, 10));
        panel.setBackground(Color.WHITE);
        panel.setBorder(BorderFactory.createTitledBorder("Résultats"));
        
        // Statistiques en haut
        lblStatistiques = new JLabel("<html><i>Aucun résultat - Configurez et exécutez l'algorithme</i></html>");
        lblStatistiques.setFont(new Font("Arial", Font.PLAIN, 12));
        lblStatistiques.setBorder(BorderFactory.createEmptyBorder(5, 5, 5, 5));
        panel.add(lblStatistiques, BorderLayout.NORTH);
        
        // Table des groupes
        String[] colonnes = {"Groupe", "Effectif", "Filles", "Garçons", "Redoublants", "Anglophones"};
        modeleTableResultats = new DefaultTableModel(colonnes, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return false;
            }
        };
        
        tableResultats = new JTable(modeleTableResultats);
        tableResultats.setRowHeight(25);
        tableResultats.getTableHeader().setFont(new Font("Arial", Font.BOLD, 12));
        
        JScrollPane scrollPane = new JScrollPane(tableResultats);
        panel.add(scrollPane, BorderLayout.CENTER);
        
        return panel;
    }
    
    private JPanel creerPanelBoutons() {
        JPanel panel = new JPanel(new BorderLayout());
        panel.setBackground(Color.WHITE);
        panel.setBorder(BorderFactory.createEmptyBorder(10, 0, 0, 0));
        
        JPanel panelGauche = new JPanel(new FlowLayout(FlowLayout.LEFT));
        panelGauche.setBackground(Color.WHITE);
        
        JButton btnRetour = new JButton("Retour");
        btnRetour.setPreferredSize(new Dimension(120, 40));
        btnRetour.setBackground(new Color(149, 165, 166));
        btnRetour.setForeground(Color.WHITE);
        btnRetour.setFont(new Font("Arial", Font.BOLD, 14));
        btnRetour.setFocusPainted(false);
        btnRetour.addActionListener(e -> retour());
        
        panelGauche.add(btnRetour);
        
        JPanel panelDroite = new JPanel(new FlowLayout(FlowLayout.RIGHT));
        panelDroite.setBackground(Color.WHITE);
        
        JButton btnSauvegarder = new JButton("Sauvegarder cette répartition");
        btnSauvegarder.setPreferredSize(new Dimension(220, 40));
        btnSauvegarder.setBackground(new Color(39, 174, 96));
        btnSauvegarder.setForeground(Color.WHITE);
        btnSauvegarder.setFont(new Font("Arial", Font.BOLD, 14));
        btnSauvegarder.setFocusPainted(false);
        btnSauvegarder.addActionListener(e -> sauvegarderRepartition());
        
        panelDroite.add(btnSauvegarder);
        
        panel.add(panelGauche, BorderLayout.WEST);
        panel.add(panelDroite, BorderLayout.EAST);
        
        return panel;
    }
    
    private void chargerEtudiants() {
        try {
            etudiants = controleurPromotion.getEtudiants(promotion.getId());
        } catch (Exception e) {
            JOptionPane.showMessageDialog(this,
                "Erreur lors du chargement des étudiants : " + e.getMessage(),
                "Erreur",
                JOptionPane.ERROR_MESSAGE);
        }
    }
    
    private void executerAlgorithme() {
        if (etudiants == null || etudiants.isEmpty()) {
            JOptionPane.showMessageDialog(this,
                "Aucun étudiant à répartir",
                "Erreur",
                JOptionPane.ERROR_MESSAGE);
            return;
        }
        
        // Validation des contraintes incompatibles
        if (checkRedoublants.isSelected() && checkAnglais.isSelected()) {
            JOptionPane.showMessageDialog(this,
                "Impossible de regrouper les redoublants ET les anglophones en même temps",
                "Contraintes incompatibles",
                JOptionPane.WARNING_MESSAGE);
            return;
        }
        
        try {
            // Créer les contraintes
            ContraintesGroupesS3 contraintes = new ContraintesGroupesS3(
                (int) spinnerTailleMin.getValue(),
                (int) spinnerTailleMax.getValue(),
                (int) spinnerTailleCible.getValue(),
                5.0 // poids redoublants
            );
            
            // Sélectionner l'algorithme
            GroupeAlgoS3 algo;
            String algoSelectionne = (String) comboAlgorithme.getSelectedItem();
            
            if (algoSelectionne.contains("Redoublants")) {
                algo = new GloutonRedoublantsEquilibre();
            } else if (algoSelectionne.contains("Score Local")) {
                algo = new GloutonScoreLocalS3();
            } else {
                algo = new ForceBruteBacktrackingS3(100000);
            }
            
            // Exé©Âƒé‚Â©cuter l'algorithme
            setCursor(Cursor.getPredefinedCursor(Cursor.WAIT_CURSOR));
            groupesGeneres = algo.generer(etudiants, contraintes);
            setCursor(Cursor.getDefaultCursor());
            
            // Afficher les ré©Âƒé‚Â©sultats
            afficherResultats();
            
            JOptionPane.showMessageDialog(this,
                "Répartition générée avec succès !",
                "Succès",
                JOptionPane.INFORMATION_MESSAGE);
            
        } catch (Exception e) {
            setCursor(Cursor.getDefaultCursor());
            JOptionPane.showMessageDialog(this,
                "Erreur lors de l'exécution : " + e.getMessage(),
                "Erreur",
                JOptionPane.ERROR_MESSAGE);
            e.printStackTrace();
        }
    }
    
    private void afficherResultats() {
        modeleTableResultats.setRowCount(0);
        
        if (groupesGeneres == null || groupesGeneres.isEmpty()) {
            return;
        }
        
        int totalEtudiants = 0;
        int totalFilles = 0;
        int totalGarcons = 0;
        int totalRedoublants = 0;
        int totalAnglophones = 0;
        
        for (int i = 0; i < groupesGeneres.size(); i++) {
            Groupe g = groupesGeneres.get(i);
            
            int nbFilles = 0, nbGarcons = 0, nbRed = 0, nbAnglo = 0;
            
            for (Etudiant e : g.getEtudiants()) {
                if (e.isFille()) nbFilles++;
                else nbGarcons++;
                if (e.isRedoublant()) nbRed++;
                if (e.aOptionAnglais()) nbAnglo++;
            }
            
            modeleTableResultats.addRow(new Object[]{
                "Groupe " + (i + 1),
                g.getEffectif(),
                nbFilles,
                nbGarcons,
                nbRed,
                nbAnglo
            });
            
            totalEtudiants += g.getEffectif();
            totalFilles += nbFilles;
            totalGarcons += nbGarcons;
            totalRedoublants += nbRed;
            totalAnglophones += nbAnglo;
        }
        
        // Mise é©Âƒé‚Â  jour des statistiques
        double pourcentageFilles = totalEtudiants > 0 ? (totalFilles * 100.0 / totalEtudiants) : 0;
        
        lblStatistiques.setText(String.format(
            "<html><b>Statistiques globales :</b> %d groupes | %d étudiants | " +
            "%d filles (%.1f%%) | %d garçons | %d redoublants | %d anglophones</html>",
            groupesGeneres.size(), totalEtudiants, totalFilles, pourcentageFilles,
            totalGarcons, totalRedoublants, totalAnglophones
        ));
    }
    
    private void sauvegarderRepartition() {
        if (groupesGeneres == null || groupesGeneres.isEmpty()) {
            JOptionPane.showMessageDialog(this,
                "Aucune répartition à sauvegarder. Générez d'abord les groupes.",
                "Erreur",
                JOptionPane.WARNING_MESSAGE);
            return;
        }
        
        int confirm = JOptionPane.showConfirmDialog(this,
            "Voulez-vous sauvegarder cette répartition dans la base de données ?",
            "Confirmation",
            JOptionPane.YES_NO_OPTION);
        
        if (confirm != JOptionPane.YES_OPTION) {
            return;
        }
        
        try {
            // Cré©Âƒé‚Â©er les affectations
            List<Map<String, Object>> affectations = new ArrayList<>();
            
            for (int i = 0; i < groupesGeneres.size(); i++) {
                Groupe g = groupesGeneres.get(i);
                int idGroupe = i + 1; // ID temporaire
                
                for (Etudiant e : g.getEtudiants()) {
                    Map<String, Object> affectation = new HashMap<>();
                    affectation.put("idEtudiant", e.getIdEtudiant());
                    affectation.put("idGroupe", idGroupe);
                    affectations.add(affectation);
                }
            }
            
            // Sauvegarder via l'API
            boolean success = controleurGroupe.saveAffectations(affectations);
            
            if (success) {
                JOptionPane.showMessageDialog(this,
                    "Répartition sauvegardée avec succès !",
                    "Succès",
                    JOptionPane.INFORMATION_MESSAGE);
            } else {
                JOptionPane.showMessageDialog(this,
                    "Erreur lors de la sauvegarde",
                    "Erreur",
                    JOptionPane.ERROR_MESSAGE);
            }
            
        } catch (Exception e) {
            JOptionPane.showMessageDialog(this,
                "Erreur : " + e.getMessage(),
                "Erreur",
                JOptionPane.ERROR_MESSAGE);
            e.printStackTrace();
        }
    }
    
    private void retour() {
        new VueConstitutionGroupes().setVisible(true);
        this.dispose();
    }
}
