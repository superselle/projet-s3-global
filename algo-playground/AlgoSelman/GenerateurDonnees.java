package AlgoSelman;

import Utilisateur.Etudiant;
import java.util.ArrayList;
import java.util.List;
import java.util.Random;
public class GenerateurDonnees {
    private static final Random random = new Random();

    public static List<Etudiant> genererPromo(int nbEtudiants) {
        List<Etudiant> promo = new ArrayList<>();
        
        for (int i = 1; i <= nbEtudiants; i++) {
            // Genre
            char genre = (random.nextInt(100) < 25) ? 'F' : 'M';
            
            // Utilisation du vrai constructeur
            Etudiant e = new Etudiant(
                i, 
                "Prenom" + i, 
                "Nom" + i, 
                genre,
                0 // pas de covoiturage par défaut
            );

            // Simulation des données manquantes dans le constructeur
            e.setApprenti(random.nextInt(100) < 15);

            promo.add(e);
        }

        // Création des covoiturages via ID int
        creerCovoiturages(promo, 10); // 10 paires
        return promo;
    }

    /**
     * Crée des paires de covoiturage aléatoires en affectant des IDs partagés.
     * @param promo liste des étudiants à enrichir
     * @param nbPaires nombre de covoiturages à créer
     */
    private static void creerCovoiturages(List<Etudiant> promo, int nbPaires) {
        for (int k = 0; k < nbPaires; k++) {
            Etudiant e1 = promo.get(random.nextInt(promo.size()));
            Etudiant e2 = promo.get(random.nextInt(promo.size()));

            // Si ils ne sont pas déjà en covoit (id = 0) et différents
            if (e1 != e2 && e1.getIdCovoiturage() == 0 && e2.getIdCovoiturage() == 0) {
                int codeUnique = k + 1;
                e1.setIdCovoiturage(codeUnique);
                e2.setIdCovoiturage(codeUnique);
                System.out.println("Covoiturage créé : " + codeUnique);
            }
        }
    }
}