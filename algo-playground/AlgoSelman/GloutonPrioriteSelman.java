package AlgoSelman;

import java.util.ArrayList;
import java.util.List;
import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Algorithme Glouton 1 : Distribution Round-Robin par priorité.
 * Distribue les packs cycliquement en priorisant apprentis puis filles.
 * @author Selman
 */
public class GloutonPrioriteSelman implements GroupeAlgoSelman {

    /**
     * Génère les groupes en distribuant les packs par priorité.
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
        
        // Séparer par priorité
        List<Pack> packsApprentis = new ArrayList<>();
        List<Pack> packsFilles = new ArrayList<>();
        List<Pack> packsAutres = new ArrayList<>();
        
        for (Pack p : packs) {
            if (p.contientApprenti()) packsApprentis.add(p);
            else if (p.contientFemme()) packsFilles.add(p);
            else packsAutres.add(p);
        }
        
        // Distribution Round-Robin
        int index = 0;
        
        // D'abord les apprentis
        for (Pack p : packsApprentis) {
            Groupe g = trouverGroupe(groupes, index, p.taille, c.tailleMax);
            if (g != null) {
                GroupingUtilsSelman.ajouterPack(g, p);
                index = (groupes.indexOf(g) + 1) % nbGroupes;
            }
        }
        
        // Puis les filles
        for (Pack p : packsFilles) {
            Groupe g = trouverGroupe(groupes, index, p.taille, c.tailleMax);
            if (g != null) {
                GroupingUtilsSelman.ajouterPack(g, p);
                index = (groupes.indexOf(g) + 1) % nbGroupes;
            }
        }
        
        // Enfin les autres dans le groupe le moins rempli
        for (Pack p : packsAutres) {
            Groupe g = trouverMoinsRempli(groupes, p.taille, c.tailleMax);
            if (g != null) {
                GroupingUtilsSelman.ajouterPack(g, p);
            }
        }
        
        // Réparation si nécessaire
        if (!GroupingUtilsSelman.verifierSolution(groupes, c)) {
            reparer(groupes, c);
        }
        
        return groupes;
    }

    private Groupe trouverGroupe(List<Groupe> groupes, int start, int taille, int max) {
        // Parcours cyclique des groupes à partir de `start`.
        int n = groupes.size();
        for (int i = 0; i < n; i++) {
            Groupe g = groupes.get((start + i) % n);
            // Si le groupe peut accueillir le pack, on le retourne.
            if (g.getEffectif() + taille <= max) return g;
        }
        // Aucun groupe avec assez de place
        return null;
    }

        /**
         * Recherche du groupe le moins rempli pouvant accueillir un pack.
         * @param groupes liste des groupes
         * @param taille taille du pack à placer
         * @param max capacité maximale autorisée pour un groupe
         * @return le groupe le moins rempli pouvant accueillir le pack, ou null
         */
    private Groupe trouverMoinsRempli(List<Groupe> groupes, int taille, int max) {

            Groupe meilleur = null;
        int minEff = Integer.MAX_VALUE;
        for (Groupe g : groupes) {
            // Vérifier capacité puis comparer l'effectif
            if (g.getEffectif() + taille <= max && g.getEffectif() < minEff) {
                minEff = g.getEffectif();
                meilleur = g;
            }
        }
        return meilleur;
    }

    /**
     * Répare la solution en comblant les groupes trop petits.
     * @param groupes liste des groupes (modifiée en place)
     * @param c contraintes (tailleMin, tailleMax, ...)
     *
     * Utilisation: appeler après la distribution initiale si `verifierSolution`
     * retourne false. La méthode tente des déplacements simples sans casser
     * les packs de covoiturage.
     */
    private void reparer(List<Groupe> groupes, ContraintesGroupesSelman c) {
        // Tente de compléter les groupes trop petits en déplaçant des
        // étudiants non-covoiturés depuis des groupes plus fournis.
        for (int iter = 0; iter < 100; iter++) {
            Groupe petit = null;
            // Trouver un groupe sous la taille minimale
            for (Groupe g : groupes) {
                if (g.getEffectif() < c.tailleMin) { petit = g; break; }
            }
            if (petit == null) break; // tout est OK
            
            boolean deplace = false;
            // Chercher un étudiant non-covoituré dans un groupe plus grand
            for (Groupe grand : groupes) {
                if (grand == petit || grand.getEffectif() <= c.tailleMin) continue;
                for (Etudiant e : grand.getEtudiants()) {
                    // idCovoiturage==0 => pas de contrainte de pack
                    if (e.getIdCovoiturage() == 0) {
                        grand.retirerEtudiant(e);
                        petit.ajouterEtudiant(e);
                        deplace = true;
                        break;
                    }
                }
                if (deplace) break;
            }
            if (!deplace) break; // impossible d'améliorer
        }
    }

}
