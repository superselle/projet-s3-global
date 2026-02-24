<?php
// Génère les données UTILISATEUR pour les étudiants S4 (P_A/P_B/P_C)
// à partir de info-bd/s4-etc.txt (INSERT INTO ETUDIANT ...).
//
// Sortie :
// - scripts/seed_utilisateurs_data_s4.generated.php (tableau PHP)
//
// Option :
// - --sql : génère aussi info-bd/utilisateurs-s4.sql

$sourceFile = __DIR__ . '/../info-bd/s4-etc.txt';
$outPhp = __DIR__ . '/seed_utilisateurs_data_s4.generated.php';
$outSql = __DIR__ . '/../info-bd/utilisateurs-s4.sql';

$generateSql = false;
if (isset($argv) && is_array($argv)) {
    $generateSql = in_array('--sql', $argv, true);
}

if (!file_exists($sourceFile)) {
    echo "Fichier introuvable: $sourceFile\n";
    exit;
}

$sqlText = file_get_contents($sourceFile);
if ($sqlText === false) {
    echo "Impossible de lire: $sourceFile\n";
    exit;
}

function pickFromList($list, $seed) {
    $n = count($list);
    if ($n === 0) {
        return '';
    }
    return $list[$seed % $n];
}

function studentGender($id) {
    return ($id % 2 === 0) ? 'Homme' : 'Femme';
}

function studentPhone9Digits($id) {
    // Tel stocké comme int (ex: 611223344), sans le 0 initial.
    $v = ($id * 7919) % 100000000;
    $digits = str_pad($v, 8, '0', STR_PAD_LEFT);
    return '6' . $digits;
}

function studentBirthDate($id) {
    // Etudiants ~2004-2006, dates valides (jour 1..28)
    $year = 2004 + ($id % 3);
    $month = 1 + (($id * 7) % 12);
    $day = 1 + (($id * 13) % 28);
    return sprintf('%04d-%02d-%02d', $year, $month, $day);
}

function studentAddress($id) {
    $streets = [
        'Rue de la Gare',
        'Avenue de la République',
        'Boulevard des Sciences',
        'Rue Victor Hugo',
        'Rue des Lilas',
        'Allée des Érables',
        'Rue du Code',
        'Avenue du Web',
        'Impasse SQL',
        'Rue des Étudiants',
    ];
    $cities = [
        'Nantes',
        'Angers',
        'Rennes',
        'Le Mans',
        'Tours',
        'Brest',
        'Poitiers',
        'Caen',
    ];

    $number = 1 + ($id % 98);
    $street = pickFromList($streets, $id);
    $city = pickFromList($cities, $id * 3);
    return $number . ' ' . $street . ', ' . $city;
}

function normalizeEmailPart($s) {
    $map = [
        'À' => 'A', 'Â' => 'A', 'Ä' => 'A', 'Á' => 'A', 'Ã' => 'A',
        'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'ã' => 'a',
        'Ç' => 'C', 'ç' => 'c',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'Î' => 'I', 'Ï' => 'I', 'Í' => 'I',
        'î' => 'i', 'ï' => 'i', 'í' => 'i',
        'Ô' => 'O', 'Ö' => 'O', 'Ó' => 'O', 'Õ' => 'O',
        'ô' => 'o', 'ö' => 'o', 'ó' => 'o', 'õ' => 'o',
        'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ú' => 'U',
        'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
        'Ÿ' => 'Y', 'ÿ' => 'y',
        'Ñ' => 'N', 'ñ' => 'n',
        'Æ' => 'AE', 'æ' => 'ae',
        'Œ' => 'OE', 'œ' => 'oe',
    ];
    $s = strtr($s, $map);
    $s = strtolower($s);
    $s = str_replace(["'", '’'], '', $s);
    $s = str_replace([' ', '-'], '.', $s);
    $s = preg_replace('/[^a-z0-9.]/', '', $s);
    $s = preg_replace('/\.+/', '.', $s);
    $s = trim($s, '.');
    return $s;
}

function parseSqlInsertValues($valuesPart) {
    $tuples = [];

    $inString = false;
    $depth = 0;
    $start = null;

    $len = strlen($valuesPart);
    for ($i = 0; $i < $len; $i++) {
        $ch = $valuesPart[$i];

        if ($ch === "'") {
            if ($inString) {
                $next = ($i + 1 < $len) ? $valuesPart[$i + 1] : '';
                if ($next === "'") {
                    $i++;
                    continue;
                }
                $inString = false;
                continue;
            }
            $inString = true;
            continue;
        }

        if ($inString) {
            continue;
        }

        if ($ch === '(') {
            if ($depth === 0) {
                $start = $i + 1;
            }
            $depth++;
            continue;
        }

        if ($ch === ')') {
            $depth--;
            if ($depth === 0 && $start !== null) {
                $tuples[] = substr($valuesPart, $start, $i - $start);
                $start = null;
            }
            continue;
        }
    }

    return $tuples;
}

