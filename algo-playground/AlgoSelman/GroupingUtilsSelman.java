package AlgoSelman;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Fonctions utilitaires partagées par les algorithmes.
 * @author Selman
 */
public final class GroupingUtilsSelman {

    /**
     * Construit les packs indivisibles à partir des étudiants.
     * @param etudiants liste des étudiants
     * @param c contraintes (pour taille covoiturage)
     * @return liste des packs triés par priorité
     */
    public static List<Pack> construirePacks(List<Etudiant> etudiants, ContraintesGroupesSelman c) {
        Map<Integer, List<Etudiant>> covoiturages = new HashMap<>();
        List<Pack> packs = new ArrayList<>();
        
        for (Etudiant e : etudiants) {
            int id = e.getIdCovoiturage();
            if (id == 0) {
                List<Etudiant> solo = new ArrayList<>();
                solo.add(e);
                packs.add(new Pack(0, solo));
            } else {
                if (!covoiturages.containsKey(id)) {
                    covoiturages.put(id, new ArrayList<>());
                }
                covoiturages.get(id).add(e);
            }
        }
        
        int compteur = 1;
        for (List<Etudiant> membres : covoiturages.values()) {
            packs.add(new Pack(compteur++, membres));
        }
        
        // Tri simple : gros packs d'abord
            trierPacks(packs);
        return packs;
    }
    
        /**
         * Trie la liste des packs selon l'heuristique : taille desc, apprentis,
         * filles.
         * @param packs liste de packs (modifiée en place)
         *
         * Utilisation : appeler après `construirePacks()` pour placer d'abord
         * les packs les plus contraignants avant la distribution.
         */
        private static void trierPacks(List<Pack> packs) {
        for (int i = 0; i < packs.size() - 1; i++) {
            for (int j = i + 1; j < packs.size(); j++) {
                    // Si l'ordre courant est inversé, échanger
                    if (comparerPacks(packs.get(i), packs.get(j)) > 0) {
                    Pack temp = packs.get(i);
                    packs.set(i, packs.get(j));
                    packs.set(j, temp);
                }
            }
        }
    }
    
        // Compare deux packs selon l'heuristique : taille, apprentis, filles.
        private static int comparerPacks(Pack a, Pack b) {
            // privilégier le pack le plus grand
            if (a.taille != b.taille) return b.taille - a.taille;
            // ensuite celui qui contient le plus d'apprentis
            if (a.nbApprentis != b.nbApprentis) return b.nbApprentis - a.nbApprentis;
            // enfin celui avec le plus de filles
            return b.nbFilles - a.nbFilles;
        }

    /**
     * Calcule le nombre optimal de groupes pour n étudiants.
     * @param n nombre d'étudiants
     * @param c contraintes de taille
     * @return nombre de groupes k tel que k*tailleMin <= n <= k*tailleMax
     */
    public static int choisirNombreDeGroupes(int n, ContraintesGroupesSelman c) {
        int meilleurK = 1;
        double meilleurEcart = Double.MAX_VALUE;
        
        for (int k = 1; k <= n; k++) {
            if (k * c.tailleMin <= n && n <= k * c.tailleMax) {
                double tailleMoyenne = (double) n / k;
                double ecart = Math.abs(tailleMoyenne - c.tailleCible);
                if (ecart < meilleurEcart) {
                    meilleurEcart = ecart;
                    meilleurK = k;
                }
            }
        }
        return meilleurK;
    }

    // Crée k groupes vides
    public static List<Groupe> initialiserGroupes(int k) {
        List<Groupe> groupes = new ArrayList<>();
        for (int i = 0; i < k; i++) {
            Groupe g = new Groupe(i + 1);
            g.setLettre("S2-" + (i + 1));
            groupes.add(g);
        }
        return groupes;
    }

    // Vérifie si la solution respecte les contraintes
    public static boolean verifierSolution(List<Groupe> groupes, ContraintesGroupesSelman c) {
        for (Groupe g : groupes) {
            int eff = g.getEffectif();
            if (eff < c.tailleMin || eff > c.tailleMax) return false;
            if (g.getNbApprentis() < c.minApprentisParGroupe) return false;
        }
        return true;
    }

    /**
     * Calcule le score d'une solution (à minimiser).
     * @param groupes liste des groupes à évaluer
     * @return EcartApprentis + EcartFilles (0 = parfait)
     */
    public static double calculerScore(List<Groupe> groupes) {
        int minApp = Integer.MAX_VALUE, maxApp = 0;
        int minFil = Integer.MAX_VALUE, maxFil = 0;
        
        for (Groupe g : groupes) {
            int app = g.getNbApprentis();
            int fil = g.getNbFilles();
            if (app < minApp) minApp = app;
            if (app > maxApp) maxApp = app;
            if (fil < minFil) minFil = fil;
            if (fil > maxFil) maxFil = fil;
        }
        return (maxApp - minApp) + (maxFil - minFil);
    }

    // Ajoute un pack dans un groupe
    public static void ajouterPack(Groupe g, Pack p) {
        for (Etudiant e : p.membres) {
            g.ajouterEtudiant(e);
        }
    }

    // Retire un pack d'un groupe
    public static void retirerPack(Groupe g, Pack p) {
        g.retirerEtudiants(p.membres);
    }

    // Clone une liste de groupes
    public static List<Groupe> clonerGroupes(List<Groupe> source) {
        List<Groupe> copie = new ArrayList<>();
        for (Groupe gSrc : source) {
            Groupe gNew = new Groupe(gSrc.getId());
            gNew.setLettre(gSrc.getLettre());
            for (Etudiant e : gSrc.getEtudiants()) {
                gNew.ajouterEtudiant(e);
            }
            copie.add(gNew);
        }
        return copie;
    }

    // Compte les apprentis
    public static int compterApprentis(List<Etudiant> etudiants) {
        int count = 0;
        for (Etudiant e : etudiants) {
            if (e.isApprenti()) count++;
        }
        return count;
    }

    // Compte les filles
    public static int compterFilles(List<Etudiant> etudiants) {
        int count = 0;
        for (Etudiant e : etudiants) {
            if (e.isFille()) count++;
        }
        return count;
    }
}
