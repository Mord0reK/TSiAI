<?php

require_once '../get/database.php';

$response = array(
    'success' => false,
    'message' => ''
);

if (isset($_POST['delete_id'])) {
    try {
        // Najpierw usuń szefów z tego pracownika
        $stmt = $pdo->prepare("UPDATE pracownicy SET ID_SZEFA = NULL WHERE ID_SZEFA = :ID_SZEFA");
        $stmt->bindParam(':ID_SZEFA', $_POST['delete_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Potem usuń pracownika
        $stmt = $pdo->prepare("DELETE FROM pracownicy WHERE ID_PRAC = :ID_PRAC");
        $stmt->bindParam(':ID_PRAC', $_POST['delete_id'], PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Pracownik został usunięty!';
        } else {
            $response['success'] = false;
            $response['message'] = 'Błąd podczas usuwania pracownika';
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'Błąd: ' . $e->getMessage();
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Nie podano ID pracownika';
}

header('Content-Type: application/json');
echo json_encode($response);
