<?php

require_once 'database.php';

$zapisano = '';
$blad = '';
$blad_nazwa = '';
$blad_placa_od = '';
$blad_placa_do = '';

if (!empty($_POST)) {
    
    $nazwa = isset($_POST['nazwa']) ? trim($_POST['nazwa']) : '';
    $placa_od = isset($_POST['placa_od']) ? $_POST['placa_od'] : '';
    $placa_do = isset($_POST['placa_do']) ? $_POST['placa_do'] : '';
    
    // Walidacja nazwy
    if (empty($nazwa)) {
        $blad = 'Tak';
        $blad_nazwa = 'Nie podano nazwy etatu';
    } elseif (mb_strlen($nazwa) > 30) {
        $blad = 'Tak';
        $blad_nazwa = 'Nazwa etatu nie może być dłuższa niż 30 znaków';
    }
    
    // Walidacja płac
    if (empty($placa_od)) {
        $blad = 'Tak';
        $blad_placa_od = 'Nie podano płacy od';
    } elseif (!is_numeric($placa_od) || $placa_od <= 0) {
        $blad = 'Tak';
        $blad_placa_od = 'Płaca od musi być liczbą dodatnią';
    }
    
    if (empty($placa_do)) {
        $blad = 'Tak';
        $blad_placa_do = 'Nie podano płacy do';
    } elseif (!is_numeric($placa_do) || $placa_do <= 0) {
        $blad = 'Tak';
        $blad_placa_do = 'Płaca do musi być liczbą dodatnią';
    }
    
    // Sprawdź czy płaca_od <= płaca_do
    if (!empty($placa_od) && !empty($placa_do) && is_numeric($placa_od) && is_numeric($placa_do)) {
        if ($placa_od > $placa_do) {
            $blad = 'Tak';
            $blad_placa_do = 'Płaca do musi być większa lub równa płacy od';
        }
    }
    
    // Jeśli brak błędów - zapisz do bazy
    if ($blad === '') {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO etaty (NAZWA, PLACA_OD, PLACA_DO)
                VALUES (:nazwa, :placa_od, :placa_do)
            ");
            
            $stmt->bindParam(':nazwa', $nazwa, PDO::PARAM_STR);
            $stmt->bindParam(':placa_od', $placa_od);
            $stmt->bindParam(':placa_do', $placa_do);

            if ($stmt->execute()) {
                $zapisano = 'Tak';
            }
        } catch (Exception $e) {
            $blad = 'Tak';
        }
    }
}

if ($zapisano === 'Tak') {
    echo '<div class="alert alert-success">Etat został dodany!</div>';
} elseif ($blad !== '') {
    echo '<div class="alert alert-danger">Formularz zawiera błędy</div>';
    echo '<div data-error-for="nazwa">' . $blad_nazwa . '</div>';
    echo '<div data-error-for="placa_od">' . $blad_placa_od . '</div>';
    echo '<div data-error-for="placa_do">' . $blad_placa_do . '</div>';
}
