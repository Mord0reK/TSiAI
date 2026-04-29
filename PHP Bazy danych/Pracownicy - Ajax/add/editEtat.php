<?php

require_once '../get/database.php';

$response = array(
    'success' => false,
    'message' => '',
    'errors' => array()
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nazwa_stara = isset($_POST['nazwa']) ? trim($_POST['nazwa']) : '';
    $nazwa_nowa = isset($_POST['nazwa_nowa']) ? trim($_POST['nazwa_nowa']) : '';
    $placa_od = isset($_POST['placa_od']) ? floatval($_POST['placa_od']) : 0;
    $placa_do = isset($_POST['placa_do']) ? floatval($_POST['placa_do']) : 0;
    
    // Walidacja nazwy starej
    if (empty($nazwa_stara)) {
        $response['errors']['nazwa'] = 'Błąd: Brak starej nazwy etatu';
    }
    
    // Walidacja nazwy nowej
    if (empty($nazwa_nowa)) {
        $response['errors']['nazwa_nowa'] = 'Nie podano nazwy etatu';
    } elseif (mb_strlen($nazwa_nowa) > 30) {
        $response['errors']['nazwa_nowa'] = 'Nazwa etatu nie może być dłuższa niż 30 znaków';
    }
    
    // Walidacja płac
    if ($placa_od <= 0) {
        $response['errors']['placa_od'] = 'Płaca od musi być większa niż 0';
    }
    
    if ($placa_do <= 0) {
        $response['errors']['placa_do'] = 'Płaca do musi być większa niż 0';
    }
    
    if ($placa_od > $placa_do) {
        $response['errors']['placa_od'] = 'Płaca od nie może być większa niż płaca do';
    }
    
    // Jeśli brak błędów - aktualizuj w bazie
    if (empty($response['errors'])) {
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
                $response['success'] = true;
                $response['message'] = 'Etat został zaktualizowany!';
            } else {
                $response['success'] = false;
                $response['message'] = 'Błąd podczas aktualizacji etatu';
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
