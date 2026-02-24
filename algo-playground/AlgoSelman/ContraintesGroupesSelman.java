package AlgoSelman;

// Configuration des contraintes pour la répartition en groupes
public final class ContraintesGroupesSelman {
    public final int tailleMin;
    public final int tailleMax;
    public final int tailleCible;
    public final int minApprentisParGroupe;
    public final int covoitMin;
    public final int covoitMax;

    public ContraintesGroupesSelman(int tailleMin, int tailleMax, int tailleCible,
                                     int minApprentisParGroupe, int covoitMin, int covoitMax) {
        this.tailleMin = tailleMin;
        this.tailleMax = tailleMax;
        this.tailleCible = tailleCible;
        this.minApprentisParGroupe = minApprentisParGroupe;
        this.covoitMin = covoitMin;
        this.covoitMax = covoitMax;
    }

    // Configuration par défaut pour S2
    public static ContraintesGroupesSelman s2() {
        return new ContraintesGroupesSelman(17, 20, 18, 1, 2, 3);
    }

    @Override
    public String toString() {
        return "Contraintes{taille=" + tailleMin + ".." + tailleMax + ", minApprentis=" + minApprentisParGroupe + "}";
    }
}
