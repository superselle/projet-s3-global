package vue;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.Font;

import javax.swing.BorderFactory;
import javax.swing.BoxLayout;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;

import modele.Promotion;
import utils.Config;

/**
 * Vue pour la répartition manuelle des étudiants
 * (Version simplifiée - à compléter avec drag & drop)
 */
public class VueRepartitionManuelle extends JFrame {
    
    private Promotion promotion;
    
    public VueRepartitionManuelle(Promotion promotion) {
        this.promotion = promotion;
        initialiserInterface();
    }
    
    private void initialiserInterface() {
        setTitle(Config.APP_TITLE + " - Répartition Manuelle");
        setSize(Config.WINDOW_WIDTH, Config.WINDOW_HEIGHT);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);
        
        JPanel panelPrincipal = new JPanel(new BorderLayout(10, 10));
        panelPrincipal.setBackground(Color.WHITE);
        panelPrincipal.setBorder(BorderFactory.createEmptyBorder(20, 20, 20, 20));
        
        JPanel panelEnTete = new JPanel();
        panelEnTete.setLayout(new BoxLayout(panelEnTete, BoxLayout.Y_AXIS));
        panelEnTete.setBackground(Color.WHITE);
        
        JLabel titre = new JLabel("Répartition Manuelle - " + promotion.getLibelle());
        titre.setFont(new Font("Arial", Font.BOLD, 24));
        titre.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        JLabel info = new JLabel("Module de répartition manuelle (drag & drop) - En développement");
        info.setFont(new Font("Arial", Font.PLAIN, 14));
        info.setForeground(new Color(127, 140, 141));
        info.setAlignmentX(JLabel.LEFT_ALIGNMENT);
        
        panelEnTete.add(titre);
        panelEnTete.add(javax.swing.Box.createVerticalStrut(10));
        panelEnTete.add(info);
        
        panelPrincipal.add(panelEnTete, BorderLayout.NORTH);
        
        // TODO: Implémenter l'interface drag & drop complète
        JLabel placeholder = new JLabel("<html><center>Interface de glisser-déposer à implémenter<br><br>" +
                "Cette vue permettra de :<br>" +
                "- Visualiser tous les étudiants non affectés<br>" +
                "- Créer des groupes<br>" +
                "- Glisser-déposer les étudiants dans les groupes<br>" +
                "- Voir les statistiques en temps réel</center></html>");
        placeholder.setFont(new Font("Arial", Font.PLAIN, 16));
        placeholder.setHorizontalAlignment(JLabel.CENTER);
        
        panelPrincipal.add(placeholder, BorderLayout.CENTER);
        
        JButton boutonRetour = new JButton("Retour");
        boutonRetour.setPreferredSize(new Dimension(120, 40));
        boutonRetour.setFont(new Font("Arial", Font.BOLD, 14));
        boutonRetour.setBackground(new Color(149, 165, 166));
        boutonRetour.setForeground(Color.WHITE);
        boutonRetour.setFocusPainted(false);
        boutonRetour.addActionListener(e -> retour());
        
        JPanel panelBoutons = new JPanel(new java.awt.FlowLayout(java.awt.FlowLayout.RIGHT));
        panelBoutons.setBackground(Color.WHITE);
        panelBoutons.add(boutonRetour);
        
        panelPrincipal.add(panelBoutons, BorderLayout.SOUTH);
        
        add(panelPrincipal);
    }
    
    private void retour() {
        new VueConstitutionGroupes().setVisible(true);
        this.dispose();
    }
}
