package algo;

import static algo.GroupingUtilsS3.*;

import java.util.ArrayList;
import java.util.List;

import modele.Groupe;
import modele.Etudiant;

/**
 * Glouton S3 #2
 *
 * Critère glouton: pour chaque étudiant (redoublants d'abord),
 * on choisit le groupe qui minimise un coût local:
 * - écart à la taille cible
 * - écart au nombre "cible" de redoublants (totalRed/k)
 *
 * Tous les "option anglais" sont forcés dans le groupe 1 (index 0).
 */
public class GloutonScoreLocalS3 implements GroupeAlgoS3 {

    @Override
    public List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupesS3 c) {
        if (etudiants == null || etudiants.isEmpty()) throw new GroupingException("Aucun étudiant");

        int nbAnglais = compterOptionAnglais(etudiants);
        if (nbAnglais > c.tailleMax) {
            throw new GroupingException("Impossible: " + nbAnglais + " étudiants option anglais > tailleMax (" + c.tailleMax + ")");
        }

        int k = choisirNombreDeGroupes(etudiants.size(), c);
        List<Groupe> groupes = initialiserGroupes(k);

        // Place anglais dans le groupe 1
        List<Etudiant> reste = new ArrayList<>();
        for (Etudiant e : etudiants) {
            if (e.aOptionAnglais()) groupes.get(0).ajouterEtudiant(e);
            else reste.add(e);
        }

        // Tri conseillé: redoublants d'abord (meilleur contrôle d'équilibrage)
        reste = trierPourPlacement(reste);

        int totalRed = compterRedoublants(etudiants);
        double targetRed = (double) totalRed / k;

        for (Etudiant e : reste) {
            int bestIdx = -1;
            double bestCost = Double.POSITIVE_INFINITY;

            for (int i = 0; i < groupes.size(); i++) {
                Groupe g = groupes.get(i);
                if (g.getEffectif() >= c.tailleMax) continue;

                // coût local si on ajoute e
                int newSize = g.getEffectif() + 1;
                int newRed = g.getNbRedoublants() + (e.isRedoublant() ? 1 : 0);

                double costSize = Math.abs(newSize - c.tailleCible);
                double costRed = Math.abs(newRed - targetRed);

                double cost = costSize + c.poidsRedoublants * costRed;

                // tie-break: groupe le moins rempli
                if (cost < bestCost || (Math.abs(cost - bestCost) < 1e-9 && newSize < groupes.get(bestIdx).getEffectif())) {
                    bestCost = cost;
                    bestIdx = i;
                }
            }

            if (bestIdx < 0) throw new GroupingException("Plus de place pour placer un étudiant");
            groupes.get(bestIdx).ajouterEtudiant(e);
        }

        // Correction tailleMin
        GloutonRedoublantsEquilibre.corrigerTaillesMin(groupes, c);

        verifierSolution(groupes, etudiants, c);
        return groupes;
    }
}
