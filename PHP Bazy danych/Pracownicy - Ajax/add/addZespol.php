<?php

require_once '../get/database.php';

$response = array(
    'success' => false,
    'message' => '',
    'errors' => array()
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nazwa = isset($_POST['nazwa']) ? trim($_POST['nazwa']) : '';
    $adres = isset($_POST['adres']) ? trim($_POST['adres']) : '';
    
    // Walidacja nazwy
    if (empty($nazwa)) {
        $response['errors']['nazwa'] = 'Nie podano nazwy zespołu';
    } elseif (mb_strlen($nazwa) > 30) {
        $response['errors']['nazwa'] = 'Nazwa zespołu nie może być dłuższa niż 30 znaków';
    }
    
    // Walidacja adresu
    if (empty($adres)) {
        $response['errors']['adres'] = 'Nie podano adresu';
    } elseif (mb_strlen($adres) > 50) {
        $response['errors']['adres'] = 'Adres nie może być dłuższy niż 50 znaków';
    }
    
    // Jeśli brak błędów - zapisz do bazy
    if (empty($response['errors'])) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO zespoly (NAZWA, ADRES)
                VALUES (:nazwa, :adres)
            ");
            
            $stmt->bindParam(':nazwa', $nazwa, PDO::PARAM_STR);
            $stmt->bindParam(':adres', $adres, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Zespół został dodany!';
            } else {
                $response['success'] = false;
                $response['message'] = 'Błąd podczas dodawania zespołu';
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
