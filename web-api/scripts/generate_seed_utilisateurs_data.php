<?php
// Génère un fichier PHP (tableau) à partir de info-bd/etudiant s1-s2-s3.txt
// On extrait seulement les INSERT INTO UTILISATEUR.

$sourceFile = __DIR__ . '/../info-bd/etudiant s1-s2-s3.txt';
$outFile = __DIR__ . '/seed_utilisateurs_data.generated.php';

if (!file_exists($sourceFile)) {
    echo "Fichier introuvable: $sourceFile\n";
    exit;
}

$sqlText = file_get_contents($sourceFile);
if ($sqlText === false) {
    echo "Impossible de lire: $sourceFile\n";
    exit;
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

    if ($raw === '') {
        return '';
    }

    return $raw;
}

$pattern = '/INSERT\s+INTO\s+UTILISATEUR\s*\((.*?)\)\s*VALUES\s*(.*?);/is';
if (!preg_match_all($pattern, $sqlText, $matches, PREG_SET_ORDER)) {
    echo "Aucun INSERT INTO UTILISATEUR trouvé.\n";
    exit;
}

$rows = [];
foreach ($matches as $m) {
    $cols = array_map('trim', explode(',', $m[1]));
    $tuples = parseSqlInsertValues($m[2]);

    foreach ($tuples as $tuple) {
        $fields = splitSqlFields($tuple);
        if (count($fields) !== count($cols)) {
            continue;
        }

        $rawAssoc = [];
        foreach ($cols as $i => $colName) {
            $rawAssoc[$colName] = sqlValueToPhp($fields[$i]);
        }

        // On fabrique un tableau au format attendu par Utilisateur::create()
        $rows[] = [
            'prenom' => isset($rawAssoc['prenom_utilisateur']) ? $rawAssoc['prenom_utilisateur'] : '',
            'nom' => isset($rawAssoc['nom_utilisateur']) ? $rawAssoc['nom_utilisateur'] : '',
            'mail' => isset($rawAssoc['mail_utilisateur']) ? $rawAssoc['mail_utilisateur'] : '',
            'tel' => isset($rawAssoc['tel_utilisateur']) ? $rawAssoc['tel_utilisateur'] : null,
            'adresse' => isset($rawAssoc['adresse_utilisateur']) ? $rawAssoc['adresse_utilisateur'] : null,
            'genre' => isset($rawAssoc['genre_utilisateur']) ? $rawAssoc['genre_utilisateur'] : null,
            'date_naissance' => isset($rawAssoc['date_naissance']) ? $rawAssoc['date_naissance'] : null,
            'login' => isset($rawAssoc['login_utilisateur']) ? $rawAssoc['login_utilisateur'] : '',

            // Dans le .txt c'est mdp_hash_utilisateur, mais chez toi c'est en clair.
            // On le met dans motdepasse, et le modèle fera password_hash().
            'motdepasse' => isset($rawAssoc['mdp_hash_utilisateur']) ? $rawAssoc['mdp_hash_utilisateur'] : '',

            'statut' => isset($rawAssoc['statut_utilisateur']) ? $rawAssoc['statut_utilisateur'] : null,
        ];
    }
}

$content = "<?php\nreturn " . var_export($rows, true) . ";\n";
file_put_contents($outFile, $content);

echo "OK: " . count($rows) . " utilisateurs exportés vers scripts/seed_utilisateurs_data.generated.php\n";
