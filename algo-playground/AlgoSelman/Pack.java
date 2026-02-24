package AlgoSelman;

import java.util.ArrayList;
import java.util.List;
import Utilisateur.Etudiant;

// Pack indivisible d'étudiants (ex: covoiturage ou étudiant solo).
// Utiliser quand un ensemble d'étudiants doit rester ensemble lors de
// la répartition dans les groupes.
public class Pack {
    public final int idCovoiturage;
    public final List<Etudiant> membres;
    public final int taille;
    public final int nbFilles;
    public final int nbApprentis;

    public Pack(int idCovoiturage, List<Etudiant> membres) {
        this.idCovoiturage = idCovoiturage;
        this.membres = new ArrayList<>(membres);
        this.taille = membres.size();
        
        int filles = 0, apprentis = 0;
        // Calculer les statistiques du pack (utile pour le tri et le score)
        for (Etudiant e : membres) {
            if (e.isFille()) filles++;
            if (e.isApprenti()) apprentis++;
        }
        this.nbFilles = filles;
        this.nbApprentis = apprentis;
    }

    public boolean contientApprenti() { return nbApprentis > 0; }
    public boolean contientFemme() { return nbFilles > 0; }
}
