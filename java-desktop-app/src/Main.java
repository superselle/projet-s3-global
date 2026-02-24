import javax.swing.SwingUtilities;
import javax.swing.UIManager;

import vue.VueConnexion;

/**
 * Point d'entrée de l'application Java
 * Application de Constitution de Groupes - SAE S301
 * 
 * @author Projet SAE S301
 * @version 1.0
 */
public class Main {
    
    public static void main(String[] args) {
        // Configuration du Look and Feel (apparence)
        try {
            // Utiliser le Look and Feel du système pour une meilleure intégration
            UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
        } catch (Exception e) {
            System.err.println("Impossible de charger le Look and Feel du système");
            e.printStackTrace();
        }
        
        // Lancer l'application sur le thread EDT (Event Dispatch Thread)
        SwingUtilities.invokeLater(new Runnable() {
            @Override
            public void run() {
                // Afficher la fenêtre de connexion
                VueConnexion vueConnexion = new VueConnexion();
                vueConnexion.setVisible(true);
            }
        });
    }
}
