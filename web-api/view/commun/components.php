<?php
function btn($text, $url = '#', $type = 'primary', $attributes = []) {
    $baseUrl = isset($GLOBALS['baseUrl']) ? $GLOBALS['baseUrl'] : '';
    if (is_string($url) && $url !== '' && $url[0] === '?') $url = $baseUrl . $url;
    $class = 'btn btn-' . $type;
    $attrs = '';
    if (isset($attributes['class'])) { $class .= ' ' . $attributes['class']; unset($attributes['class']); }
    foreach ($attributes as $k => $v) {
        if ($k === 'href' || $v === null) continue;
        $attrs .= is_bool($v) ? ($v ? ' ' . $k : '') : ' ' . $k . '="' . $v . '"';
    }
    return '<a href="' . $url . '" class="' . $class . '"' . $attrs . '>' . $text . '</a>';
}
function btnSubmit($text, $type = 'primary', $attributes = []) {
    $class = 'btn btn-' . $type;
    $attrs = '';
    if (isset($attributes['class'])) { $class .= ' ' . $attributes['class']; unset($attributes['class']); }
    foreach ($attributes as $k => $v) {
        if ($k === 'type' || $v === null) continue;
        $attrs .= is_bool($v) ? ($v ? ' ' . $k : '') : ' ' . $k . '="' . $v . '"';
    }
    return '<button type="submit" class="' . $class . '"' . $attrs . '>' . $text . '</button>';
}
function alert($message, $type = 'info') { return '<div class="alert alert-' . $type . '">' . $message . '</div>'; }
function badge($text, $type = 'primary') { return '<span class="badge badge-' . $type . '">' . $text . '</span>'; }
function linkBack($url, $text = '← Retour') {
    $baseUrl = isset($GLOBALS['baseUrl']) ? $GLOBALS['baseUrl'] : '';
    if (is_string($url) && $url !== '' && $url[0] === '?') $url = $baseUrl . $url;
    return '<a href="' . $url . '" class="btn btn-secondary btn-sm">' . $text . '</a>';
}
function displayFlashMessages() {
    $messages = ['created' => 'Élément créé avec succès.', 'updated' => 'Modifications enregistrées avec succès.', 'deleted' => 'Élément supprimé avec succès.', '1' => 'Opération effectuée avec succès.'];
    if (isset($_GET['success'])) {
        $msg = isset($messages[$_GET['success']]) ? $messages[$_GET['success']] : 'Opération effectuée avec succès.';
        echo '<div class="alert alert-success">' . htmlspecialchars($msg) . '</div>';
    }
    $errors = ['db' => 'Erreur lors de l\'enregistrement. Vérifiez les données.', 'notfound' => 'Élément introuvable.', 'missing' => 'Données manquantes ou invalides.', 'email' => 'Cet email est déjà utilisé.'];
    if (isset($_GET['error'])) {
        $msg = isset($errors[$_GET['error']]) ? $errors[$_GET['error']] : 'Une erreur est survenue.';
        echo '<div class="alert alert-danger">' . htmlspecialchars($msg) . '</div>';
    }
}
function renderSelect($name, $items, $idKey, $labelKey, $selectedValue = '', $required = false) {
    $html = '<select id="' . $name . '" name="' . $name . '" class="form-control"' . ($required ? ' required' : '') . '><option value="">— Choisir —</option>';
    foreach ($items as $item) {
        $id = is_object($item) ? $item->get($idKey) : $item[$idKey];
        $label = is_object($item) ? $item->get($labelKey) : $item[$labelKey];
        $sel = ($id == $selectedValue) ? ' selected' : '';
        $html .= '<option value="' . htmlspecialchars($id) . '"' . $sel . '>' . htmlspecialchars($label) . '</option>';
    }
    return $html . '</select>';
}
function layoutStart($withSidebar = null) {
    if ($withSidebar === null) $withSidebar = isset($GLOBALS['showSidebar']) ? $GLOBALS['showSidebar'] : true;
    require_once 'view/commun/header.php';
    echo '<div class="content-wrapper"><div class="layout-with-sidebar">';
    if ($withSidebar) require_once 'view/commun/navbar.php';
    echo '<main class="main-content">';
}
function layoutEnd() {
    echo '</main></div></div>';
    require_once 'view/commun/footer.php';
}
function card($content, $title = '', $class = '') {
    $html = '<div class="card' . ($class ? ' ' . $class : '') . '">';
    if ($title) $html .= '<div class="card-body"><h3>' . htmlspecialchars($title) . '</h3>';
    else $html .= '<div class="card-body">';
    $html .= $content . '</div></div>';
    return $html;
}
function alertEmpty($message = 'Aucun élément trouvé.', $type = 'info') {
    return '<div class="alert alert-' . $type . '">' . htmlspecialchars($message) . '</div>';
}
