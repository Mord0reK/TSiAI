<?php

require_once 'database.php';

$zapisano = '';
$blad = '';
$blad_nazwa = '';
$blad_adres = '';

if (!empty($_POST)) {
    
    $nazwa = isset($_POST['nazwa']) ? trim($_POST['nazwa']) : '';
    $adres = isset($_POST['adres']) ? trim($_POST['adres']) : '';
    
    // Walidacja nazwy
    if (empty($nazwa)) {
        $blad = 'Tak';
        $blad_nazwa = 'Nie podano nazwy zespołu';
    } elseif (mb_strlen($nazwa) > 30) {
        $blad = 'Tak';
        $blad_nazwa = 'Nazwa zespołu nie może być dłuższa niż 30 znaków';
    }
    
    // Walidacja adresu
    if (empty($adres)) {
        $blad = 'Tak';
        $blad_adres = 'Nie podano adresu';
    } elseif (mb_strlen($adres) > 50) {
        $blad = 'Tak';
        $blad_adres = 'Adres nie może być dłuższy niż 50 znaków';
    }
    
    // Jeśli brak błędów - zapisz do bazy
    if ($blad === '') {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO zespoly (NAZWA, ADRES)
                VALUES (:nazwa, :adres)
            ");
            
            $stmt->bindParam(':nazwa', $nazwa, PDO::PARAM_STR);
            $stmt->bindParam(':adres', $adres, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $zapisano = 'Tak';
            }
        } catch (Exception $e) {
            $blad = 'Tak';
        }
    }
}

if ($zapisano === 'Tak') {
    echo '<div class="alert alert-success">Zespół został dodany!</div>';
} elseif ($blad !== '') {
    echo '<div class="alert alert-danger">Formularz zawiera błędy</div>';
    echo '<div data-error-for="nazwa">' . $blad_nazwa . '</div>';
    echo '<div data-error-for="adres">' . $blad_adres . '</div>';
}
