package AlgoNesrine;

import java.util.ArrayList;
import java.util.List;

import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Algo force brute (cours) : on essaie toutes les possibilites (tous les choix)
 * et on garde la meilleure solution selon un critere d'optimisation.
 *
 * Implementation : backtracking (recursif) sur les packs indivisibles.
 */
public class ForceBruteBacktracking implements GroupeAlgo {
	private final long maxNoeuds;
	private long noeuds;

	public ForceBruteBacktracking() {
		this(2_000_000);
	}

	public ForceBruteBacktracking(long maxNoeuds) {
		this.maxNoeuds = maxNoeuds;
	}

	@Override
	public List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupes c) {
		int n = etudiants.size();
		int k = GroupingUtils.choisirNombreDeGroupes(n, c);
		int totalFilles = (int) etudiants.stream().filter(e -> e != null && e.isFille()).count();
		GroupingUtils.Mixite mixite = GroupingUtils.choisirModeMixite(totalFilles, k, c);

		List<Pack> packs = GroupingUtils.construirePacks(etudiants, c);
		List<Groupe> groupes = GroupingUtils.initialiserGroupes(k);

		int fillesRestantes = packs.stream().mapToInt(p -> p.nbFilles).sum();
		int etudiantsRestants = packs.stream().mapToInt(p -> p.taille).sum();

		noeuds = 0;
		Best best = new Best();
		backtrack(0, packs, groupes, fillesRestantes, etudiantsRestants, totalFilles, n, c, mixite, best);

		if (best.meilleure == null) {
			throw new GroupingUtils.GroupingException("ForceBrute: aucune solution trouv?e (ou limite de noeuds atteinte)");
		}
		GroupingUtils.verifierSolution(best.meilleure, c, mixite);
		return best.meilleure;
	}

	private static final class Best {
		double score = Double.POSITIVE_INFINITY;
		List<Groupe> meilleure = null;
	}

	private void backtrack(int idx, List<Pack> packs, List<Groupe> groupes,
			int fillesRestantes, int etudiantsRestants,
			int totalFilles, int totalEtudiants,
			ContraintesGroupes c, GroupingUtils.Mixite mixite, Best best) {
		noeuds++;
		if (noeuds > maxNoeuds) return;

		if (idx >= packs.size()) {
			if (!solutionValide(groupes, c, mixite)) return;
			double sc = GroupingUtils.score(groupes, totalFilles, totalEtudiants, c);
			if (sc < best.score) {
				best.score = sc;
				best.meilleure = copierGroupes(groupes);
			}
			return;
		}

		// pruning :
		// 1) si certains groupes ne peuvent plus atteindre le min de filles
		// 2) si certains groupes ne peuvent plus atteindre la tailleMin (pas assez de places restantes)
		if (!faisable(groupes, fillesRestantes, etudiantsRestants, c, mixite)) return;

		Pack p = packs.get(idx);
		// On consomme le pack "en avance" (comme dans N(k) du cours : on fait un choix puis on rappelle)
		int fillesApres = fillesRestantes - p.nbFilles;
		int etudiantsApres = etudiantsRestants - p.taille;

		for (Groupe g : groupes) {
			if (g.getEffectif() + p.taille > c.tailleMax) continue;
			g.ajouterEtudiants(p.membres);
			backtrack(idx + 1, packs, groupes, fillesApres, etudiantsApres, totalFilles, totalEtudiants, c, mixite, best);
			g.retirerEtudiants(p.membres);
		}
	}

	private static boolean faisable(List<Groupe> groupes, int fillesRestantes, int etudiantsRestants,
			ContraintesGroupes c, GroupingUtils.Mixite mixite) {
		int besoinFillesTotal = 0;
		int besoinPlacesMin = 0;
		for (Groupe g : groupes) {
			besoinFillesTotal += Math.max(0, mixite.minFillesParGroupe - g.getNbFilles());
			besoinPlacesMin += Math.max(0, c.tailleMin - g.getEffectif());
		}
		return besoinFillesTotal <= fillesRestantes && besoinPlacesMin <= etudiantsRestants;
	}

	private static boolean solutionValide(List<Groupe> groupes, ContraintesGroupes c, GroupingUtils.Mixite mixite) {
		for (Groupe g : groupes) {
			int n = g.getEffectif();
			if (n < c.tailleMin || n > c.tailleMax) return false;
			int f = g.getNbFilles();
			if (f < mixite.minFillesParGroupe) return false;
			if (mixite.fallback && mixite.fillesPaires && (f % 2 != 0)) return false;
		}
		return true;
	}

	private static List<Groupe> copierGroupes(List<Groupe> groupes) {
		List<Groupe> copy = new ArrayList<>();
		for (Groupe g : groupes) {
			Groupe ng = new Groupe(g.getId());
			ng.ajouterEtudiants(new ArrayList<>(g.getEtudiants()));
			copy.add(ng);
		}
		return copy;
	}
}
