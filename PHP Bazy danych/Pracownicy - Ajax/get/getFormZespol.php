<?php

$html = '';

$html .= '<form id="formAddZespol" method="post" novalidate>';

// Nazwa
$html .= '<div class="mb-3">';
$html .= '<label for="nazwa" class="form-label">Nazwa zespołu *</label>';
$html .= '<input type="text" class="form-control" id="nazwa" name="nazwa" placeholder="Zespół informatyki" required>';
$html .= '<div class="invalid-feedback" id="nazwa-error"></div>';
$html .= '</div>';

// Adres
$html .= '<div class="mb-3">';
$html .= '<label for="adres" class="form-label">Adres *</label>';
$html .= '<input type="text" class="form-control" id="adres" name="adres" placeholder="ul. Główna 123" required>';
$html .= '<div class="invalid-feedback" id="adres-error"></div>';
$html .= '</div>';

$html .= '</form>';

echo $html;
