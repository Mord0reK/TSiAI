<?php

$html = '';

$html .= '<form id="formAddEtat" method="post" novalidate>';

// Nazwa
$html .= '<div class="mb-3">';
$html .= '<label for="nazwa" class="form-label">Nazwa etatu *</label>';
$html .= '<input type="text" class="form-control" id="nazwa" name="nazwa" placeholder="Kierownik" required>';
$html .= '<div class="invalid-feedback" id="nazwa-error"></div>';
$html .= '</div>';

// Płaca od
$html .= '<div class="mb-3">';
$html .= '<label for="placa_od" class="form-label">Płaca od *</label>';
$html .= '<input type="number" class="form-control" id="placa_od" name="placa_od" placeholder="0.00" step="0.01" min="0" required>';
$html .= '<div class="invalid-feedback" id="placa_od-error"></div>';
$html .= '</div>';

// Płaca do
$html .= '<div class="mb-3">';
$html .= '<label for="placa_do" class="form-label">Płaca do *</label>';
$html .= '<input type="number" class="form-control" id="placa_do" name="placa_do" placeholder="0.00" step="0.01" min="0" required>';
$html .= '<div class="invalid-feedback" id="placa_do-error"></div>';
$html .= '</div>';

$html .= '</form>';

echo $html;
