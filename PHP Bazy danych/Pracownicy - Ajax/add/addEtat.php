<?php

require_once '../get/database.php';

$response = array(
    'success' => false,
    'message' => '',
    'errors' => array()
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nazwa = isset($_POST['nazwa']) ? trim($_POST['nazwa']) : '';
    $placa_od = isset($_POST['placa_od']) ? $_POST['placa_od'] : '';
    $placa_do = isset($_POST['placa_do']) ? $_POST['placa_do'] : '';
    
    // Walidacja nazwy
    if (empty($nazwa)) {
        $response['errors']['nazwa'] = 'Nie podano nazwy etatu';
    } elseif (mb_strlen($nazwa) > 30) {
        $response['errors']['nazwa'] = 'Nazwa etatu nie może być dłuższa niż 30 znaków';
    }
    
    // Walidacja płac
    if (empty($placa_od)) {
        $response['errors']['placa_od'] = 'Nie podano płacy od';
    } elseif (!is_numeric($placa_od) || $placa_od <= 0) {
        $response['errors']['placa_od'] = 'Płaca od musi być liczbą dodatnią';
    }
    
    if (empty($placa_do)) {
        $response['errors']['placa_do'] = 'Nie podano płacy do';
    } elseif (!is_numeric($placa_do) || $placa_do <= 0) {
        $response['errors']['placa_do'] = 'Płaca do musi być liczbą dodatnią';
    }
    
    // Sprawdź czy płaca_od <= płaca_do
    if (!empty($placa_od) && !empty($placa_do) && is_numeric($placa_od) && is_numeric($placa_do)) {
        if ($placa_od > $placa_do) {
            $response['errors']['placa_do'] = 'Płaca do musi być większa lub równa płacy od';
        }
    }
    
    // Jeśli brak błędów - zapisz do bazy
    if (empty($response['errors'])) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO etaty (NAZWA, PLACA_OD, PLACA_DO)
                VALUES (:nazwa, :placa_od, :placa_do)
            ");
            
            $stmt->bindParam(':nazwa', $nazwa, PDO::PARAM_STR);
            $stmt->bindParam(':placa_od', $placa_od);
            $stmt->bindParam(':placa_do', $placa_do);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Etat został dodany!';
            } else {
                $response['success'] = false;
                $response['message'] = 'Błąd podczas dodawania etatu';
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
