package AlgoRached;

import java.util.ArrayList;
import java.util.List;

import Scolarite.Groupe;
import Utilisateur.Etudiant;

/**
 * Démo simple (console) pour S3.
 * À adapter selon votre source de données.
 */
public class DemoS3 {
    public static void main(String[] args) {
        List<Etudiant> etudiants = new ArrayList<>();

        for (int i = 1; i <= 54; i++) {
            Etudiant e = new Etudiant(i, "Prenom" + i, "Nom" + i, (i % 2 == 0) ? 'F' : 'M', 0);
            e.setRedoublant(i % 7 == 0);
            e.setOptionAnglais(i % 9 == 0);
            etudiants.add(e);
        }

        ContraintesGroupesS3 c = ContraintesGroupesS3.s3();

        GroupeAlgoS3 algo = new GloutonRedoublantsEquilibre();
        List<Groupe> groupes = algo.generer(etudiants, c);

        System.out.println("Groupes générés: " + groupes.size());
        for (Groupe g : groupes) {
            System.out.println("Groupe " + g.getId() + " : effectif=" + g.getEffectif()
                    + ", redoublants=" + g.getNbRedoublants()
                    + ", anglais=" + g.getEtudiants().stream().filter(Etudiant::aOptionAnglais).count());
        }
        System.out.println("Score: " + GroupingUtilsS3.score(groupes, c));
    }
}
