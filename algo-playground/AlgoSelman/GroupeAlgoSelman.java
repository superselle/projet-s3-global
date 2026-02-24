package AlgoSelman;

import Scolarite.Groupe;
import Utilisateur.Etudiant;
import java.util.List;

// Interface commune pour les algorithmes de génération de groupes
public interface GroupeAlgoSelman {
    List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupesSelman contraintes);
}
