package vue;

import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.util.List;

import javax.swing.*;
import javax.swing.table.*;

import controleur.ControleurGroupe;
import controleur.ControleurPromotion;
import modele.*;
import utils.Config;

/**
 * Vue pour la répartition manuelle des étudiants dans les groupes
 */
public class VueRepartitionManuelleComplete extends JFrame {
    
    private Promotion promotion;
    private ControleurPromotion controleurPromotion;
    private ControleurGroupe controleurGroupe;
    
    private List<Etudiant> etudiants;
    private List<Groupe> groupes;
    
    private JTable tableEtudiants;
    private DefaultTableModel modeleTableEtudiants;
    private JPanel panelGroupes;
    
    public VueRepartitionManuelleComplete(Promotion promotion) {
        this.promotion = promotion;
        this.controleurPromotion = new ControleurPromotion();
        this.controleurGroupe = new ControleurGroupe();
        this.groupes = new ArrayList<>();
        
        initialiserInterface();
        chargerDonnees();
    }
    
    private void initialiserInterface() {
        setTitle(Config.APP_TITLE + " - Répartition Manuelle");
        setSize(Config.WINDOW_WIDTH, Config.WINDOW_HEIGHT);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        
        JPanel panelPrincipal = new JPanel(new BorderLayout(10, 10));
        panelPrincipal.setBackground(Color.WHITE);
        panelPrincipal.setBorder(BorderFactory.createEmptyBorder(20, 20, 20, 20));
        
        // En-tête
        JPanel panelEnTete = creerEnTete();
        panelPrincipal.add(panelEnTete, BorderLayout.NORTH);
        
        // Zone centrale divisée en 2 parties
        JSplitPane splitPane = new JSplitPane(JSplitPane.HORIZONTAL_SPLIT);
        splitPane.setResizeWeight(0.4);
        
        // Gauche : Liste des étudiants non affectés
        JPanel panelGauche = creerPanelEtudiants();
        splitPane.setLeftComponent(panelGauche);
        
        // Droite : Groupes
        JScrollPane scrollGroupes = creerPanelGroupes();
        splitPane.setRightComponent(scrollGroupes);
        
        panelPrincipal.add(splitPane, BorderLayout.CENTER);
        
        // Boutons en bas
        JPanel panelBoutons = creerPanelBoutons();
        panelPrincipal.add(panelBoutons, BorderLayout.SOUTH);
        
        add(panelPrincipal);
    }
    
    private JPanel creerEnTete() {
        JPanel panel = new JPanel(new BorderLayout());
        panel.setBackground(Color.WHITE);
        panel.setBorder(BorderFactory.createEmptyBorder(0, 0, 20, 0));
        
        JLabel titre = new JLabel("Répartition Manuelle - " + promotion.getLibelle());
        titre.setFont(new Font("Arial", Font.BOLD, 24));
        titre.setForeground(new Color(28, 53, 94));
        
        panel.add(titre, BorderLayout.WEST);
        
        return panel;
    }
    
    private JPanel creerPanelEtudiants() {
        JPanel panel = new JPanel(new BorderLayout(5, 5));
        panel.setBackground(Color.WHITE);
        panel.setBorder(BorderFactory.createTitledBorder("Étudiants non affectés"));
        
        // Table des étudiants
        String[] colonnes = {"Nom", "Prénom", "Groupe actuel"};
        modeleTableEtudiants = new DefaultTableModel(colonnes, 0) {
            @Override
            public boolean isCellEditable(int row, int column) {
                return false;
            }
        };
        
        tableEtudiants = new JTable(modeleTableEtudiants);
        tableEtudiants.setSelectionMode(ListSelectionModel.MULTIPLE_INTERVAL_SELECTION);
        tableEtudiants.setRowHeight(25);
        
        JScrollPane scrollPane = new JScrollPane(tableEtudiants);
        panel.add(scrollPane, BorderLayout.CENTER);
        
        // Bouton pour créÂ©er un nouveau groupe
        JButton btnNouveauGroupe = new JButton("CréÂ©er un nouveau groupe");
        btnNouveauGroupe.setBackground(new Color(139, 28, 58));
        btnNouveauGroupe.setForeground(Color.WHITE);
        btnNouveauGroupe.setFont(new Font("Arial", Font.BOLD, 12));
        btnNouveauGroupe.setFocusPainted(false);
        btnNouveauGroupe.addActionListener(e -> creerNouveauGroupe());
        
        panel.add(btnNouveauGroupe, BorderLayout.SOUTH);
        
        return panel;
    }
    
