package AlgoSelman;

import java.util.ArrayList;
import java.util.List;
import java.util.Random;
import Scolarite.Groupe;
import Utilisateur.Etudiant;

// Programme de test des algorithmes
public class DemoSelman {
    private static final Random random = new Random(42);

    public static void main(String[] args) {
        System.out.println("=== PROJET SAE S3 - ALGORITHMES SELMAN ===\n");

        ContraintesGroupesSelman contraintes = ContraintesGroupesSelman.s2();
        System.out.println("Config : " + contraintes + "\n");

        // Test 1 : Promo standard
        System.out.println("--- TEST 1 : 105 etudiants ---");
        List<Etudiant> promo = genererPromo(105, 25, 15, 10);
        afficherStats(promo);
        testerGloutons(promo, contraintes);

        // Test 2 : Force brute avec 40 étudiants
        System.out.println("\n--- TEST 2 : 40 etudiants (avec Force Brute) ---");
        List<Etudiant> promo40 = genererPromo(40, 25, 15, 6);
        afficherStats(promo40);
        testerAvecForceBrute(promo40, contraintes);

        // Test 3 : Comparaison
        System.out.println("\n--- TEST 3 : Comparaison sur 20 jeux ---");
        comparer(20, 100, contraintes);
    }

    public static List<Etudiant> genererPromo(int nb, int pctFilles, int pctApprentis, int nbCovoit) {
        List<Etudiant> promo = new ArrayList<>();
        for (int i = 1; i <= nb; i++) {
            char genre = random.nextInt(100) < pctFilles ? 'F' : 'M';
            Etudiant e = new Etudiant(i, "P" + i, "N" + i, genre, 0);
            e.setApprenti(random.nextInt(100) < pctApprentis);
            promo.add(e);
        }
        
        int crees = 0;
        for (int t = 0; t < nbCovoit * 10 && crees < nbCovoit; t++) {
            Etudiant e1 = promo.get(random.nextInt(promo.size()));
            Etudiant e2 = promo.get(random.nextInt(promo.size()));
            if (e1 != e2 && e1.getIdCovoiturage() == 0 && e2.getIdCovoiturage() == 0) {
                e1.setIdCovoiturage(crees + 1);
                e2.setIdCovoiturage(crees + 1);
                crees++;
            }
        }
        return promo;
    }

    private static void afficherStats(List<Etudiant> promo) {
        int filles = GroupingUtilsSelman.compterFilles(promo);
        int apprentis = GroupingUtilsSelman.compterApprentis(promo);
        System.out.println("Promo : " + promo.size() + " etudiants, " + filles + " filles, " + apprentis + " apprentis\n");
    }

    private static void afficherResultat(String nom, List<Groupe> groupes, long temps, ContraintesGroupesSelman c) {
        System.out.println(nom + " :");
        if (groupes == null) { System.out.println("  Echec\n"); return; }
        
        for (Groupe g : groupes) {
            System.out.println("  Groupe " + g.getId() + " : " + g.getEffectif() + " etu, " + g.getNbFilles() + " filles, " + g.getNbApprentis() + " apprentis");
        }
        System.out.println("  Score : " + GroupingUtilsSelman.calculerScore(groupes) + " - Temps : " + temps + " ms");
        System.out.println("  Valide : " + GroupingUtilsSelman.verifierSolution(groupes, c) + "\n");
    }

    private static void testerGloutons(List<Etudiant> promo, ContraintesGroupesSelman c) {
        try {
            long t1 = System.currentTimeMillis();
            List<Groupe> r1 = new GloutonPrioriteSelman().generer(new ArrayList<>(promo), c);
            afficherResultat("Glouton Priorite", r1, System.currentTimeMillis() - t1, c);
        } catch (Exception e) { System.out.println("Erreur Priorite: " + e.getMessage()); }

        try {
            long t2 = System.currentTimeMillis();
            List<Groupe> r2 = new GloutonScoreSelman().generer(new ArrayList<>(promo), c);
            afficherResultat("Glouton Score", r2, System.currentTimeMillis() - t2, c);
        } catch (Exception e) { System.out.println("Erreur Score: " + e.getMessage()); }
    }

    private static void testerAvecForceBrute(List<Etudiant> promo, ContraintesGroupesSelman c) {
        testerGloutons(promo, c);
        try {
            System.out.println("Force Brute (limite 500M noeuds) :");
            long t = System.currentTimeMillis();
            List<Groupe> r = new ForceBruteSelman(500_000_000).generer(new ArrayList<>(promo), c);
            afficherResultat("Force Brute", r, System.currentTimeMillis() - t, c);
        } catch (Exception e) { 
            System.out.println("  Erreur ForceBrute: " + e.getMessage() + "\n"); 
        }
    }

    private static void comparer(int nbJeux, int taille, ContraintesGroupesSelman c) {
        int vPrio = 0, vScore = 0;
        for (int i = 0; i < nbJeux; i++) {
            List<Etudiant> p = genererPromo(taille, 20 + random.nextInt(20), 10 + random.nextInt(15), 5 + random.nextInt(10));
            double s1 = Double.MAX_VALUE, s2 = Double.MAX_VALUE;
            try { s1 = GroupingUtilsSelman.calculerScore(new GloutonPrioriteSelman().generer(new ArrayList<>(p), c)); } catch (Exception e) {}
            try { s2 = GroupingUtilsSelman.calculerScore(new GloutonScoreSelman().generer(new ArrayList<>(p), c)); } catch (Exception e) {}
            if (s1 < s2) vPrio++; else if (s2 < s1) vScore++;
        }
        System.out.println("Priorite gagne " + vPrio + "/" + nbJeux + " fois");
        System.out.println("Score gagne " + vScore + "/" + nbJeux + " fois");
    }
}
