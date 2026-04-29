<?php

require_once 'database.php';

$nazwa = isset($_GET['nazwa']) ? trim($_GET['nazwa']) : '';

if (empty($nazwa)) {
    echo '<div class="alert alert-danger">Błąd: Nie podano nazwy etatu</div>';
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM etaty WHERE NAZWA = :nazwa');
    $stmt->bindParam(':nazwa', $nazwa, PDO::PARAM_STR);
    $stmt->execute();
    $etat = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$etat) {
        echo '<div class="alert alert-danger">Błąd: Etat nie znaleziony</div>';
        exit;
    }
    
    $html = '';
    
    $html .= '<form id="formEditEtat" method="post" novalidate>';
    $html .= '<input type="hidden" name="nazwa" value="' . htmlspecialchars($etat['NAZWA']) . '">';
    
    // Nazwa
    $html .= '<div class="mb-3">';
    $html .= '<label for="nazwa" class="form-label">Nazwa etatu *</label>';
    $html .= '<input type="text" class="form-control" id="nazwa" name="nazwa_nowa" value="' . htmlspecialchars($etat['NAZWA']) . '" required>';
    $html .= '<div class="invalid-feedback" id="nazwa-error"></div>';
    $html .= '</div>';
    
    // Płaca od
    $html .= '<div class="mb-3">';
    $html .= '<label for="placa_od" class="form-label">Płaca od *</label>';
    $html .= '<input type="number" step="0.01" class="form-control" id="placa_od" name="placa_od" value="' . htmlspecialchars($etat['PLACA_OD']) . '" required>';
    $html .= '<div class="invalid-feedback" id="placa_od-error"></div>';
    $html .= '</div>';
    
    // Płaca do
    $html .= '<div class="mb-3">';
    $html .= '<label for="placa_do" class="form-label">Płaca do *</label>';
    $html .= '<input type="number" step="0.01" class="form-control" id="placa_do" name="placa_do" value="' . htmlspecialchars($etat['PLACA_DO']) . '" required>';
    $html .= '<div class="invalid-feedback" id="placa_do-error"></div>';
    $html .= '</div>';
    
    $html .= '</form>';
    
    echo $html;
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Błąd: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
