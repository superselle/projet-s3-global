package algo;

import java.util.List;
import modele.Groupe;
import modele.Etudiant;

/** Interface commune aux algorithmes S3. */
public interface GroupeAlgoS3 {
    List<Groupe> generer(List<Etudiant> etudiants, ContraintesGroupesS3 c);
}
