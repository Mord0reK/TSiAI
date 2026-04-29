<?php

require_once '../get/database.php';

$response = array(
    'success' => false,
    'message' => ''
);

if (isset($_POST['delete_nazwa'])) {
    try {
        // Najpierw ustaw ETAT na NULL dla pracowników z tym etatem
        $stmt = $pdo->prepare("UPDATE pracownicy SET ETAT = NULL WHERE ETAT = :NAZWA");
        $stmt->bindParam(':NAZWA', $_POST['delete_nazwa'], PDO::PARAM_STR);
        $stmt->execute();

        // Potem usuń etat
        $stmt = $pdo->prepare("DELETE FROM etaty WHERE NAZWA = :NAZWA");
        $stmt->bindParam(':NAZWA', $_POST['delete_nazwa'], PDO::PARAM_STR);
        $result = $stmt->execute();

        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Etat został usunięty!';
        } else {
            $response['success'] = false;
            $response['message'] = 'Błąd podczas usuwania etatu';
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'Błąd: ' . $e->getMessage();
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Nie podano nazwy etatu';
}

header('Content-Type: application/json');
echo json_encode($response);
