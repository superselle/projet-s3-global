package Pedagogie;

import java.util.*;

public class Matiere {

	/**
	 * Association Type = Utilisateur.Enseignant
	 */
	ArrayList sesEnseignants;
	/**
	 * Association Type = Pedagogie.Note
	 */
	ArrayList sesNotes;
	/**
	 * Association Type = Scolarite.Groupe
	 */
	ArrayList sonGroupe;
	private int id;
	private String lettre;
	private String nom;
	private int numero;

}