<?php

require_once '../get/database.php';

$response = array(
    'success' => false,
    'message' => '',
    'errors' => array()
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
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
        $response['errors']['id_prac'] = 'Nieprawidłowy ID pracownika';
    }
    
    // Walidacja imienia
    if (empty($imie)) {
        $response['errors']['imie'] = 'Nie podano imienia';
    } elseif (mb_strlen($imie) > 20) {
        $response['errors']['imie'] = 'Imię nie może być dłuższe niż 20 znaków';
    } elseif (!preg_match('/^[a-zA-ZąćęłńóśźżĄĆĘŁŃÓŚŹŻ\s-]*$/', $imie)) {
        $response['errors']['imie'] = 'Imię może zawierać tylko litery, spacje i myślniki';
    }
    
    // Walidacja nazwiska
    if (empty($nazwisko)) {
        $response['errors']['nazwisko'] = 'Nie podano nazwiska';
    } elseif (mb_strlen($nazwisko) > 15) {
        $response['errors']['nazwisko'] = 'Nazwisko nie może być dłuższe niż 15 znaków';
    }
    
    // Walidacja etatu
    if (empty($etat)) {
        $response['errors']['etat'] = 'Nie wybrano etatu';
    }
    
    // Walidacja daty zatrudnienia
    if (empty($zatrudniony)) {
        $response['errors']['zatrudniony'] = 'Nie podano daty zatrudnienia';
    }
    
    // Walidacja płacy podstawowej
    if ($placa_pod <= 0) {
        $response['errors']['placa_pod'] = 'Płaca podstawowa musi być większa niż 0';
    } else {
        // Walidacja czy płaca mieści się w przedziale etatu
        try {
            $stmt = $pdo->prepare('SELECT PLACA_OD, PLACA_DO FROM etaty WHERE NAZWA = :etat');
            $stmt->bindParam(':etat', $etat, PDO::PARAM_STR);
            $stmt->execute();
            $etat_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($etat_data) {
                if ($placa_pod < $etat_data['PLACA_OD'] || $placa_pod > $etat_data['PLACA_DO']) {
                    $response['errors']['placa_pod'] = 'Płaca podstawowa musi być w przedziale ' . $etat_data['PLACA_OD'] . ' - ' . $etat_data['PLACA_DO'];
                }
            }
        } catch (Exception $e) {
            // Ignoruj błędy walidacji zakresu
        }
    }
    
    // Walidacja płacy dodatkowej
    if ($placa_dod < 0) {
        $response['errors']['placa_dod'] = 'Płaca dodatkowa nie może być ujemna';
    }
    
    // Walidacja zespołu
    if ($id_zesp <= 0) {
        $response['errors']['id_zesp'] = 'Nie wybrano zespołu';
    }
    
    // Jeśli brak błędów - aktualizuj w bazie
    if (empty($response['errors'])) {
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
            $stmt->bindParam(':id_szefa', $id_szefa, PDO::PARAM_INT);
            $stmt->bindParam(':zatrudniony', $zatrudniony, PDO::PARAM_STR);
            $stmt->bindParam(':placa_pod', $placa_pod);
            $stmt->bindParam(':placa_dod', $placa_dod);
            $stmt->bindParam(':id_zesp', $id_zesp, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Pracownik został zaktualizowany!';
            } else {
                $response['success'] = false;
                $response['message'] = 'Błąd podczas aktualizacji pracownika';
            }
        } catch (Exception $e) {
            $response['success'] = false;
            $response['message'] = 'Błąd: ' . $e->getMessage();
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Formularz zawiera błędy';
    }
}

header('Content-Type: application/json');
echo json_encode($response);
