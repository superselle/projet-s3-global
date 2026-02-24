package algo;

import java.util.*;

import modele.Groupe;
import modele.Etudiant;

import static algo.GroupingUtilsS3.*;

/**
 * Algo glouton "multi-critères" (semestres 3/4/5...)
 * 
 * Objectifs / règles :
 * - Tailles: tailleMin..tailleMax (via ContraintesGroupesS3)
 * - Covoiturage (optionnel): packs indivisibles de taille 2..3
 * - Répartition sexe (optionnelle): objectif d'équilibrage (pas une contrainte dure)
 * - Semestre impair: possibilité de regrouper tous les redoublants dans un seul groupe (index 0)
 * - Semestre pair: possibilité de regrouper tous les "option anglais" dans un seul groupe (index 0)
 */
public final class GloutonMultiCriteresSX {

    private GloutonMultiCriteresSX() {}

    public static List<Groupe> generer(
            List<Etudiant> etudiants,
            ContraintesGroupesS3 c,
            boolean useCovoiturage,
            boolean useRepartitionSexe,
            boolean regrouperRedoublants,
            boolean regrouperOptionAnglais
    ) {
        if (etudiants == null || etudiants.isEmpty()) throw new GroupingException("Aucun Ã©Â©Ã‚Æ’Ã©â€šÃ‚Â©tudiant");
        if (regrouperRedoublants && regrouperOptionAnglais) {
            throw new GroupingException("CritÃ©Â©Ã‚Æ’Ã©â€šÃ‚Â¨res incompatibles: regrouper redoublants ET option anglais");
        }

        int n = etudiants.size();
        int k = choisirNombreDeGroupes(n, c);
        List<Groupe> groupes = initialiserGroupes(k);

        List<Pack> packs = useCovoiturage ? construirePacks(etudiants) : packsSolo(etudiants);

        int totalFilles = 0;
        if (useRepartitionSexe) {
            for (Etudiant e : etudiants) {
                if (e != null && e.isFille()) totalFilles++;
            }
        }
        double targetGirls = useRepartitionSexe ? ((double) totalFilles / k) : 0.0;

        // 1) packs forcés dans le groupe 0
        List<Pack> forced = new ArrayList<>();
        List<Pack> rest = new ArrayList<>();
        for (Pack p : packs) {
            if (isForcedPack(p, regrouperRedoublants, regrouperOptionAnglais)) forced.add(p);
            else rest.add(p);
        }

        Groupe g0 = groupes.get(0);
        for (Pack p : forced) {
            if (g0.getEffectif() + p.taille > c.tailleMax) {
                throw new GroupingException("Impossible: groupe 1 dépasse tailleMax après placement des packs forcés");
            }
            g0.ajouterEtudiants(p.membres);
        }

        // 2) placement glouton des autres packs
        rest.sort(Comparator
                .comparingInt((Pack p) -> p.taille).reversed()
                .thenComparingInt((Pack p) -> p.nbFilles).reversed());

        for (Pack p : rest) {
            int bestIdx = -1;
            double bestCost = Double.POSITIVE_INFINITY;

            for (int i = 0; i < groupes.size(); i++) {
                Groupe g = groupes.get(i);
                if (g.getEffectif() + p.taille > c.tailleMax) continue;

                double cost = costAfterAdd(g, p, c, useRepartitionSexe, targetGirls);

                if (cost < bestCost) {
                    bestCost = cost;
                    bestIdx = i;
                }
            }

            if (bestIdx < 0) throw new GroupingException("Aucun placement possible pour un pack (taille=" + p.taille + ")");
            groupes.get(bestIdx).ajouterEtudiants(p.membres);
        }

        // 3) correction tailleMin (déplacer uniquement des solos, et jamais sortir les forcés du groupe 0)
        corrigerTaillesMin(groupes, c, regrouperRedoublants, regrouperOptionAnglais);

        // 4) vérifs basiques (unicité + tailles + contrainte regroupement)
        verifier(groupes, etudiants, c, regrouperRedoublants, regrouperOptionAnglais);

        return groupes;
    }

    private static double costAfterAdd(Groupe g, Pack p, ContraintesGroupesS3 c, boolean useSex, double targetGirls) {
        int newSize = g.getEffectif() + p.taille;
        double costSize = Math.abs(newSize - c.tailleCible);

        if (!useSex) {
            return costSize;
        }

        int girlsAfter = g.getNbFilles() + p.nbFilles;
        double costGirls = Math.abs(girlsAfter - targetGirls);

        return costSize + 2.0 * costGirls;
    }

