package AlgoNesrine;

/**
 * Contraintes de constitution des groupes (S1).
 */
public final class ContraintesGroupes {
	public final int tailleMin;
	public final int tailleMax;

	/** objectif "confort" (pas une règle) : taille visée */
	public final int tailleCible;

	/** regle : minimum de filles par groupe */
	public final int minFilles;

	/** alternative si minFilles impossible : minimum de filles */
	public final int minFillesFallback;

	/** si on est en mode fallback : nombre de filles doit etre pair */
	public final boolean fallbackFillesPaires;

	/** r?gle : packs covoiturage de taille 2 ou 3 */
	public final int covoitMin;
	public final int covoitMax;

	public ContraintesGroupes(int tailleMin, int tailleMax, int tailleCible,
			int minFilles, int minFillesFallback, boolean fallbackFillesPaires,
			int covoitMin, int covoitMax) {
		if (tailleMin <= 0 || tailleMax < tailleMin) {
			throw new IllegalArgumentException("Tailles de groupe invalides");
		}
		this.tailleMin = tailleMin;
		this.tailleMax = tailleMax;
		this.tailleCible = tailleCible;
		this.minFilles = minFilles;
		this.minFillesFallback = minFillesFallback;
		this.fallbackFillesPaires = fallbackFillesPaires;
		this.covoitMin = covoitMin;
		this.covoitMax = covoitMax;
	}

	/** Param?tres du sujet : S1, groupes 17..20, objectif 18, filles >= 6 sinon >=4 et pair, covoit 2..3 */
	public static ContraintesGroupes s1() {
		return new ContraintesGroupes(
				17, 20, 18,
				6, 4, true,
				2, 3
		);
	}
}