function splitSqlFields($tuple) {
    $fields = [];

    $inString = false;
    $buf = '';
    $len = strlen($tuple);

    for ($i = 0; $i < $len; $i++) {
        $ch = $tuple[$i];

        if ($ch === "'") {
            if ($inString) {
                $next = ($i + 1 < $len) ? $tuple[$i + 1] : '';
                if ($next === "'") {
                    $buf .= "'";
                    $i++;
                    continue;
                }
                $inString = false;
                continue;
            }
            $inString = true;
            continue;
        }

        if (!$inString && $ch === ',') {
            $fields[] = trim($buf);
            $buf = '';
            continue;
        }

        $buf .= $ch;
    }

    $fields[] = trim($buf);
    return $fields;
}

function sqlValueToPhp($raw) {
    $raw = trim($raw);

    if (strcasecmp($raw, 'NULL') === 0) {
        return null;
    }
    return $raw;
}

$pattern = '/INSERT\s+INTO\s+ETUDIANT\s*\((.*?)\)\s*VALUES\s*(.*?);/is';
$matches = [];
if (!preg_match_all($pattern, $sqlText, $matches, PREG_SET_ORDER)) {
    echo "Aucun INSERT INTO ETUDIANT trouvé dans $sourceFile\n";
    exit;
}

$rows = [];
$idsSeen = [];

foreach ($matches as $m) {
    $cols = array_map('trim', explode(',', $m[1]));
    $tuples = parseSqlInsertValues($m[2]);

    foreach ($tuples as $tuple) {
        $fields = splitSqlFields($tuple);
        if (count($fields) !== count($cols)) {
            continue;
        }

        $assoc = [];
        foreach ($cols as $i => $colName) {
            $assoc[$colName] = sqlValueToPhp($fields[$i]);
        }

        $idUtilisateur = isset($assoc['id_utilisateur']) ? $assoc['id_utilisateur'] : null;
        $idParcours = isset($assoc['id_parcours']) ? $assoc['id_parcours'] : null;

        if ($idUtilisateur === null || $idParcours === null) {
            continue;
        }

        if (isset($idsSeen[$idUtilisateur])) {
            continue;
        }
        $idsSeen[$idUtilisateur] = true;

        $prefix = 'etu';
        if ($idParcours === 'P_A') {
            $prefix = 'pa';
        } elseif ($idParcours === 'P_B') {
            $prefix = 'pb';
        } elseif ($idParcours === 'P_C') {
            $prefix = 'pc';
        }

        $prenom = pickFromList(['Lucas','Emma','Louis','Jade','Hugo','Léa','Arthur','Chloé','Nathan','Inès','Théo','Manon','Jules','Camille','Ethan','Sarah','Noah','Zoé','Liam','Louise'], $idUtilisateur);
        $nom = pickFromList(['Martin','Bernard','Thomas','Petit','Robert','Richard','Durand','Dubois','Moreau','Laurent','Simon','Michel','Lefebvre','Leroy','Roux','David','Bertrand','Morel','Fournier','Girard'], $idUtilisateur * 5);
        $mail = normalizeEmailPart($prenom) . '.' . normalizeEmailPart($nom) . '@etu.univ.fr';

        $rows[] = [
            'id_utilisateur' => $idUtilisateur,
            'prenom' => $prenom,
            'nom' => $nom,
            'mail' => $mail,
            'tel' => studentPhone9Digits($idUtilisateur),
            'adresse' => studentAddress($idUtilisateur),
            'genre' => studentGender($idUtilisateur),
            'date_naissance' => studentBirthDate($idUtilisateur),
            'login' => $prefix . $idUtilisateur,
            'motdepasse' => 'etu123',
            'statut' => 'ETUDIANT',
        ];
    }
}

usort($rows, function ($a, $b) {
    return $a['id_utilisateur'] <=> $b['id_utilisateur'];
});

$phpContent = "<?php\nreturn " . var_export($rows, true) . ";\n";
file_put_contents($outPhp, $phpContent);

echo 'OK: ' . count($rows) . " utilisateurs S4 exportés\n";
echo "- $outPhp\n";

if ($generateSql) {
    $hash = password_hash('etu123', PASSWORD_DEFAULT);

    $lines = [];
    $lines[] = "-- ============================================================";
    $lines[] = "-- UTILISATEUR S4 (P_A / P_B / P_C)";
    $lines[] = "-- Généré depuis info-bd/s4-etc.txt";
    $lines[] = "-- MDP (pour tous): etu123";
    $lines[] = "-- ============================================================";
    $lines[] = '';
    $lines[] = "INSERT INTO UTILISATEUR (id_utilisateur, prenom_utilisateur, nom_utilisateur, mail_utilisateur, login_utilisateur, mdp_hash_utilisateur, statut_utilisateur) VALUES";

    for ($i = 0; $i < count($rows); $i++) {
        $r = $rows[$i];
        $id = $r['id_utilisateur'];
        $prenom = addslashes($r['prenom']);
        $nom = addslashes($r['nom']);
        $mail = addslashes($r['mail']);
        $login = addslashes($r['login']);

        $suffix = ($i === count($rows) - 1) ? ';' : ',';
        $lines[] = "($id, '$prenom', '$nom', '$mail', '$login', '$hash', 'ETUDIANT')$suffix";
    }

    file_put_contents($outSql, implode("\n", $lines) . "\n");
    echo "- $outSql\n";
}
