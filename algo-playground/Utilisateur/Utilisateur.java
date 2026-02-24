package Utilisateur;

import java.util.Date;

/**
 * Classe de base pour les utilisateurs.
 *
 * Le zip initial fournit surtout les attributs (export UML). Pour pouvoir
 * ex?cuter des algorithmes de constitution de groupes, on ajoute ici les
 * constructeurs et getters/setters minimum.
 */
public abstract class Utilisateur {

	protected int id;
	protected String prenom;
	protected String nom;
	protected String mail;
	protected String tel;
	protected String adresse;
	/** 'F' ou 'M' (ou autre si vous d?cidez d'?tendre). */
	protected char genre;
	protected String statut;
	protected Date dateNaissance;
	protected String loginUtilisateur;
	protected String mdpHash;

	public Utilisateur() {
		// constructeur vide (utilis? par certains frameworks / chargements)
	}

	public Utilisateur(int id, String prenom, String nom, char genre) {
		this.id = id;
		this.prenom = prenom;
		this.nom = nom;
		this.genre = genre;
	}

	public int getId() {
		return id;
	}

	public void setId(int id) {
		this.id = id;
	}

	public String getPrenom() {
		return prenom;
	}

	public void setPrenom(String prenom) {
		this.prenom = prenom;
	}

	public String getNom() {
		return nom;
	}

	public void setNom(String nom) {
		this.nom = nom;
	}

	public char getGenre() {
		return genre;
	}

	public void setGenre(char genre) {
		this.genre = genre;
	}

	public String getNomComplet() {
		return (prenom == null ? "" : prenom) + " " + (nom == null ? "" : nom);
	}
}