    private static boolean isForcedPack(Pack p, boolean regrouperRed, boolean regrouperAnglais) {
        if (!regrouperRed && !regrouperAnglais) return false;
        for (Etudiant e : p.membres) {
            if (e == null) continue;
            if (regrouperRed && e.isRedoublant()) return true;
            if (regrouperAnglais && e.aOptionAnglais()) return true;
        }
        return false;
    }

    private static void verifier(List<Groupe> groupes, List<Etudiant> tous, ContraintesGroupesS3 c,
                                 boolean regrouperRed, boolean regrouperAnglais) {
        // tailles
        for (int i = 0; i < groupes.size(); i++) {
            int n = groupes.get(i).getEffectif();
            if (n < c.tailleMin || n > c.tailleMax) {
                throw new GroupingException("Taille invalide groupe " + (i + 1) + ": " + n);
            }
        }

        // unicité
        Set<Etudiant> seen = new HashSet<>();
        for (Groupe g : groupes) {
            for (Etudiant e : g.getEtudiants()) {
                if (!seen.add(e)) throw new GroupingException("Étudiant dupliqué: " + e.getNomComplet());
            }
        }
        if (seen.size() != tous.size()) {
            throw new GroupingException("Solution incomplète: " + seen.size() + "/" + tous.size());
        }

        // regroupement en groupe 0
        if (regrouperRed) {
            for (int i = 1; i < groupes.size(); i++) {
                for (Etudiant e : groupes.get(i).getEtudiants()) {
                    if (e.isRedoublant()) throw new GroupingException("Redoublants répartis sur plusieurs groupes");
                }
            }
        }
        if (regrouperAnglais) {
            for (int i = 1; i < groupes.size(); i++) {
                for (Etudiant e : groupes.get(i).getEtudiants()) {
                    if (e.aOptionAnglais()) throw new GroupingException("Option anglais répartie sur plusieurs groupes");
                }
            }
        }
    }

    private static void corrigerTaillesMin(List<Groupe> groupes, ContraintesGroupesS3 c,
                                          boolean regrouperRed, boolean regrouperAnglais) {
        Groupe g0 = groupes.get(0);

        boolean progress;
        do {
            progress = false;

            Groupe low = null;
            for (Groupe g : groupes) {
                if (g.getEffectif() < c.tailleMin) { low = g; break; }
            }
            if (low == null) return;

            Groupe donor = null;
            for (Groupe g : groupes) {
                if (g.getEffectif() > c.tailleMin) {
                    donor = g;
                    break;
                }
            }
            if (donor == null) throw new GroupingException("Impossible d'atteindre tailleMin partout");

            // déplacer un étudiant solo (covoit <= 0) ; si donor == g0, ne pas déplacer un forcé
            Etudiant chosen = null;
            for (Etudiant e : donor.getEtudiants()) {
                if (e == null) continue;
                if (e.getIdCovoiturage() > 0) continue;
                if (donor == g0) {
                    if (regrouperRed && e.isRedoublant()) continue;
                    if (regrouperAnglais && e.aOptionAnglais()) continue;
                }
                chosen = e;
                break;
            }
            if (chosen == null) throw new GroupingException("Aucun étudiant déplaçable (solo) pour corriger tailleMin");

            donor.retirerEtudiant(chosen);
            low.ajouterEtudiant(chosen);
            progress = true;
        } while (progress);
    }

    private static List<Pack> packsSolo(List<Etudiant> etudiants) {
        List<Pack> res = new ArrayList<>();
        for (Etudiant e : etudiants) {
            if (e == null) continue;
            res.add(new Pack(0, List.of(e)));
        }
        return res;
    }

    private static List<Pack> construirePacks(List<Etudiant> etudiants) {
        Map<Integer, List<Etudiant>> covoit = new HashMap<>();
        List<Pack> res = new ArrayList<>();

        for (Etudiant e : etudiants) {
            if (e == null) continue;
            int id = e.getIdCovoiturage();
            if (id <= 0) {
                res.add(new Pack(0, List.of(e)));
            } else {
                covoit.computeIfAbsent(id, _k -> new ArrayList<>()).add(e);
            }
        }

        for (Map.Entry<Integer, List<Etudiant>> entry : covoit.entrySet()) {
            int id = entry.getKey();
            List<Etudiant> membres = entry.getValue();
            // règles du sujet: 2..3
            if (membres.size() < 2 || membres.size() > 3) {
                throw new GroupingException("Covoiturage " + id + " invalide: taille=" + membres.size() + " (attendu 2..3)");
            }
            res.add(new Pack(id, membres));
        }

        return res;
    }
}
