package AlgoNesrine;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

import Utilisateur.Etudiant;

/**
 * Unité indivisible pour la constitution des groupes.
 * - soit un étudiant seul
 * - soit un pack de covoiturage (2..3) qu'on ne doit pas séparer.
 */
final class Pack {
	final int id; // 0 si solo, sinon id covoit
	final List<Etudiant> membres;
	final int taille;
	final int nbFilles;

	Pack(int id, List<Etudiant> membres) {
		this.id = id;
		this.membres = Collections.unmodifiableList(new ArrayList<>(membres));
		this.taille = this.membres.size();
		int f = 0;
		for (Etudiant e : this.membres) {
			if (e != null && e.isFille()) f++;
		}
		this.nbFilles = f;
	}
}
