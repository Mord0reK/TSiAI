<?php

require_once 'database.php';

$zapisano = '';
$blad = '';
$blad_imie = '';
$blad_nazwisko = '';
$blad_etat = '';
$blad_id_prac = '';
$blad_id_szefa = '';
$blad_zatrudniony = '';
$blad_placa_pod = '';
$blad_placa_dod = '';
$blad_id_zesp = '';

if (!empty($_POST)) {
    $id_prac = isset($_POST['id_prac']) ? intval($_POST['id_prac']) : 0;
    $imie = isset($_POST['imie']) ? trim($_POST['imie']) : '';
    $nazwisko = isset($_POST['nazwisko']) ? trim($_POST['nazwisko']) : '';
    $etat = isset($_POST['etat']) ? trim($_POST['etat']) : '';
    $id_szefa = isset($_POST['id_szefa']) ? (trim($_POST['id_szefa']) === '' ? null : intval($_POST['id_szefa'])) : null;
    $zatrudniony = isset($_POST['zatrudniony']) ? trim($_POST['zatrudniony']) : '';
    $placa_pod = isset($_POST['placa_pod']) ? floatval($_POST['placa_pod']) : 0;
    $placa_dod = isset($_POST['placa_dod']) ? floatval($_POST['placa_dod']) : 0;
    $id_zesp = isset($_POST['id_zesp']) ? intval($_POST['id_zesp']) : 0;
    
    // Walidacja ID
    if ($id_prac <= 0) {
        $blad = 'Tak';
        $blad_id_prac = 'Nieprawidłowy ID pracownika';
    }
    
    // Walidacja imienia
    if (empty($imie)) {
        $blad = 'Tak';
        $blad_imie = 'Nie podano imienia';
    } elseif (mb_strlen($imie) > 20) {
        $blad = 'Tak';
        $blad_imie = 'Imię nie może być dłuższe niż 20 znaków';
    } elseif (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s-]*$/', $imie)) {
        $blad = 'Tak';
        $blad_imie = 'Imię może zawierać tylko litery, spacje i myślniki';
    }
    
    // Walidacja nazwiska
    if (empty($nazwisko)) {
        $blad = 'Tak';
        $blad_nazwisko = 'Nie podano nazwiska';
    } elseif (mb_strlen($nazwisko) > 15) {
        $blad = 'Tak';
        $blad_nazwisko = 'Nazwisko nie może być dłuższe niż 15 znaków';
    }
    
    // Walidacja etatu
    if (empty($etat)) {
        $blad = 'Tak';
        $blad_etat = 'Nie wybrano etatu';
    }
    
    // Walidacja daty zatrudnienia
    if (empty($zatrudniony)) {
        $blad = 'Tak';
        $blad_zatrudniony = 'Nie podano daty zatrudnienia';
    }
    
    // Walidacja płacy podstawowej
    if ($placa_pod <= 0) {
        $blad = 'Tak';
        $blad_placa_pod = 'Płaca podstawowa musi być większa niż 0';
    } else {
        // Walidacja czy płaca mieści się w przedziale etatu
        try {
            $stmt = $pdo->prepare('SELECT PLACA_OD, PLACA_DO FROM etaty WHERE NAZWA = :etat');
            $stmt->bindParam(':etat', $etat, PDO::PARAM_STR);
            $stmt->execute();
            $etat_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($etat_data) {
                if ($placa_pod < $etat_data['PLACA_OD'] || $placa_pod > $etat_data['PLACA_DO']) {
                    $blad = 'Tak';
                    $blad_placa_pod = 'Płaca podstawowa musi być w przedziale ' . $etat_data['PLACA_OD'] . ' - ' . $etat_data['PLACA_DO'];
                }
            }
        } catch (Exception $e) {
            // Ignoruj błędy walidacji zakresu
        }
    }
    
    // Walidacja płacy dodatkowej
    if ($placa_dod < 0) {
        $blad = 'Tak';
        $blad_placa_dod = 'Płaca dodatkowa nie może być ujemna';
    } elseif ($placa_dod > $placa_pod) {
        $blad = 'Tak';
        $blad_placa_dod = 'Płaca dodatkowa nie może być większa niż płaca podstawowa';
    }
    
    // Walidacja zespołu
    if ($id_zesp <= 0) {
        $blad = 'Tak';
        $blad_id_zesp = 'Nie wybrano zespołu';
    }
    
    // Jeśli brak błędów - aktualizuj w bazie
    if ($blad === '') {
        try {
            $stmt = $pdo->prepare("
                UPDATE pracownicy 
                SET IMIE = :imie, 
                    NAZWISKO = :nazwisko, 
                    ETAT = :etat, 
                    ID_SZEFA = :id_szefa, 
                    ZATRUDNIONY = :zatrudniony, 
                    PLACA_POD = :placa_pod, 
                    PLACA_DOD = :placa_dod, 
                    ID_ZESP = :id_zesp
                WHERE ID_PRAC = :id_prac
            ");
            
            $stmt->bindParam(':id_prac', $id_prac, PDO::PARAM_INT);
            $stmt->bindParam(':imie', $imie, PDO::PARAM_STR);
            $stmt->bindParam(':nazwisko', $nazwisko, PDO::PARAM_STR);
            $stmt->bindParam(':etat', $etat, PDO::PARAM_STR);
            if ($id_szefa === null) {
                $stmt->bindValue(':id_szefa', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':id_szefa', $id_szefa, PDO::PARAM_INT);
            }
            $stmt->bindParam(':zatrudniony', $zatrudniony, PDO::PARAM_STR);
            $stmt->bindParam(':placa_pod', $placa_pod);
            $stmt->bindParam(':placa_dod', $placa_dod);
            $stmt->bindParam(':id_zesp', $id_zesp, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $zapisano = 'Tak';
            }
        } catch (Exception $e) {
            $blad = 'Tak';
        }
    }
}

if ($zapisano === 'Tak') {
    echo '<div class="alert alert-success">Pracownik został zaktualizowany!</div>';
} elseif ($blad !== '') {
    echo '<div class="alert alert-danger">Formularz zawiera błędy</div>';
    echo '<div data-error-for="id_prac">' . $blad_id_prac . '</div>';
    echo '<div data-error-for="imie">' . $blad_imie . '</div>';
    echo '<div data-error-for="nazwisko">' . $blad_nazwisko . '</div>';
    echo '<div data-error-for="etat">' . $blad_etat . '</div>';
    echo '<div data-error-for="id_szefa">' . $blad_id_szefa . '</div>';
    echo '<div data-error-for="zatrudniony">' . $blad_zatrudniony . '</div>';
    echo '<div data-error-for="placa_pod">' . $blad_placa_pod . '</div>';
    echo '<div data-error-for="placa_dod">' . $blad_placa_dod . '</div>';
    echo '<div data-error-for="id_zesp">' . $blad_id_zesp . '</div>';
}
