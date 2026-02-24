package vue;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.Font;
import java.awt.GridBagConstraints;
import java.awt.GridBagLayout;
import java.awt.Insets;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;

import javax.swing.BorderFactory;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JPasswordField;
import javax.swing.JTextField;
import javax.swing.SwingConstants;

import controleur.ControleurAuth;
import utils.Config;
import utils.RoundedButton;
import utils.RoundedPanel;
import utils.ModernTextField;

/**
 * Vue de connexion (login)
 */
public class VueConnexion extends JFrame {
    
    private JTextField champIdentifiant;
    private JPasswordField champMotDePasse;
    private JButton boutonConnexion;
    private ControleurAuth controleurAuth;
    
    public VueConnexion() {
        this.controleurAuth = new ControleurAuth();
        initialiserInterface();
    }
    
    private void initialiserInterface() {
        setTitle(Config.APP_TITLE + " - Connexion");
        setSize(550, 500);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        setResizable(false);
        
        // Panel principal avec dégradé
        JPanel panelPrincipal = new JPanel(new BorderLayout(10, 10));
        panelPrincipal.setBackground(new Color(245, 247, 250));
        panelPrincipal.setBorder(BorderFactory.createEmptyBorder(40, 40, 40, 40));
        
        // En-tête
        JPanel panelEnTete = creerEnTete();
        panelPrincipal.add(panelEnTete, BorderLayout.NORTH);
        
        // Formulaire
        RoundedPanel panelFormulaire = creerFormulaire();
        panelPrincipal.add(panelFormulaire, BorderLayout.CENTER);
        
        add(panelPrincipal);
    }
    
    private JPanel creerEnTete() {
        JPanel panel = new JPanel();
        panel.setBackground(new Color(245, 247, 250));
        panel.setLayout(new BorderLayout(10, 15));
        panel.setBorder(BorderFactory.createEmptyBorder(0, 0, 20, 0));
        
        JLabel titre = new JLabel("Application de constitution de groupes", SwingConstants.CENTER);
        titre.setFont(new Font("Segoe UI", Font.BOLD, 26));
        titre.setForeground(new Color(28, 53, 94));
        
        JLabel sousTitre = new JLabel("Connectez-vous à votre espace", SwingConstants.CENTER);
        sousTitre.setFont(new Font("Segoe UI", Font.PLAIN, 15));
        sousTitre.setForeground(new Color(108, 117, 125));
        
        panel.add(titre, BorderLayout.NORTH);
        panel.add(sousTitre, BorderLayout.CENTER);
        
        return panel;
    }
    
    private RoundedPanel creerFormulaire() {
        RoundedPanel panel = new RoundedPanel(20);
        panel.setLayout(new GridBagLayout());
        panel.setBackgroundColor(Color.WHITE);
        panel.setDrawShadow(true);
        panel.setBorder(BorderFactory.createEmptyBorder(40, 40, 40, 40));
        
        GridBagConstraints gbc = new GridBagConstraints();
        gbc.fill = GridBagConstraints.HORIZONTAL;
        gbc.insets = new Insets(10, 10, 10, 10);
        
        // Label identifiant
        gbc.gridx = 0;
        gbc.gridy = 0;
        gbc.gridwidth = 2;
        JLabel labelIdentifiant = new JLabel("Identifiant");
        labelIdentifiant.setFont(new Font("Segoe UI", Font.BOLD, 13));
        labelIdentifiant.setForeground(new Color(73, 80, 87));
        panel.add(labelIdentifiant, gbc);
        
        // Champ identifiant
        gbc.gridy = 1;
        champIdentifiant = new ModernTextField(20);
        champIdentifiant.setPreferredSize(new Dimension(350, 45));
        champIdentifiant.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        panel.add(champIdentifiant, gbc);
        
        // Label mot de passe
        gbc.gridy = 2;
        gbc.insets = new Insets(20, 10, 5, 10);
        JLabel labelMotDePasse = new JLabel("Mot de passe");
        labelMotDePasse.setFont(new Font("Segoe UI", Font.BOLD, 13));
        labelMotDePasse.setForeground(new Color(73, 80, 87));
        panel.add(labelMotDePasse, gbc);
        
        // Champ mot de passe
        gbc.gridy = 3;
        gbc.insets = new Insets(5, 10, 10, 10);
        champMotDePasse = new JPasswordField(20);
        champMotDePasse.setPreferredSize(new Dimension(350, 45));
        champMotDePasse.setFont(new Font("Segoe UI", Font.PLAIN, 14));
        champMotDePasse.setBorder(BorderFactory.createCompoundBorder(
            BorderFactory.createLineBorder(new Color(200, 200, 200), 1),
            BorderFactory.createEmptyBorder(10, 15, 10, 15)
        ));
        panel.add(champMotDePasse, gbc);
        
        // Bouton connexion
        gbc.gridy = 4;
        gbc.insets = new Insets(25, 10, 10, 10);
        boutonConnexion = new RoundedButton("Se connecter", new Color(52, 152, 219));
        boutonConnexion.setPreferredSize(new Dimension(350, 50));
        boutonConnexion.setFont(new Font("Segoe UI", Font.BOLD, 15));
        boutonConnexion.setForeground(Color.WHITE);
        
        boutonConnexion.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                seConnecter();
            }
        });
        
        panel.add(boutonConnexion, gbc);
        
        // Action sur Enter
        champMotDePasse.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent e) {
                seConnecter();
            }
        });
        
        return panel;
    }
    
    private void seConnecter() {
        String identifiant = champIdentifiant.getText().trim();
        String motDePasse = new String(champMotDePasse.getPassword());
        
        if (identifiant.isEmpty() || motDePasse.isEmpty()) {
            JOptionPane.showMessageDialog(
                this,
                "Veuillez remplir tous les champs.",
                "Champs manquants",
                JOptionPane.WARNING_MESSAGE
            );
            return;
        }
        
        // DéƒÂ©sactiver le bouton pendant la connexion
        boutonConnexion.setEnabled(false);
        boutonConnexion.setText("Connexion en cours...");
        
        // Tenter la connexion
        boolean succes = controleurAuth.login(identifiant, motDePasse);
        
        if (succes) {
            // Vérifier que c'est un responsable filière
            String role = controleurAuth.getUserRole();
            if ("responsable_filiere".equals(role)) {
                // Ouvrir le dashboard
                dispose();
                new VueDashboard().setVisible(true);
            } else {
                JOptionPane.showMessageDialog(
                    this,
                    "Cette application est réservé aux responsables de filière/année.",
                    "Accès refusé",
                    JOptionPane.ERROR_MESSAGE
                );
                controleurAuth.logout();
                boutonConnexion.setEnabled(true);
                boutonConnexion.setText("Se connecter");
            }
        } else {
            JOptionPane.showMessageDialog(
                this,
                "Identifiant ou mot de passe incorrect.",
                "Erreur de connexion",
                JOptionPane.ERROR_MESSAGE
            );
            boutonConnexion.setEnabled(true);
            boutonConnexion.setText("Se connecter");
            champMotDePasse.setText("");
        }
    }
    
    public static void main(String[] args) {
        // Pour tester la vue indépendamment
        javax.swing.SwingUtilities.invokeLater(new Runnable() {
            public void run() {
                new VueConnexion().setVisible(true);
            }
        });
    }
}
