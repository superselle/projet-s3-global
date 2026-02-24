package AlgoRached;

import static AlgoRached.GroupingUtilsS3.*;

import java.util.ArrayList;
import java.util.List;

import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Force brute (backtracking) S3.
 *
 * - Place tous les "option anglais" dans le groupe 1 .
 * - Explore toutes les affectations possibles des autres étudiants.
 * - Garde la meilleure solution selon GroupingUtilsS3.score(...).
 *
 * Pour éviter l'explosion combinatoire, la recherche est limitée par maxNoeuds.
 */
public class ForceBruteBacktrackingS3 implements GroupeAlgoS3 {

    private final long maxNoeuds;
    private long noeuds;

    public ForceBruteBacktrackingS3(long maxNoeuds) {
        this.maxNoeuds = maxNoeuds;
    }

    @Override
    public List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupesS3 c) {
        if (etudiants == null || etudiants.isEmpty()) throw new GroupingException("Aucun étudiant");

        int nbAnglais = compterOptionAnglais(etudiants);
        if (nbAnglais > c.tailleMax) {
            throw new GroupingException("Impossible: " + nbAnglais + " étudiants option anglais > tailleMax (" + c.tailleMax + ")");
        }

        int k = choisirNombreDeGroupes(etudiants.size(), c);
        List<Groupe> groupes = initialiserGroupes(k);

        // Fixe anglais dans groupe 1
        List<Etudiant> reste = new ArrayList<>();
        for (Etudiant e : etudiants) {
            if (e.aOptionAnglais()) groupes.get(0).ajouterEtudiant(e);
            else reste.add(e);
        }
        if (groupes.get(0).getEffectif() > c.tailleMax) {
            throw new GroupingException("Impossible: groupe anglais dépasse tailleMax");
        }

        // Tri pour améliorer le pruning: redoublants d'abord
        reste = trierPourPlacement(reste);

        this.noeuds = 0;
        Best best = new Best();

        backtrack(0, reste, groupes, etudiants, c, best);

        if (best.groupes == null) throw new GroupingException("Aucune solution trouvée (ou limite atteinte)");
        verifierSolution(best.groupes, etudiants, c);
        return best.groupes;
    }

    private static class Best {
        double score = Double.POSITIVE_INFINITY;
        List<Groupe> groupes = null;
    }

    private void backtrack(int idx, List<Etudiant> reste, List<Groupe> groupes, List<Etudiant> tous, ContraintesGroupesS3 c, Best best) {
        if (noeuds++ > maxNoeuds) return;

        // Pruning: capacité et possibilité d'atteindre tailleMin
        int remaining = reste.size() - idx;
        int deficit = 0;
        for (Groupe g : groupes) {
            int d = c.tailleMin - g.getEffectif();
            if (d > 0) deficit += d;
            if (g.getEffectif() > c.tailleMax) return;
        }
        if (remaining < deficit) return;

        if (idx == reste.size()) {
            for (Groupe g : groupes) {
                int n = g.getEffectif();
                if (n < c.tailleMin || n > c.tailleMax) return;
            }
            double s = score(groupes, c);
            if (s < best.score) {
                best.score = s;
                best.groupes = deepCopy(groupes);
            }
            return;
        }

        Etudiant e = reste.get(idx);

        // Essayer tous les groupes
        for (Groupe g : groupes) {
            if (g.getEffectif() >= c.tailleMax) continue;

            g.ajouterEtudiant(e);

            // Pruning léger: si déjà trop déséquilibré, on peut continuer quand même (pas de borne stricte)
            backtrack(idx + 1, reste, groupes, tous, c, best);

            g.retirerEtudiant(e);
        }
    }

    private static List<Groupe> deepCopy(List<Groupe> groupes) {
        List<Groupe> copy = new ArrayList<>();
        for (Groupe g : groupes) {
            Groupe ng = new Groupe();
            ng.setId(g.getId());
            ng.ajouterEtudiants(new ArrayList<>(g.getEtudiants()));
            copy.add(ng);
        }
        return copy;
    }
}
