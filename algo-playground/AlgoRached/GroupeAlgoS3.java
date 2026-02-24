package AlgoRached;

import java.util.List;
import Scolarite.Groupe;
import Utilisateur.Etudiant;

/** Interface commune aux algorithmes S3. */
public interface GroupeAlgoS3 {
    List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupesS3 c);
}
