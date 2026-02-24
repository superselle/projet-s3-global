package algo;

/**
 * Contraintes de constitution des groupes (S3).
 *
 * Règles :
 * - tailleMin..tailleMax (17..20)
 * - tous les étudiants "option anglais" dans un même groupe (mais le groupe peut contenir d'autres étudiants)
 *
 * Optimisation :
 * - répartir équitablement les redoublants
 */
public final class ContraintesGroupesS3 {
    public final int tailleMin;
    public final int tailleMax;
    public final int tailleCible;

    /** Poids de l'équilibrage des redoublants dans le score d'optimisation. */
    public final double poidsRedoublants;

    public ContraintesGroupesS3(int tailleMin, int tailleMax, int tailleCible, double poidsRedoublants) {
        this.tailleMin = tailleMin;
        this.tailleMax = tailleMax;
        this.tailleCible = tailleCible;
        this.poidsRedoublants = poidsRedoublants;
    }

    public static ContraintesGroupesS3 s3() {
        return new ContraintesGroupesS3(17, 20, 18, 5.0);
    }
}
