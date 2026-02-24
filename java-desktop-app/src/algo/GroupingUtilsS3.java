package algo;

import java.util.ArrayList;
import java.util.Comparator;
import java.util.HashSet;
import java.util.List;
import java.util.Set;

import modele.Groupe;
import modele.Etudiant;

/**
 * Outils partagés pour S3 (sans covoiturage).
 */
public final class GroupingUtilsS3 {
    private GroupingUtilsS3() {}

    /** Exception dédiée aux cas "pas de solution" / données invalides. */
    public static class GroupingException extends RuntimeException {
        public GroupingException(String message) { super(message); }
    }

    /**
     * Choisit k (nb de groupes) tel que k*min <= N <= k*max.
     * Si plusieurs k possibles, on choisit celui dont la taille moyenne est la plus proche de la cible.
     */
    public static int choisirNombreDeGroupes(int nEtudiants, ContraintesGroupesS3 c) {
        if (nEtudiants <= 0) throw new GroupingException("Aucun étudiant");
        List<Integer> candidats = new ArrayList<>();
        for (int k = 1; k <= nEtudiants; k++) {
            if (k * c.tailleMin <= nEtudiants && nEtudiants <= k * c.tailleMax) {
                candidats.add(k);
            }
        }
        if (candidats.isEmpty()) {
            throw new GroupingException("Impossible: " + nEtudiants +
                    " étudiants ne peuvent pas être répartis en groupes de " + c.tailleMin + ".." + c.tailleMax);
        }
        double bestDist = Double.POSITIVE_INFINITY;
        int bestK = candidats.get(0);
        for (int k : candidats) {
            double moyenne = (double) nEtudiants / k;
            double dist = Math.abs(moyenne - c.tailleCible);
            if (dist < bestDist) { bestDist = dist; bestK = k; }
        }
        return bestK;
    }

    public static List<Groupe> initialiserGroupes(int k) {
        List<Groupe> res = new ArrayList<>();
        for (int i = 0; i < k; i++) {
            Groupe g = new Groupe();
            g.setId(i + 1);
            res.add(g);
        }
        return res;
    }

    public static int compterRedoublants(List<Etudiant> etudiants) {
        int r = 0;
        for (Etudiant e : etudiants) if (e.isRedoublant()) r++;
        return r;
    }

    public static int compterOptionAnglais(List<Etudiant> etudiants) {
        int a = 0;
        for (Etudiant e : etudiants) if (e.aOptionAnglais()) a++;
        return a;
    }

    /** Retourne l'index du groupe qui contient l'étudiant e (ou -1). */
    public static int indexDuGroupeDe(List<Groupe> groupes, Etudiant e) {
        for (int i = 0; i < groupes.size(); i++) {
            if (groupes.get(i).getEtudiants().contains(e)) return i;
        }
        return -1;
    }

    /**
     * Vérifie toutes les règles S3 :
     * - tailles 17..20
     * - tous les "option anglais" dans un seul groupe
     * - unicité (chaque étudiant apparaît une fois)
     */
    public static void verifierSolution(List<Groupe> groupes, List<Etudiant> tous, ContraintesGroupesS3 c) {
        if (groupes == null || groupes.isEmpty()) throw new GroupingException("Aucun groupe");

        // Tailles
        for (int i = 0; i < groupes.size(); i++) {
            int n = groupes.get(i).getEffectif();
            if (n < c.tailleMin || n > c.tailleMax) {
                throw new GroupingException("Taille invalide pour le groupe " + (i+1) + ": " + n);
            }
        }

        // Unicité / couverture
        Set<Etudiant> vus = new HashSet<>();
        for (Groupe g : groupes) {
            for (Etudiant e : g.getEtudiants()) {
                if (!vus.add(e)) throw new GroupingException("Étudiant dupliqué: " + e.getNomComplet());
            }
        }
        if (vus.size() != tous.size()) {
            throw new GroupingException("Solution incomplète: " + vus.size() + "/" + tous.size() + " étudiants placés");
        }

        // Option anglais dans un seul groupe
        int idxAnglais = -1;
        for (int i = 0; i < groupes.size(); i++) {
            for (Etudiant e : groupes.get(i).getEtudiants()) {
                if (e.aOptionAnglais()) {
                    if (idxAnglais == -1) idxAnglais = i;
                    else if (idxAnglais != i) {
                        throw new GroupingException("Option anglais répartie sur plusieurs groupes");
                    }
                }
            }
        }
    }

    /**
     * Score d'optimisation (plus petit = meilleur) :
     * - écart à la taille cible
     * - écart d'équilibrage des redoublants (poidsé)
     */
    public static double score(List<Groupe> groupes, ContraintesGroupesS3 c) {
        int k = groupes.size();
        int totalRed = 0;
        int total = 0;
        for (Groupe g : groupes) {
            total += g.getEffectif();
            totalRed += g.getNbRedoublants();
        }
        double targetRed = (k == 0) ? 0.0 : ((double) totalRed / k);

        double s = 0.0;
        for (Groupe g : groupes) {
            double dT = g.getEffectif() - c.tailleCible;
            double dR = g.getNbRedoublants() - targetRed;
            s += dT * dT;
            s += c.poidsRedoublants * (dR * dR);
        }
        return s;
    }

    /** Trie utile : redoublants d'abord, puis option anglais, puis nom. */
    public static List<Etudiant> trierPourPlacement(List<Etudiant> etudiants) {
        List<Etudiant> res = new ArrayList<>(etudiants);
        res.sort(Comparator
                .comparing((Etudiant e) -> e.isRedoublant()).reversed()
                .thenComparing((Etudiant e) -> e.aOptionAnglais()).reversed()
                .thenComparing(Etudiant::getNomComplet));
        return res;
    }
}
