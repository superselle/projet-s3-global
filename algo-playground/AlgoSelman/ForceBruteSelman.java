package AlgoSelman;

import java.util.List;
import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Algorithme Force Brute avec Backtracking.
 * Explore toutes les combinaisons possibles et garde la meilleure.
 * @author Selman
 */
public class ForceBruteSelman implements GroupeAlgoSelman {
    private long maxNoeuds;
    private long noeudsExplores;
    private List<Groupe> meilleureSolution;
    private double meilleurScore;
    private ContraintesGroupesSelman contraintes;

    public ForceBruteSelman() { this.maxNoeuds = 100000; }
    public ForceBruteSelman(long max) { this.maxNoeuds = max; }

    /**
     * Génère les groupes par force brute (backtracking).
     * @param etudiants liste des étudiants à répartir
     * @param c contraintes à respecter
     * @return liste des groupes constituant la meilleure solution
     */
    @Override
    public List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupesSelman c) {
        if (etudiants == null || etudiants.isEmpty()) {
            throw new IllegalArgumentException("Liste d'etudiants vide");
        }
        
        this.noeudsExplores = 0;
        this.meilleureSolution = null;
        this.meilleurScore = Double.MAX_VALUE;
        this.contraintes = c;

        int nbGroupes = GroupingUtilsSelman.choisirNombreDeGroupes(etudiants.size(), c);
        List<Groupe> groupes = GroupingUtilsSelman.initialiserGroupes(nbGroupes);
        List<Pack> packs = GroupingUtilsSelman.construirePacks(etudiants, c);

        explorer(0, packs, groupes);

        if (meilleureSolution == null) {
            throw new IllegalStateException("Aucune solution valide trouvee");
        }
        
        System.out.println("[INFO] ForceBruteSelman : solution trouvée en " + noeudsExplores + " noeuds (score=" + meilleurScore + ")");
        return meilleureSolution;
    }
        /**
         * Explore récursivement les placements de packs (backtracking).
         * @param index index du pack courant à placer
         * @param packs liste des packs
         * @param groupes solution courante (modifiée puis restaurée)
         */
    private void explorer(int index, List<Pack> packs, List<Groupe> groupes) {

        if (noeudsExplores >= maxNoeuds) return;
        noeudsExplores++;

        // Cas de base : tous les packs placés
        if (index == packs.size()) {
            if (GroupingUtilsSelman.verifierSolution(groupes, contraintes)) {
                double score = GroupingUtilsSelman.calculerScore(groupes);
                if (score < meilleurScore) {
                    meilleurScore = score;
                    meilleureSolution = GroupingUtilsSelman.clonerGroupes(groupes);
                }
            }
            return;
        }

        Pack pack = packs.get(index);

        // Essayer chaque groupe
        for (Groupe g : groupes) {
            if (g.getEffectif() + pack.taille > contraintes.tailleMax) continue;

            GroupingUtilsSelman.ajouterPack(g, pack);
            explorer(index + 1, packs, groupes);
            GroupingUtilsSelman.retirerPack(g, pack);
        }
    }
}
