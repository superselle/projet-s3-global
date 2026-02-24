package Scolarite;

import Utilisateur.Etudiant;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

/**
 * Groupe d'?tudiants.
 *
 * Le zip initial avait un export UML (attributs uniquement). Pour les algorithmes,
 * on a besoin :
 * - d'une vraie liste d'?tudiants,
 * - de m?thodes d'ajout,
 * - de m?thodes de comptage (effectif, nb de filles...)
 */
public class Groupe {

	/** Association Type = Scolarite.Parcours */
	ArrayList<Object> sesParcours;
	/** Association Type = Pedagogie.Matiere */
	ArrayList<Object> sesMatieres;

	private int id;
	private String lettre;
	private int anneePromo;
	private int semestre;

	private final List<Etudiant> etudiants = new ArrayList<>();

	public Groupe() {}

	public Groupe(int id) {
		this.id = id;
	}

	public int getId() {
		return id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public List<Etudiant> getEtudiants() {
		return Collections.unmodifiableList(etudiants);
	}

	public void ajouterEtudiant(Etudiant e) {
		if (e == null) {
			throw new IllegalArgumentException("Etudiant null");
		}
		etudiants.add(e);
	}

	public void ajouterEtudiants(List<Etudiant> es) {
		if (es == null) {
			throw new IllegalArgumentException("Liste d'etudiants null");
		}
		for (Etudiant e : es) {
			ajouterEtudiant(e);
		}
	}

	public void retirerEtudiant(Etudiant e) {
		if (e == null) {
			throw new IllegalArgumentException("Etudiant null");
		}
		etudiants.remove(e);
	}

	public void retirerEtudiants(List<Etudiant> es) {
		if (es == null) {
			throw new IllegalArgumentException("Liste d'etudiants null");
		}
		for (Etudiant e : es) {
			retirerEtudiant(e);
		}
	}

	public int getEffectif() {
		return etudiants.size();
	}

	public int getNbFilles() {
		int n = 0;
		for (Etudiant e : etudiants) {
			if (e != null && e.isFille()) n++;
		}
		return n;
	}

	public int getNbGarcons() {
		int n = 0;
		for (Etudiant e : etudiants) {
			if (e != null && e.isGarcon()) n++;
		}
		return n;
	}

	@Override
	public String toString() {
		return "Groupe{" + id + ", effectif=" + getEffectif() + ", filles=" + getNbFilles() + "}";
	}

public int getNbRedoublants() {
    int c = 0;
    for (Etudiant e : etudiants) {
        if (e.isRedoublant()) c++;
    }
    return c;
}

public int getNbApprentis() {
    int c = 0;
    for (Etudiant e : etudiants) {
        if (e.isApprenti()) c++;
    }
    return c;
}

public String getLettre() {
    return lettre;
}

public void setLettre(String lettre) {
    this.lettre = lettre;
}

}