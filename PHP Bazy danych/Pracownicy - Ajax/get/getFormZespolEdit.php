<?php

require_once 'database.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo '<div class="alert alert-danger">Błąd: Nieprawidłowy ID zespołu</div>';
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT * FROM zespoly WHERE ID_ZESP = :id');
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $zespol = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$zespol) {
        echo '<div class="alert alert-danger">Błąd: Zespół nie znaleziony</div>';
        exit;
    }
    
    $html = '';
    
    $html .= '<form id="formEditZespol" method="post" novalidate>';
    $html .= '<input type="hidden" name="id_zesp" value="' . htmlspecialchars($zespol['ID_ZESP']) . '">';
    
    // Nazwa
    $html .= '<div class="mb-3">';
    $html .= '<label for="nazwa" class="form-label">Nazwa zespołu *</label>';
    $html .= '<input type="text" class="form-control" id="nazwa" name="nazwa" value="' . htmlspecialchars($zespol['NAZWA']) . '" placeholder="Zespół informatyki" required>';
    $html .= '<div class="invalid-feedback" id="nazwa-error"></div>';
    $html .= '</div>';
    
    // Adres
    $html .= '<div class="mb-3">';
    $html .= '<label for="adres" class="form-label">Adres *</label>';
    $html .= '<input type="text" class="form-control" id="adres" name="adres" value="' . htmlspecialchars($zespol['ADRES']) . '" placeholder="ul. Główna 123" required>';
    $html .= '<div class="invalid-feedback" id="adres-error"></div>';
    $html .= '</div>';
    
    $html .= '</form>';
    
    echo $html;
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Błąd: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
