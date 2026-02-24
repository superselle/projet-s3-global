package Utilisateur;

import Scolarite.Groupe;
import Scolarite.Parcours;
import Scolarite.TypeBac;
import java.util.ArrayList;

/**
 * ?tudiant.
 *
 * Le sujet utilise la notion de "covoiturage" : des ?tudiants doivent rester
 * ensemble (pack de 2 ? 3). On encode cela via idCovoiturage.
 */
public class Etudiant extends Utilisateur {

	/** Association Type = Pedagogie.Note */
	ArrayList<Object> sesNotes;
	Parcours sonParcours;
	TypeBac sonTypeBac;
	/** Association Type = Sondage.Reponse */
	ArrayList<Object> sesReponses;
	Groupe sonGroupe;
	/** Association Type = Scolarite.MentionBac */
	ArrayList<Object> saMentionBac;
	private String statut;
	private boolean estAnglophone;
	private boolean estApprenti;
	private boolean estRedoublant;
	/** 0 = pas de covoiturage, sinon identifiant de pack */
	private int idCovoiturage;

	public Etudiant() {
		super();
	}

	public Etudiant(int id, String prenom, String nom, char genre, int idCovoiturage) {
		super(id, prenom, nom, genre);
		this.idCovoiturage = idCovoiturage;
	}

	public int getIdCovoiturage() {
		return idCovoiturage;
	}

	public void setIdCovoiturage(int idCovoiturage) {
		this.idCovoiturage = idCovoiturage;
	}

	public boolean isFille() {
		return Character.toUpperCase(this.genre) == 'F';
	}

	public boolean isGarcon() {
		return Character.toUpperCase(this.genre) == 'M';
	}

// =========================
// Ajouts pour l'algorithmique (S1/S3)
// =========================

public boolean isRedoublant() {
    return estRedoublant;
}

public void setRedoublant(boolean estRedoublant) {
    this.estRedoublant = estRedoublant;
}

/**
 * Dans les données fournies, nous ne disposons pas d'un champ explicite "optionAnglais".
 * On utilise donc le booléen existant {@code estAnglophone} comme indicateur (alias).
 */
public boolean aOptionAnglais() {
    return estAnglophone;
}

public void setOptionAnglais(boolean optionAnglais) {
    this.estAnglophone = optionAnglais;
}

public boolean isAnglophone() {
    return estAnglophone;
}

public void setAnglophone(boolean estAnglophone) {
    this.estAnglophone = estAnglophone;
}

public boolean isApprenti() {
    return estApprenti;
}

public void setApprenti(boolean estApprenti) {
    this.estApprenti = estApprenti;
}

}