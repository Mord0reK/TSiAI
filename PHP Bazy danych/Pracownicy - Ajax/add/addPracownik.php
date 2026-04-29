<?php

require_once '../get/database.php';

$response = array(
    'success' => false,
    'message' => '',
    'errors' => array()
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $imie = isset($_POST['imie']) ? trim($_POST['imie']) : '';
    $nazwisko = isset($_POST['nazwisko']) ? trim($_POST['nazwisko']) : '';
    $etat = isset($_POST['etat']) ? trim($_POST['etat']) : '';
    $szef = isset($_POST['szef']) ? $_POST['szef'] : null;
    $zespol = isset($_POST['zespol']) ? $_POST['zespol'] : null;
    $data_zatrudnienia = isset($_POST['data_zatrudnienia']) ? $_POST['data_zatrudnienia'] : '';
    $placa_pod = isset($_POST['placa_pod']) ? $_POST['placa_pod'] : '';
    $placa_dod = isset($_POST['placa_dod']) ? $_POST['placa_dod'] : '';
    
    // Walidacja imienia
    if (empty($imie)) {
        $response['errors']['imie'] = 'Nie podano imienia';
    } elseif (mb_strlen($imie) > 20) {
        $response['errors']['imie'] = 'Imię nie może być dłuższe niż 20 znaków';
    } elseif (preg_match('/[^a-zA-ZąĆćęłńóśżźĄĆĘŁŃÓŚŻŹ-]/u', $imie)) {
        $response['errors']['imie'] = 'W polu znalazły się inne znaki niż litery';
    }
    
    // Walidacja nazwiska
    if (empty($nazwisko)) {
        $response['errors']['nazwisko'] = 'Nie podano nazwiska';
    } elseif (mb_strlen($nazwisko) > 15) {
        $response['errors']['nazwisko'] = 'Nazwisko nie może być dłuższe niż 15 znaków';
    } elseif (preg_match('/[^a-zA-ZąĆćęłńóśżźĄĆĘŁŃÓŚŻŹ-]/u', $nazwisko)) {
        $response['errors']['nazwisko'] = 'W polu znalazły się inne znaki niż litery';
    }
    
    // Walidacja etatu
    if (empty($etat) || $etat === 'Wybierz etat') {
        $response['errors']['etat'] = 'Nie wybrano etatu';
    }
    
    // Walidacja zespołu
    if (empty($zespol) || $zespol === '0') {
        $response['errors']['zespol'] = 'Nie podano zespołu';
    }
    
    // Walidacja daty zatrudnienia
    if (empty($data_zatrudnienia)) {
        $response['errors']['data_zatrudnienia'] = 'Nie podano daty zatrudnienia';
    } else {
        $data = DateTime::createFromFormat('Y-m-d', $data_zatrudnienia);
        $dzisiaj = new DateTime();
        $min_data = (clone $dzisiaj)->modify('-100 years');
        $max_data = (clone $dzisiaj)->modify('+100 years');
        
        if ($data < $min_data || $data > $max_data) {
            $response['errors']['data_zatrudnienia'] = 'Data nie może być o 100 lat do tyłu ani 100 lat do przodu';
        }
    }
    
    // Walidacja płac
    if (empty($placa_pod)) {
        $response['errors']['placa_pod'] = 'Nie podano płacy podstawowej';
    } elseif (!is_numeric($placa_pod) || $placa_pod <= 0) {
        $response['errors']['placa_pod'] = 'Płaca podstawowa musi być liczbą dodatnią';
    } else {
        // Walidacja czy płaca mieści się w przedziale etatu
        try {
            $stmt = $pdo->prepare('SELECT PLACA_OD, PLACA_DO FROM etaty WHERE NAZWA = :etat');
            $stmt->bindParam(':etat', $etat, PDO::PARAM_STR);
            $stmt->execute();
            $etat_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($etat_data) {
                $placa_pod_num = floatval($placa_pod);
                if ($placa_pod_num < $etat_data['PLACA_OD'] || $placa_pod_num > $etat_data['PLACA_DO']) {
                    $response['errors']['placa_pod'] = 'Płaca podstawowa musi być w przedziale ' . $etat_data['PLACA_OD'] . ' - ' . $etat_data['PLACA_DO'];
                }
            }
        } catch (Exception $e) {
            // Ignoruj błędy walidacji zakresu
        }
    }
    
    if (empty($placa_dod) || !is_numeric($placa_dod)) {
        $placa_dod = 0;
    } elseif ($placa_dod < 0) {
        $response['errors']['placa_dod'] = 'Płaca dodatkowa nie może być ujemna';
    }
    
    // Jeśli brak błędów - zapisz do bazy
    if (empty($response['errors'])) {
        try {
            // Konwertuj szefa - jeśli 0 to NULL
            $szef_value = ($szef === '0' || empty($szef)) ? null : $szef;
            
            $stmt = $pdo->prepare("
                INSERT INTO pracownicy (IMIE, NAZWISKO, ETAT, ID_SZEFA, ZATRUDNIONY, PLACA_POD, PLACA_DOD, ID_ZESP)
                VALUES (:imie, :nazwisko, :etat, :szef, :data, :placa_pod, :placa_dod, :zespol)
            ");
            
            $stmt->bindParam(':imie', $imie, PDO::PARAM_STR);
            $stmt->bindParam(':nazwisko', $nazwisko, PDO::PARAM_STR);
            $stmt->bindParam(':etat', $etat, PDO::PARAM_STR);
            $stmt->bindParam(':szef', $szef_value, PDO::PARAM_INT);
            $stmt->bindParam(':data', $data_zatrudnienia, PDO::PARAM_STR);
            $stmt->bindParam(':placa_pod', $placa_pod);
            $stmt->bindParam(':placa_dod', $placa_dod);
            $stmt->bindParam(':zespol', $zespol, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Pracownik został dodany!';
            } else {
                $response['success'] = false;
                $response['message'] = 'Błąd podczas dodawania pracownika';
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
