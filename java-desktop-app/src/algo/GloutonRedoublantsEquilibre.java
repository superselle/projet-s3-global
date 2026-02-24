package algo;

import static algo.GroupingUtilsS3.*;

import java.util.ArrayList;
import java.util.List;

import modele.Groupe;
import modele.Etudiant;

/**
 * Glouton S3 #1
 *
 * Stratégie:
 * 1) Tous les "option anglais" vont dans le groupe 1 (index 0).
 * 2) On distribue d'abord les redoublants restants en équilibrant le compteur de redoublants par groupe.
 * 3) Puis on distribue les non-redoublants pour équilibrer les tailles.
 * 4) Correction: si un groupe est < tailleMin, on déplace des étudiants depuis un groupe > tailleMin (sans toucher à la contrainte anglais).
 */
public class GloutonRedoublantsEquilibre implements GroupeAlgoS3 {

    @Override
    public List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupesS3 c) {
        if (etudiants == null || etudiants.isEmpty()) throw new GroupingException("Aucun étudiant");
        int n = etudiants.size();

        // Vérif préalable: le nombre d'options anglais doit pouvoir tenir dans un seul groupe.
        int nbAnglais = compterOptionAnglais(etudiants);
        if (nbAnglais > c.tailleMax) {
            throw new GroupingException("Impossible: " + nbAnglais + " étudiants option anglais > tailleMax (" + c.tailleMax + ")");
        }

        int k = choisirNombreDeGroupes(n, c);
        List<Groupe> groupes = initialiserGroupes(k);
        Groupe gAnglais = groupes.get(0);

        List<Etudiant> red = new ArrayList<>();
        List<Etudiant> autres = new ArrayList<>();

        // 1) Place tous les "anglais" dans le groupe 1 (index 0), sépare le reste.
        for (Etudiant e : etudiants) {
            if (e.aOptionAnglais()) gAnglais.ajouterEtudiant(e);
            else if (e.isRedoublant()) red.add(e);
            else autres.add(e);
        }

        if (gAnglais.getEffectif() > c.tailleMax) {
            throw new GroupingException("Impossible: groupe anglais dépasse tailleMax après placement initial");
        }

        // 2) Répartir les redoublants en équilibrant (min redoublants, puis min taille)
        for (Etudiant e : red) {
            Groupe best = null;
            for (Groupe g : groupes) {
                if (g.getEffectif() >= c.tailleMax) continue;
                if (best == null) best = g;
                else {
                    int r1 = g.getNbRedoublants();
                    int r2 = best.getNbRedoublants();
                    if (r1 < r2) best = g;
                    else if (r1 == r2 && g.getEffectif() < best.getEffectif()) best = g;
                }
            }
            if (best == null) throw new GroupingException("Plus de place pour placer un redoublant");
            best.ajouterEtudiant(e);
        }

        // 3) Remplir avec les autres en équilibrant la taille
        for (Etudiant e : autres) {
            Groupe best = null;
            for (Groupe g : groupes) {
                if (g.getEffectif() >= c.tailleMax) continue;
                if (best == null || g.getEffectif() < best.getEffectif()) best = g;
            }
            if (best == null) throw new GroupingException("Plus de place pour placer un étudiant");
            best.ajouterEtudiant(e);
        }

        // 4) Correction pour atteindre tailleMin partout
        corrigerTaillesMin(groupes, c);

        verifierSolution(groupes, etudiants, c);
        return groupes;
    }

    /**
     * Déplace des étudiants depuis des groupes > tailleMin vers des groupes < tailleMin.
     * Ne déplace jamais un étudiant option anglais hors du groupe anglais (index 0).
     */
    static void corrigerTaillesMin(List<Groupe> groupes, ContraintesGroupesS3 c) {
        Groupe gAnglais = groupes.get(0);

        boolean progress;
        do {
            progress = false;

            Groupe low = null;
            for (Groupe g : groupes) {
                if (g.getEffectif() < c.tailleMin) { low = g; break; }
            }
            if (low == null) return;

            // Trouver un donneur
            Groupe donor = null;
            for (Groupe g : groupes) {
                if (g.getEffectif() > c.tailleMin) {
                    // Si g == groupe anglais, on ne pourra donner que des non-anglais.
                    if (g == gAnglais) {
                        boolean hasNonAnglais = false;
                        for (Etudiant e : g.getEtudiants()) if (!e.aOptionAnglais()) { hasNonAnglais = true; break; }
                        if (!hasNonAnglais) continue;
                    }
                    donor = g;
                    break;
                }
            }
            if (donor == null) {
                throw new GroupingException("Impossible d'atteindre tailleMin pour tous les groupes (pas assez d'étudiants déplaçables)");
            }

            // Choisir un étudiant à déplacer (favorise un échange qui améliore l'équilibrage des redoublants)
            Etudiant chosen = choisirEtudiantADonner(donor, low, donor == gAnglais);
            if (chosen == null) throw new GroupingException("Aucun étudiant déplaçable trouvé");

            donor.retirerEtudiant(chosen);
            low.ajouterEtudiant(chosen);
            progress = true;

        } while (progress);
    }

    private static Etudiant choisirEtudiantADonner(Groupe donor, Groupe low, boolean donorIsEnglishGroup) {
        // Si donneur = groupe anglais, on ne peut pas donner les anglais.
        Etudiant best = null;

        int donorRed = donor.getNbRedoublants();
        int lowRed = low.getNbRedoublants();

        // Heuristique :
        // - si low manque de redoublants et donor en a, donner un redoublant
        // - sinon donner un non-redoublant
        boolean preferRed = (lowRed < donorRed);

        for (Etudiant e : donor.getEtudiants()) {
            if (donorIsEnglishGroup && e.aOptionAnglais()) continue;

            if (best == null) best = e;
            else {
                boolean eIsRed = e.isRedoublant();
                boolean bIsRed = best.isRedoublant();

                if (preferRed) {
                    if (eIsRed && !bIsRed) best = e;
                } else {
                    if (!eIsRed && bIsRed) best = e;
                }
            }
        }
        return best;
    }
}
