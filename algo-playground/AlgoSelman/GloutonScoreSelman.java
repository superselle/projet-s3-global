package AlgoSelman;

import java.util.List;
import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Algorithme Glouton 2 : Optimisation du score (Best Fit).
 * Chaque pack est placé dans le groupe qui minimise le score global.
 * @author Selman
 */
public class GloutonScoreSelman implements GroupeAlgoSelman {

    /**
     * Génère les groupes en minimisant le score à chaque placement.
     * @param etudiants liste des étudiants à répartir
     * @param c contraintes à respecter
     * @return liste des groupes constitués
     */
    @Override
    public List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupesSelman c) {
        if (etudiants == null || etudiants.isEmpty()) {
            throw new IllegalArgumentException("Liste d'etudiants vide");
        }
        
        int nbGroupes = GroupingUtilsSelman.choisirNombreDeGroupes(etudiants.size(), c);
        List<Groupe> groupes = GroupingUtilsSelman.initialiserGroupes(nbGroupes);
        List<Pack> packs = GroupingUtilsSelman.construirePacks(etudiants, c);

        // Pour chaque pack, on choisit le groupe qui minimise le score
        for (Pack pack : packs) {
            Groupe meilleurGroupe = null;
            double meilleurScore = Double.MAX_VALUE;

            for (Groupe g : groupes) {
                if (g.getEffectif() + pack.taille > c.tailleMax) continue;

                // Simuler l'ajout
                GroupingUtilsSelman.ajouterPack(g, pack);
                double score = GroupingUtilsSelman.calculerScore(groupes);
                
                if (score < meilleurScore) {
                    meilleurScore = score;
                    meilleurGroupe = g;
                } else if (score == meilleurScore && meilleurGroupe != null) {
                    if (g.getEffectif() < meilleurGroupe.getEffectif()) {
                        meilleurGroupe = g;
                    }
                }
                
                // Retirer pour tester le suivant
                GroupingUtilsSelman.retirerPack(g, pack);
            }

            if (meilleurGroupe != null) {
                GroupingUtilsSelman.ajouterPack(meilleurGroupe, pack);
            }
        }

        // Réparation si nécessaire
        if (!GroupingUtilsSelman.verifierSolution(groupes, c)) {
            reparer(groupes, c);
        }

        return groupes;
    }

    /**
     * Répare la solution en comblant les groupes sous la taille minimale.
     * @param groupes liste des groupes (modifiée en place)
     * @param c contraintes (tailleMin, tailleMax, ...)
     *
     * Utilisation: appeler si `GroupingUtilsSelman.verifierSolution` échoue
     * après la phase de placement. Effectue des déplacements simples.
     */
    private void reparer(List<Groupe> groupes, ContraintesGroupesSelman c) {
        for (int iter = 0; iter < 100; iter++) {
            Groupe petit = null;
            // repérer un groupe en dessous de la taille minimale
            for (Groupe g : groupes) {
                if (g.getEffectif() < c.tailleMin) { petit = g; break; }
            }
            if (petit == null) break; // solution valide
            
            boolean deplace = false;
            // tenter de trouver un étudiant non-covoituré à déplacer
            for (Groupe grand : groupes) {
                if (grand == petit || grand.getEffectif() <= c.tailleMin) continue;
                for (Etudiant e : grand.getEtudiants()) {
                    // on évite de casser des packs
                    if (e.getIdCovoiturage() == 0) {
                        grand.retirerEtudiant(e);
                        petit.ajouterEtudiant(e);
                        deplace = true;
                        break;
                    }
                }
                if (deplace) break;
            }
            if (!deplace) break; // pas de mouvement possible
        }
    }

}