    private JScrollPane creerPanelGroupes() {
        panelGroupes = new JPanel();
        panelGroupes.setLayout(new BoxLayout(panelGroupes, BoxLayout.Y_AXIS));
        panelGroupes.setBackground(Color.WHITE);
        
        JScrollPane scrollPane = new JScrollPane(panelGroupes);
        scrollPane.setBorder(BorderFactory.createTitledBorder("Groupes"));
        
        return scrollPane;
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
        
        JButton btnSauvegarder = new JButton("Sauvegarder les affectations");
        btnSauvegarder.setPreferredSize(new Dimension(220, 40));
        btnSauvegarder.setBackground(new Color(39, 174, 96));
        btnSauvegarder.setForeground(Color.WHITE);
        btnSauvegarder.setFont(new Font("Arial", Font.BOLD, 14));
        btnSauvegarder.setFocusPainted(false);
        btnSauvegarder.addActionListener(e -> sauvegarderAffectations());
        
        panelDroite.add(btnSauvegarder);
        
        panel.add(panelGauche, BorderLayout.WEST);
        panel.add(panelDroite, BorderLayout.EAST);
        
        return panel;
    }
    
    private void chargerDonnees() {
        try {
            // Charger les éÂ©tudiants
            etudiants = controleurPromotion.getEtudiants(promotion.getId());
            
            // Charger les groupes existants
            groupes = controleurGroupe.getGroupes(promotion.getId());
            
            // Afficher les données
            rafraichirTableEtudiants();
            rafraichirPanelGroupes();
            
        } catch (Exception e) {
            JOptionPane.showMessageDialog(this,
                "Erreur lors du chargement des données : " + e.getMessage(),
                "Erreur",
                JOptionPane.ERROR_MESSAGE);
        }
    }
    
    private void rafraichirTableEtudiants() {
        modeleTableEtudiants.setRowCount(0);
        for (Etudiant etu : etudiants) {
            String groupe = (etu.getNomGroupe() != null && !etu.getNomGroupe().isEmpty()) 
                ? etu.getNomGroupe() : "Non affecté";
            modeleTableEtudiants.addRow(new Object[]{
                etu.getNom(),
                etu.getPrenom(),
                groupe
            });
        }
    }
    
    private void rafraichirPanelGroupes() {
        panelGroupes.removeAll();
        
        for (Groupe groupe : groupes) {
            panelGroupes.add(creerPanelGroupe(groupe));
            panelGroupes.add(Box.createVerticalStrut(10));
        }
        
        panelGroupes.revalidate();
        panelGroupes.repaint();
    }
    
