package AlgoNesrine;

import java.util.List;

import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Interface des générateurs de groupes.
 */
public interface GroupeAlgo {
	List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupes contraintes);
}
