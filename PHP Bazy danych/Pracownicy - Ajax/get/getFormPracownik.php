<?php

require_once 'database.php';

$stmt = $pdo->prepare('SELECT NAZWA, PLACA_OD, PLACA_DO FROM etaty ORDER BY NAZWA');
$stmt->execute();
$etaty = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT ID_PRAC, CONCAT(IMIE, " ", NAZWISKO) AS NAZWA FROM pracownicy ORDER BY IMIE, NAZWISKO');
$stmt->execute();
$szefy = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT ID_ZESP, NAZWA FROM zespoly ORDER BY NAZWA');
$stmt->execute();
$zespoly = $stmt->fetchAll();

$html = '';

$html .= '<form id="formAddPracownik" method="post" novalidate>';

$html .= '<div class="mb-3">';
$html .= '<label for="imie" class="form-label">Imię *</label>';
$html .= '<input type="text" class="form-control" id="imie" name="imie" placeholder="Jan" required>';
$html .= '<div class="invalid-feedback" id="imie-error"></div>';
$html .= '</div>';

$html .= '<div class="mb-3">';
$html .= '<label for="nazwisko" class="form-label">Nazwisko *</label>';
$html .= '<input type="text" class="form-control" id="nazwisko" name="nazwisko" placeholder="Kowalski" required>';
$html .= '<div class="invalid-feedback" id="nazwisko-error"></div>';
$html .= '</div>';

$html .= '<div class="mb-3">';
$html .= '<label for="etat" class="form-label">Etat *</label>';
$html .= '<select class="form-select" id="etat" name="etat" required>';
$html .= '<option value="">Wybierz etat</option>';
foreach ($etaty as $row) {
    $html .= '<option value="' . htmlspecialchars($row['NAZWA']) . '" data-placa-od="' . htmlspecialchars($row['PLACA_OD']) . '" data-placa-do="' . htmlspecialchars($row['PLACA_DO']) . '">' . htmlspecialchars($row['NAZWA']) . '</option>';
}
$html .= '</select>';
$html .= '<div class="invalid-feedback" id="etat-error"></div>';
$html .= '<small class="form-text text-muted" id="etat-range" style="display:none;">Zakres płacy: <strong id="etat-range-value"></strong></small>';
$html .= '</div>';

$html .= '<div class="mb-3">';
$html .= '<label for="szef" class="form-label">Szef</label>';
$html .= '<select class="form-select" id="szef" name="szef">';
$html .= '<option value="0">Brak szefa</option>';
foreach ($szefy as $row) {
    $html .= '<option value="' . htmlspecialchars($row['ID_PRAC']) . '">' . htmlspecialchars($row['NAZWA']) . '</option>';
}
$html .= '</select>';
$html .= '<div class="invalid-feedback" id="szef-error"></div>';
$html .= '</div>';

$html .= '<div class="mb-3">';
$html .= '<label for="zespol" class="form-label">Zespół *</label>';
$html .= '<select class="form-select" id="zespol" name="zespol" required>';
$html .= '<option value="">Wybierz zespół</option>';
foreach ($zespoly as $row) {
    $html .= '<option value="' . htmlspecialchars($row['ID_ZESP']) . '">' . htmlspecialchars($row['NAZWA']) . '</option>';
}
$html .= '</select>';
$html .= '<div class="invalid-feedback" id="zespol-error"></div>';
$html .= '</div>';

$html .= '<div class="mb-3">';
$html .= '<label for="data_zatrudnienia" class="form-label">Data zatrudnienia *</label>';
$html .= '<input type="date" class="form-control" id="data_zatrudnienia" name="data_zatrudnienia" required>';
$html .= '<div class="invalid-feedback" id="data_zatrudnienia-error"></div>';
$html .= '</div>';

$html .= '<div class="mb-3">';
$html .= '<label for="placa_pod" class="form-label">Płaca podstawowa *</label>';
$html .= '<input type="number" class="form-control" id="placa_pod" name="placa_pod" placeholder="0.00" step="0.01" min="0" required>';
$html .= '<div class="invalid-feedback" id="placa_pod-error"></div>';
$html .= '<small class="form-text text-danger" id="placa-warning" style="display:none;">⚠️ Płaca nie mieści się w przedziale wybranego etatu!</small>';
$html .= '</div>';

$html .= '<div class="mb-3">';
$html .= '<label for="placa_dod" class="form-label">Płaca dodatkowa</label>';
$html .= '<input type="number" class="form-control" id="placa_dod" name="placa_dod" placeholder="0.00" step="0.01" min="0">';
$html .= '<div class="invalid-feedback" id="placa_dod-error"></div>';
$html .= '</div>';

$html .= '</form>';

echo $html;
