package AlgoNesrine;

import java.util.ArrayList;
import java.util.List;

import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Demo console minimaliste.
 */
public class Demo {
	public static void main(String[] args) {
		ContraintesGroupes c = ContraintesGroupes.s1();
		List<Etudiant> etudiants = genererFauxEtudiants();

		List<GroupeAlgo> algos = List.of(
				new GloutonEquilibrage(),
				new GloutonFillesDAbord(),
				new ForceBruteBacktracking(300_000)
		);

		for (GroupeAlgo algo : algos) {
			System.out.println("\n=== " + algo.getClass().getSimpleName() + " ===");
			try {
				List<Groupe> groupes = algo.generer(etudiants, c);
				for (Groupe g : groupes) {
					System.out.println(g);
				}
			} catch (RuntimeException e) {
				System.out.println("Erreur: " + e.getMessage());
			}
		}
	}

	private static List<Etudiant> genererFauxEtudiants() {
		List<Etudiant> res = new ArrayList<>();
		// 54 ?tudiants -> 3 groupes de 18
		// 18 filles, 36 gar?ons
		for (int i = 1; i <= 18; i++) {
			res.add(new Etudiant(i, "F" + i, "Test", 'F', 0));
		}
		for (int i = 19; i <= 54; i++) {
			res.add(new Etudiant(i, "G" + i, "Test", 'M', 0));
		}
		// Ajout de quelques covoiturages (2 et 3)
		res.get(0).setIdCovoiturage(10);
		res.get(1).setIdCovoiturage(10);
		res.get(2).setIdCovoiturage(11);
		res.get(3).setIdCovoiturage(11);
		res.get(4).setIdCovoiturage(11);
		return res;
	}
}
