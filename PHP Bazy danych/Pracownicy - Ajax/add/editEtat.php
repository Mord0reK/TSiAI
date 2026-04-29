<?php

require_once 'database.php';

$zapisano = '';
$blad = '';
$blad_nazwa = '';
$blad_placa_od = '';
$blad_placa_do = '';

if (!empty($_POST)) {
    
    $nazwa_stara = isset($_POST['nazwa']) ? trim($_POST['nazwa']) : '';
    $nazwa_nowa = isset($_POST['nazwa_nowa']) ? trim($_POST['nazwa_nowa']) : '';
    $placa_od = isset($_POST['placa_od']) ? floatval($_POST['placa_od']) : 0;
    $placa_do = isset($_POST['placa_do']) ? floatval($_POST['placa_do']) : 0;
    
    // Walidacja nazwy starej
    if (empty($nazwa_stara)) {
        $blad = 'Tak';
        $blad_nazwa = 'Błąd: Brak starej nazwy etatu';
    }
    
    // Walidacja nazwy nowej
    if (empty($nazwa_nowa)) {
        $blad = 'Tak';
        $blad_nazwa = 'Nie podano nazwy etatu';
    } elseif (mb_strlen($nazwa_nowa) > 30) {
        $blad = 'Tak';
        $blad_nazwa = 'Nazwa etatu nie może być dłuższa niż 30 znaków';
    }
    
    // Walidacja płac
    if ($placa_od <= 0) {
        $blad = 'Tak';
        $blad_placa_od = 'Płaca od musi być większa niż 0';
    }
    
    if ($placa_do <= 0) {
        $blad = 'Tak';
        $blad_placa_do = 'Płaca do musi być większa niż 0';
    }
    
    if ($placa_od > $placa_do) {
        $blad = 'Tak';
        $blad_placa_od = 'Płaca od nie może być większa niż płaca do';
    }
    
    // Jeśli brak błędów - aktualizuj w bazie
    if ($blad === '') {
        try {
            $stmt = $pdo->prepare("
                UPDATE etaty 
                SET NAZWA = :nazwa_nowa, 
                    PLACA_OD = :placa_od, 
                    PLACA_DO = :placa_do
                WHERE NAZWA = :nazwa_stara
            ");
            
            $stmt->bindParam(':nazwa_stara', $nazwa_stara, PDO::PARAM_STR);
            $stmt->bindParam(':nazwa_nowa', $nazwa_nowa, PDO::PARAM_STR);
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
    echo '<div class="alert alert-success">Etat został zaktualizowany!</div>';
} elseif ($blad !== '') {
    echo '<div class="alert alert-danger">Formularz zawiera błędy</div>';
    echo '<div data-error-for="nazwa_nowa">' . $blad_nazwa . '</div>';
    echo '<div data-error-for="placa_od">' . $blad_placa_od . '</div>';
    echo '<div data-error-for="placa_do">' . $blad_placa_do . '</div>';
}
