<?php

require_once 'database.php';

// Helper function do bezpiecznego wyświetlania wartości nullable
function safe_display($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo '<div class="alert alert-danger">Błąd: Nieprawidłowy ID pracownika</div>';
    exit;
}

try {
    // Pobierz dane pracownika
    $stmt = $pdo->prepare('SELECT * FROM pracownicy WHERE ID_PRAC = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $pracownik = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pracownik) {
        echo '<div class="alert alert-danger">Błąd: Pracownik nie znaleziony</div>';
        exit;
    }
    
    // Pobierz etaty z zakresami płac
    $stmt = $pdo->prepare('SELECT NAZWA, PLACA_OD, PLACA_DO FROM etaty ORDER BY NAZWA');
    $stmt->execute();
    $etaty = $stmt->fetchAll();
    
    // Pobierz szefów (pracowników)
    $stmt = $pdo->prepare('SELECT ID_PRAC, CONCAT(IMIE, " ", NAZWISKO) AS NAZWA FROM pracownicy WHERE ID_PRAC != :id ORDER BY IMIE, NAZWISKO');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $szefy = $stmt->fetchAll();
    
    // Pobierz zespoły
    $stmt = $pdo->prepare('SELECT ID_ZESP, NAZWA FROM zespoly ORDER BY NAZWA');
    $stmt->execute();
    $zespoly = $stmt->fetchAll();
    
    $html = '';
    
    $html .= '<form id="formEditPracownik" method="post" novalidate>';
    $html .= '<input type="hidden" name="id_prac" value="' . safe_display($pracownik['ID_PRAC']) . '">';
    
    // Imię
    $html .= '<div class="mb-3">';
    $html .= '<label for="imie" class="form-label">Imię *</label>';
    $html .= '<input type="text" class="form-control" id="imie" name="imie" value="' . safe_display($pracownik['IMIE']) . '" required>';
    $html .= '<div class="invalid-feedback" id="imie-error"></div>';
    $html .= '</div>';
    
    // Nazwisko
    $html .= '<div class="mb-3">';
    $html .= '<label for="nazwisko" class="form-label">Nazwisko *</label>';
    $html .= '<input type="text" class="form-control" id="nazwisko" name="nazwisko" value="' . safe_display($pracownik['NAZWISKO']) . '" required>';
    $html .= '<div class="invalid-feedback" id="nazwisko-error"></div>';
    $html .= '</div>';
    
    // Etat
    $html .= '<div class="mb-3">';
    $html .= '<label for="etat" class="form-label">Etat *</label>';
    $html .= '<select class="form-select" id="etat" name="etat" required>';
    $html .= '<option value="">-- Wybierz etat --</option>';
    foreach ($etaty as $e) {
        $selected = ($e['NAZWA'] === $pracownik['ETAT']) ? 'selected' : '';
        $html .= '<option value="' . safe_display($e['NAZWA']) . '" data-placa-od="' . safe_display($e['PLACA_OD']) . '" data-placa-do="' . safe_display($e['PLACA_DO']) . '" ' . $selected . '>' . safe_display($e['NAZWA']) . '</option>';
    }
    $html .= '</select>';
    $html .= '<div class="invalid-feedback" id="etat-error"></div>';
    $html .= '<small class="form-text text-muted" id="etat-range" style="display:none;">Zakres płacy: <strong id="etat-range-value"></strong></small>';
    $html .= '</div>';
    
    // Szef
    $html .= '<div class="mb-3">';
    $html .= '<label for="id_szefa" class="form-label">Szef</label>';
    $html .= '<select class="form-select" id="id_szefa" name="id_szefa">';
    $html .= '<option value="">-- Brak szefa --</option>';
    foreach ($szefy as $s) {
        $selected = ($s['ID_PRAC'] == $pracownik['ID_SZEFA']) ? 'selected' : '';
        $html .= '<option value="' . safe_display($s['ID_PRAC']) . '" ' . $selected . '>' . safe_display($s['NAZWA']) . '</option>';
    }
    $html .= '</select>';
    $html .= '<div class="invalid-feedback" id="id_szefa-error"></div>';
    $html .= '</div>';
    
    // Data zatrudnienia
    $html .= '<div class="mb-3">';
    $html .= '<label for="zatrudniony" class="form-label">Data zatrudnienia *</label>';
    $html .= '<input type="date" class="form-control" id="zatrudniony" name="zatrudniony" value="' . safe_display($pracownik['ZATRUDNIONY']) . '" required>';
    $html .= '<div class="invalid-feedback" id="zatrudniony-error"></div>';
    $html .= '</div>';
    
    // Płaca podstawowa
    $html .= '<div class="mb-3">';
    $html .= '<label for="placa_pod" class="form-label">Płaca podstawowa *</label>';
    $html .= '<input type="number" step="0.01" class="form-control" id="placa_pod" name="placa_pod" value="' . safe_display($pracownik['PLACA_POD']) . '" required>';
    $html .= '<div class="invalid-feedback" id="placa_pod-error"></div>';
    $html .= '<small class="form-text text-danger" id="placa-warning" style="display:none;">⚠️ Płaca nie mieści się w przedziale wybranego etatu!</small>';
    $html .= '</div>';
    
    // Płaca dodatkowa
    $html .= '<div class="mb-3">';
    $html .= '<label for="placa_dod" class="form-label">Płaca dodatkowa</label>';
    $html .= '<input type="number" step="0.01" class="form-control" id="placa_dod" name="placa_dod" value="' . safe_display($pracownik['PLACA_DOD']) . '">';
    $html .= '<div class="invalid-feedback" id="placa_dod-error"></div>';
    $html .= '</div>';
    
    // Zespół
    $html .= '<div class="mb-3">';
    $html .= '<label for="id_zesp" class="form-label">Zespół *</label>';
    $html .= '<select class="form-select" id="id_zesp" name="id_zesp" required>';
    $html .= '<option value="">-- Wybierz zespół --</option>';
    foreach ($zespoly as $z) {
        $selected = ($z['ID_ZESP'] == $pracownik['ID_ZESP']) ? 'selected' : '';
        $html .= '<option value="' . safe_display($z['ID_ZESP']) . '" ' . $selected . '>' . safe_display($z['NAZWA']) . '</option>';
    }
    $html .= '</select>';
    $html .= '<div class="invalid-feedback" id="id_zesp-error"></div>';
    $html .= '</div>';
    
    $html .= '</form>';
    
    echo $html;
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Błąd: ' . safe_display($e->getMessage()) . '</div>';
}
