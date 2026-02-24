package AlgoNesrine;

import java.util.ArrayList;
import java.util.Comparator;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Fonctions utilitaires partagées par les algorithmes.
 */
public final class GroupingUtils {
	private GroupingUtils() {}

	/** Exception dédiée aux cas "pas de solution" / données invalides. */
	public static class GroupingException extends RuntimeException {
		public GroupingException(String message) { super(message); }
	}

	/**
	 * Calcule k (nb de groupes) tel que k*min <= N <= k*max.
	 * Si plusieurs k possibles, on choisit celui dont la taille moyenne est la plus proche de la cible.
	 */
	public static int choisirNombreDeGroupes(int nEtudiants, ContraintesGroupes c) {
		if (nEtudiants <= 0) throw new GroupingException("Aucun ?tudiant");
		List<Integer> candidats = new ArrayList<>();
		for (int k = 1; k <= nEtudiants; k++) {
			if (k * c.tailleMin <= nEtudiants && nEtudiants <= k * c.tailleMax) {
				candidats.add(k);
			}
		}
		if (candidats.isEmpty()) {
			throw new GroupingException("Impossible: " + nEtudiants + " ?tudiants ne peuvent pas ?tre r?partis en groupes de " + c.tailleMin + ".." + c.tailleMax);
		}
		return candidats.stream()
				.min(Comparator.comparingDouble(k -> Math.abs((nEtudiants / (double) k) - c.tailleCible)))
				.orElseThrow();
	}

	/**
	 * D?termine la régle de mixité réellement applicable.
	 * - Si total filles >= k*minFilles -> minFilles
	 * - sinon si total filles >= k*minFillesFallback -> fallback
	 * - sinon -> impossible
	 */
	public static Mixite choisirModeMixite(int totalFilles, int k, ContraintesGroupes c) {
		if (totalFilles >= k * c.minFilles) {
			return new Mixite(c.minFilles, false, false);
		}
		if (totalFilles >= k * c.minFillesFallback) {
			return new Mixite(c.minFillesFallback, true, c.fallbackFillesPaires);
		}
		throw new GroupingException("Impossible: pas assez de filles pour satisfaire la mixit? (" + totalFilles + " filles pour " + k + " groupes)");
	}

	/**
	 * Construit les packs indivisibles a partir des étudiants.
	 * Vérifie que les packs covoiturage ont une taille dans [covoitMin..covoitMax].
	 */
	static List<Pack> construirePacks(List<Etudiant> etudiants, ContraintesGroupes c) {
		Map<Integer, List<Etudiant>> covoit = new HashMap<>();
		List<Pack> packs = new ArrayList<>();
		for (Etudiant e : etudiants) {
			if (e == null) continue;
			int id = e.getIdCovoiturage();
			if (id <= 0) {
				packs.add(new Pack(0, List.of(e)));
			} else {
				covoit.computeIfAbsent(id, _k -> new ArrayList<>()).add(e);
			}
		}
		for (Map.Entry<Integer, List<Etudiant>> entry : covoit.entrySet()) {
			int id = entry.getKey();
			List<Etudiant> membres = entry.getValue();
			if (membres.size() < c.covoitMin || membres.size() > c.covoitMax) {
				throw new GroupingException("Covoiturage " + id + " invalide: taille=" + membres.size() + " (attendu " + c.covoitMin + ".." + c.covoitMax + ")");
			}
			packs.add(new Pack(id, membres));
		}
		packs.sort(Comparator
				.comparingInt((Pack p) -> p.taille).reversed()
				.thenComparingInt((Pack p) -> p.nbFilles).reversed());
		return packs;
	}

	static List<Groupe> initialiserGroupes(int k) {
		List<Groupe> groupes = new ArrayList<>();
		for (int i = 0; i < k; i++) {
			groupes.add(new Groupe(i + 1));
		}
		return groupes;
	}

	/**
	 * Vérifie la validité finale des groupes (régles).
	 */
	public static void verifierSolution(List<Groupe> groupes, ContraintesGroupes c, Mixite mixite) {
		for (Groupe g : groupes) {
			int n = g.getEffectif();
			if (n < c.tailleMin || n > c.tailleMax) {
				throw new GroupingException("Taille de groupe invalide: " + n);
			}
			int f = g.getNbFilles();
			if (f < mixite.minFillesParGroupe) {
				throw new GroupingException("Mixit? invalide: " + f + " filles (min " + mixite.minFillesParGroupe + ")");
			}
			if (mixite.fallback && mixite.fillesPaires && (f % 2 != 0)) {
				throw new GroupingException("Mixit? invalide en mode fallback: nb de filles impair (" + f + ")");
			}
		}
	}

	/**
	 * Petite structure: mode de mixité sélectionné.
	 */
	public static final class Mixite {
		public final int minFillesParGroupe;
		public final boolean fallback;
		public final boolean fillesPaires;
		public Mixite(int minFillesParGroupe, boolean fallback, boolean fillesPaires) {
			this.minFillesParGroupe = minFillesParGroupe;
			this.fallback = fallback;
			this.fillesPaires = fillesPaires;
		}
	}

	/**
	 * Score (critére d'optimisation) : plus petit est meilleur.
	 *
	 * - privilégier des groupes proches de la taille cible
	 * - et une répartition des filles équilibrée (proche de l'idéal)
	 */
	static double score(List<Groupe> groupes, int totalFilles, int totalEtudiants, ContraintesGroupes c) {
		int k = groupes.size();
		double idealSize = totalEtudiants / (double) k;
		double idealGirls = totalFilles / (double) k;
		double s = 0.0;
		for (Groupe g : groupes) {
			double ds = g.getEffectif() - idealSize;
			double df = g.getNbFilles() - idealGirls;
			s += ds * ds + df * df;
			double dc = g.getEffectif() - c.tailleCible;
			s += 0.25 * dc * dc;
		}
		return s;
	}
}