    private JPanel creerPanelGroupe(Groupe groupe) {
        JPanel panel = new JPanel(new BorderLayout(5, 5));
        panel.setBackground(new Color(236, 240, 241));
        panel.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createLineBorder(new Color(189, 195, 199), 2),
            BorderFactory.createEmptyBorder(10, 10, 10, 10)
        ));
        panel.setMaximumSize(new Dimension(Integer.MAX_VALUE, 150));
        
        // Titre du groupe
        JLabel lblNom = new JLabel(groupe.getNomGroupe() + " (" + groupe.getEffectif() + "/" + groupe.getEffectifMax() + ")");
        lblNom.setFont(new Font("Arial", Font.BOLD, 16));
        lblNom.setForeground(new Color(28, 53, 94));
        
        // Liste des étudiants du groupe
        StringBuilder sb = new StringBuilder("<html>");
        int count = 0;
        for (Etudiant etu : etudiants) {
            if (etu.getIdGroupe() > 0 && etu.getIdGroupe() == groupe.getIdGroupe()) {
                sb.append(etu.getNom()).append(" ").append(etu.getPrenom()).append("<br>");
                count++;
            }
        }
        if (count == 0) {
            sb.append("<i>Aucun étudiant affecté</i>");
        }
        sb.append("</html>");
        
        JLabel lblEtudiants = new JLabel(sb.toString());
        lblEtudiants.setFont(new Font("Arial", Font.PLAIN, 12));
        
        // Boutons
        JPanel panelBoutons = new JPanel(new FlowLayout(FlowLayout.RIGHT));
        panelBoutons.setBackground(new Color(236, 240, 241));
        
        JButton btnAjouter = new JButton("Ajouter sélection");
        btnAjouter.setBackground(new Color(52, 152, 219));
        btnAjouter.setForeground(Color.WHITE);
        btnAjouter.setFocusPainted(false);
        btnAjouter.addActionListener(e -> ajouterEtudiantsAuGroupe(groupe));
        
        JButton btnSupprimer = new JButton("Retirer du groupe");
        btnSupprimer.setBackground(new Color(231, 76, 60));
        btnSupprimer.setForeground(Color.WHITE);
        btnSupprimer.setFocusPainted(false);
        btnSupprimer.addActionListener(e -> retirerEtudiantsDuGroupe(groupe));
        
        panelBoutons.add(btnAjouter);
        panelBoutons.add(btnSupprimer);
        
        panel.add(lblNom, BorderLayout.NORTH);
        panel.add(new JScrollPane(lblEtudiants), BorderLayout.CENTER);
        panel.add(panelBoutons, BorderLayout.SOUTH);
        
        return panel;
    }
    
    private void creerNouveauGroupe() {
        String nomGroupe = JOptionPane.showInputDialog(this, 
            "Nom du nouveau groupe:", 
            "Nouveau Groupe",
            JOptionPane.QUESTION_MESSAGE);
        
        if (nomGroupe != null && !nomGroupe.trim().isEmpty()) {
            String effectifStr = JOptionPane.showInputDialog(this,
                "Effectif maximum:",
                "20");
            
            try {
                int effectifMax = Integer.parseInt(effectifStr);
                
                // Créer le groupe localement
                Groupe nouveauGroupe = new Groupe();
                nouveauGroupe.setIdGroupe(-(groupes.size() + 1)); // ID temporaire négatif
                nouveauGroupe.setNomGroupe(nomGroupe);
                nouveauGroupe.setEffectif(0);
                nouveauGroupe.setEffectifMax(effectifMax);
                
                groupes.add(nouveauGroupe);
                rafraichirPanelGroupes();
                
            } catch (NumberFormatException e) {
                JOptionPane.showMessageDialog(this,
                    "Effectif invalide",
                    "Erreur",
                    JOptionPane.ERROR_MESSAGE);
            }
        }
    }
    
    private void ajouterEtudiantsAuGroupe(Groupe groupe) {
        int[] selectedRows = tableEtudiants.getSelectedRows();
        if (selectedRows.length == 0) {
            JOptionPane.showMessageDialog(this,
                "Veuillez sélectionner au moins un étudiant",
                "Aucune sélection",
                JOptionPane.WARNING_MESSAGE);
            return;
        }
        
        // VéÂ©rifier la capacitéÂ©
        if (groupe.getEffectif() + selectedRows.length > groupe.getEffectifMax()) {
            JOptionPane.showMessageDialog(this,
                "Le groupe ne peut pas accueillir autant d'étudiants",
                "Capacité dépassée",
                JOptionPane.WARNING_MESSAGE);
            return;
        }
        
        // Affecter les étudiants
        for (int row : selectedRows) {
            Etudiant etu = etudiants.get(row);
            etu.setIdGroupe(groupe.getIdGroupe());
            etu.setNomGroupe(groupe.getNomGroupe());
        }
        
        // Mettre à jour l'effectif
        groupe.setEffectif(groupe.getEffectif() + selectedRows.length);
        
        rafraichirTableEtudiants();
        rafraichirPanelGroupes();
    }
    
    private void retirerEtudiantsDuGroupe(Groupe groupe) {
        // Créer une liste des étudiants du groupe
        List<Etudiant> etudiantsGroupe = new ArrayList<>();
        for (Etudiant etu : etudiants) {
            if (etu.getIdGroupe() > 0 && etu.getIdGroupe() == groupe.getIdGroupe()) {
                etudiantsGroupe.add(etu);
            }
        }
        
        if (etudiantsGroupe.isEmpty()) {
            JOptionPane.showMessageDialog(this,
                "Aucun étudiant dans ce groupe",
                "Groupe vide",
                JOptionPane.INFORMATION_MESSAGE);
            return;
        }
        
        // Créer une liste de sélection
        String[] nomsEtudiants = etudiantsGroupe.stream()
            .map(e -> e.getNom() + " " + e.getPrenom())
            .toArray(String[]::new);
        
        JList<String> liste = new JList<>(nomsEtudiants);
        liste.setSelectionMode(ListSelectionModel.MULTIPLE_INTERVAL_SELECTION);
        
        int result = JOptionPane.showConfirmDialog(this,
            new JScrollPane(liste),
            "Sélectionnez les étudiants à retirer",
            JOptionPane.OK_CANCEL_OPTION,
            JOptionPane.PLAIN_MESSAGE);
        
        if (result == JOptionPane.OK_OPTION) {
            int[] selectedIndices = liste.getSelectedIndices();
            for (int idx : selectedIndices) {
                Etudiant etu = etudiantsGroupe.get(idx);
                etu.setIdGroupe(0);
                etu.setNomGroupe(null);
            }
            
            groupe.setEffectif(groupe.getEffectif() - selectedIndices.length);
            
            rafraichirTableEtudiants();
            rafraichirPanelGroupes();
        }
    }
    
    private void sauvegarderAffectations() {
        try {
            // Créer la liste des affectations
            List<Map<String, Object>> affectations = new ArrayList<>();
            for (Etudiant etu : etudiants) {
                if (etu.getIdGroupe() > 0) {
                    Map<String, Object> affectation = new HashMap<>();
                    affectation.put("idEtudiant", etu.getIdEtudiant());
                    affectation.put("idGroupe", etu.getIdGroupe());
                    affectations.add(affectation);
                }
            }
            
            // Envoyer à l'API
            boolean success = controleurGroupe.saveAffectations(affectations);
            
            if (success) {
                JOptionPane.showMessageDialog(this,
                    "Les affectations ont été sauvegardées avec succès",
                    "Succès",
                    JOptionPane.INFORMATION_MESSAGE);
            } else {
                JOptionPane.showMessageDialog(this,
                    "Erreur lors de la sauvegarde des affectations",
                    "Erreur",
                    JOptionPane.ERROR_MESSAGE);
            }
            
        } catch (Exception e) {
            JOptionPane.showMessageDialog(this,
                "Erreur : " + e.getMessage(),
                "Erreur",
                JOptionPane.ERROR_MESSAGE);
        }
    }
    
    private void retour() {
        new VueConstitutionGroupes().setVisible(true);
        this.dispose();
    }
}
