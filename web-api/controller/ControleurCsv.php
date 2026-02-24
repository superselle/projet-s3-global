<?php
require_once "controller/ControleurBase.php";
require_once "model/Promotion.php";
require_once "model/Etudiant.php";
require_once "model/Matiere.php";
require_once "model/Note.php";

class ControleurCsv extends ControleurBase {
    public function index() {
        // Vérification des droits
        $this->requireLogin(["responsable_filiere", "responsable_formation"]);
        
        $pid = $_GET["id"] ?? die("ID manquant");
        $msg = "";

        if (isset($_POST["op"]) && $_POST["op"] === "importNotes") {
            if (isset($_FILES["csv"]["tmp_name"]) && is_uploaded_file($_FILES["csv"]["tmp_name"])) {
                $h = fopen($_FILES["csv"]["tmp_name"], "r");
                $head = fgetcsv($h, 0, ";");
                
                if ($head === false) {
                     $msg = "Fichier vide ou illisible.";
                } else {
                    $map = array_flip(array_map("strtolower", array_map("trim", $head)));
                    
                    $colNum = null;
                    if (isset($map["numero_etudiant"])) $colNum = $map["numero_etudiant"];
                    elseif (isset($map["numero"])) $colNum = $map["numero"];

                    if ($colNum !== null && (isset($map["nom"]) || isset($map["prenom"]))) {
                        
                        $cols = [];
                        $ignoreCols = [$colNum, $map["nom"] ?? -1, $map["prenom"] ?? -1];

                        foreach ($head as $i => $v) {
                            if (!in_array($i, $ignoreCols) && trim($v) !== "") {
                                $cols[$i] = trim($v);
                            }
                        }

                        while (($row = fgetcsv($h, 0, ";")) !== false) {
                            $num = intval($row[$colNum] ?? 0);
                            if ($num <= 0) continue;
                            
                            foreach ($cols as $i => $label) {
                                $val = str_replace(",", ".", $row[$i] ?? "");
                                
                                if ($val === "" || !is_numeric($val)) continue;
                                
                                $idM = Matiere::findByNameOrCode($label);
                                
                                if ($idM) {
                                    Note::upsert($num, $idM, floatval($val), null);
                                }
                            }
                        }
                        $msg = "Import terminé.";
                    } else {
                        $msg = "Erreur : La colonne 'numero_etudiant' (ou 'numero') est obligatoire.";
                    }
                }
                fclose($h);
            }
        }

        $this->render("view/commun/csvAffectations.php", ["pid" => $pid, "msg" => $msg, "userRole" => $_SESSION['role'] ?? null]);
    }

    public function exportPromotionMinimum() {
        $this->requireLogin(["enseignant", "responsable_filiere", "responsable_formation"]);
        $this->doExport($_GET["id"], Etudiant::getListePedagogique($_GET["id"]), ["numero_etudiant","nom","prenom","genre","email","type_bac","est_redoublant","groupe"]);
    }

    public function exportPromotionComplete() {
        $this->requireLogin(["responsable_filiere", "responsable_formation"]);
        $d = Etudiant::getExportCompletPromotion($_GET["id"]);
        $this->doExport($_GET["id"], $d["rows"], $d["columns"]);
    }

    private function doExport($pid, $rows, $cols) {
        if (empty($rows)) {
            die("Aucune donnée à exporter.");
        }
        header("Content-Type: text/csv; charset=UTF-8");
        header("Content-Disposition: attachment; filename=\"export_".$pid.".csv\"");
        $out = fopen("php://output", "w");
        fputs($out, "\xEF\xBB\xBF"); 
        fputcsv($out, $cols, ";");
        
        // Mapping colonnes CSV -> propriétés objet Etudiant
        $mapping = [
            'numero_etudiant' => 'id_etudiant',
            'numero' => 'id_etudiant',
            'login' => 'login_utilisateur',
            'genre' => 'genre_utilisateur',
            'telephone' => 'tel_utilisateur',
            'type_bac' => 'libelle_type',
            'mention_bac' => 'libelle_mention',
            'groupe' => 'nom_groupe',
            'parcours' => 'nom_parcours'
        ];
        
        foreach ($rows as $r) {
            $line = [];
            foreach ($cols as $c) {
                // Utiliser le mapping si disponible
                $prop = $mapping[$c] ?? $c;
                
                if (is_object($r)) {
                    $line[] = $r->get($prop) ?? "";
                } else {
                    $line[] = $r[$prop] ?? $r[$c] ?? "";
                }
            }
            fputcsv($out, $line, ";");
        }
        exit;
    }
}