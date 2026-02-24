package AlgoNesrine;

import java.util.Comparator;
import java.util.List;

import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Algo glouton 1 : "equilibrage".
 *
 * Principe (cours) : on fixe un critere glouton simple et on l'applique a chaque choix.
 * Ici, a chaque pack, on le place dans le groupe "le plus vide" tout en restant
 * faisable pour atteindre la mixite.
 */
public class GloutonEquilibrage implements GroupeAlgo {

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

		for (Pack p : packs) {
			fillesRestantes -= p.nbFilles;
			etudiantsRestants -= p.taille;

			Groupe meilleur = null;
			double meilleurScore = Double.POSITIVE_INFINITY;

			// On essaie tous les groupes (choix), on garde le meilleur selon le critere glouton
			for (Groupe g : groupes) {
				if (g.getEffectif() + p.taille > c.tailleMax) continue;

				// Faisabilite minimale : pourra-t-on encore atteindre minFilles dans ce groupe ?
				if (!faisableApresAjout(groupes, g, p, fillesRestantes, etudiantsRestants, c, mixite)) continue;

				double score = scorePlacement(g, p, c, mixite);
				if (score < meilleurScore) {
					meilleurScore = score;
					meilleur = g;
				}
			}

			if (meilleur == null) {
				throw new GroupingUtils.GroupingException("GloutonEquilibrage: aucun placement possible pour un pack (taille=" + p.taille + ", filles=" + p.nbFilles + ")");
			}
			meilleur.ajouterEtudiants(p.membres);
		}

		// Derniere passe : certains groupes peuvent etre < tailleMin si N proche du bas.
		// On "requilibre" en déplaçant des etudiants solo (jamais casser un covoit).
		reparerTaillesMin(groupes, c);

		GroupingUtils.verifierSolution(groupes, c, mixite);
		return groupes;
	}

	/**
	 * Critere glouton (choix local) :
	 * 1) remplir les groupes les moins remplis
	 * 2) réduire le deficit de filles
	 * 3) rapprocher de la taille cible
	 */
	private static double scorePlacement(Groupe g, Pack p, ContraintesGroupes c, GroupingUtils.Mixite mixite) {
		int nApres = g.getEffectif() + p.taille;
		int fApres = g.getNbFilles() + p.nbFilles;
		int deficit = Math.max(0, mixite.minFillesParGroupe - fApres);
		int distCible = Math.abs(nApres - c.tailleCible);
		return (nApres * 10.0) + (deficit * 1000.0) + distCible;
	}

	private static boolean faisableApresAjout(List<Groupe> groupes, Groupe cible, Pack p,
			int fillesRestantes, int etudiantsRestants, ContraintesGroupes c, GroupingUtils.Mixite mixite) {
		int besoinFillesTotal = 0;
		int besoinPlacesMin = 0;
		for (Groupe g : groupes) {
			int n = g.getEffectif();
			int f = g.getNbFilles();
			if (g == cible) {
				n += p.taille;
				f += p.nbFilles;
			}
			besoinFillesTotal += Math.max(0, mixite.minFillesParGroupe - f);
			besoinPlacesMin += Math.max(0, c.tailleMin - n);
		}
		return besoinFillesTotal <= fillesRestantes && besoinPlacesMin <= etudiantsRestants;
	}

	/**
	 * Repare les groupes sous la taille minimale en deplaçant uniquement des étudiants "solo".
	 */
	private static void reparerTaillesMin(List<Groupe> groupes, ContraintesGroupes c) {
		// On boucle tant qu'on trouve un groupe trop petit et un groupe trop grand.
		boolean changement;
		do {
			changement = false;
			Groupe petit = groupes.stream().min(Comparator.comparingInt(Groupe::getEffectif)).orElse(null);
			Groupe grand = groupes.stream().max(Comparator.comparingInt(Groupe::getEffectif)).orElse(null);
			if (petit == null || grand == null) return;
			if (petit.getEffectif() >= c.tailleMin) return;
			if (grand.getEffectif() <= c.tailleMin) return;

			// Cherche un étudiant sans covoit dans le plus grand groupe
			Etudiant aDeplacer = null;
			for (Etudiant e : grand.getEtudiants()) {
				if (e != null && e.getIdCovoiturage() <= 0) {
					aDeplacer = e;
					break;
				}
			}
			if (aDeplacer == null) return; // pas de solo a deplacer


			grand.retirerEtudiant(aDeplacer);
			petit.ajouterEtudiant(aDeplacer);
			changement = true;
		} while (changement);
	}
}
