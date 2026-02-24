package AlgoNesrine;

import java.util.ArrayList;
import java.util.Comparator;
import java.util.List;

import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Algo glouton 2 : "filles d'abord".
 *
 * Critére glouton : on satisfait au plus tôt la contrainte de mixite (min de filles),
 * puis on complete en équilibrant les tailles.
 */
public class GloutonFillesDAbord implements GroupeAlgo {

	@Override
	public List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupes c) {
		int n = etudiants.size();
		int k = GroupingUtils.choisirNombreDeGroupes(n, c);
		int totalFilles = (int) etudiants.stream().filter(e -> e != null && e.isFille()).count();
		GroupingUtils.Mixite mixite = GroupingUtils.choisirModeMixite(totalFilles, k, c);

		List<Pack> packs = new ArrayList<>(GroupingUtils.construirePacks(etudiants, c));
		List<Groupe> groupes = GroupingUtils.initialiserGroupes(k);

		// 1) Phase "mixite" : donner des packs riches en filles aux groupes en déficit.
		for (Groupe g : groupes) {
			while (g.getNbFilles() < mixite.minFillesParGroupe) {
				Pack choix = choisirPackPourFilles(packs, g, c);
				if (choix == null) {
					throw new GroupingUtils.GroupingException("GloutonFillesDAbord: impossible d'atteindre la mixit? dans un groupe");
				}
				g.ajouterEtudiants(choix.membres);
				packs.remove(choix);
			}
		}

		// 2) Phase "remplissage" : on met chaque pack dans le groupe le moins rempli.
		packs.sort(Comparator
				.comparingInt((Pack p) -> p.taille).reversed()
				.thenComparingInt((Pack p) -> p.nbFilles).reversed());

		for (Pack p : packs) {
			Groupe g = groupes.stream()
					.filter(gr -> gr.getEffectif() + p.taille <= c.tailleMax)
					.min(Comparator.comparingInt(Groupe::getEffectif))
					.orElse(null);
			if (g == null) {
				throw new GroupingUtils.GroupingException("GloutonFillesDAbord: aucun groupe ne peut accueillir un pack (taille=" + p.taille + ")");
			}
			g.ajouterEtudiants(p.membres);
		}

		// 3) Ajustement optionnel en mode fallback "filles paires".
		if (mixite.fallback && mixite.fillesPaires) {
			corrigerPariteFilles(groupes, c, mixite);
		}

		GroupingUtils.verifierSolution(groupes, c, mixite);
		return groupes;
	}

	/** Choisit le pack (qui rentre) maximisant nbFilles, puis ratio nbFilles/taille. */
	private static Pack choisirPackPourFilles(List<Pack> packs, Groupe g, ContraintesGroupes c) {
		Pack meilleur = null;
		double meilleurScore = Double.NEGATIVE_INFINITY;
		for (Pack p : packs) {
			if (g.getEffectif() + p.taille > c.tailleMax) continue;
			// pack utile si au moins 1 fille, sinon il n'aide pas la phase 1
			if (p.nbFilles <= 0) continue;
			double score = (p.nbFilles * 10.0) + (p.nbFilles / (double) p.taille);
			if (score > meilleurScore) {
				meilleurScore = score;
				meilleur = p;
			}
		}
		return meilleur;
	}


	private static void corrigerPariteFilles(List<Groupe> groupes, ContraintesGroupes c, GroupingUtils.Mixite mixite) {
		Groupe impair = groupes.stream().filter(g -> g.getNbFilles() % 2 != 0).findFirst().orElse(null);
		if (impair == null) return;
		Groupe pair = groupes.stream().filter(g -> g.getNbFilles() % 2 == 0).findFirst().orElse(null);
		if (pair == null) return;

		Etudiant filleSolo = null;
		for (Etudiant e : impair.getEtudiants()) {
			if (e != null && e.isFille() && e.getIdCovoiturage() <= 0) {
				filleSolo = e;
				break;
			}
		}
		if (filleSolo == null) return;
		if (pair.getEffectif() + 1 > c.tailleMax) return;

		impair.retirerEtudiant(filleSolo);
		pair.ajouterEtudiant(filleSolo);

		// on re-verifie la mixite minimum
		if (impair.getNbFilles() < mixite.minFillesParGroupe) {
			// annule si on casse le min
			pair.retirerEtudiant(filleSolo);
			impair.ajouterEtudiant(filleSolo);
		}
	}
}
