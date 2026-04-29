<?php

require_once '../get/database.php';

$response = array(
    'success' => false,
    'message' => ''
);

if (isset($_POST['delete_id'])) {
    try {
        // Najpierw ustaw id_zesp na NULL dla pracowników z tym zespołem
        $stmt = $pdo->prepare("UPDATE pracownicy SET id_zesp = NULL WHERE ID_ZESP = :ID_ZESP");
        $stmt->bindParam(':ID_ZESP', $_POST['delete_id'], PDO::PARAM_INT);
        $stmt->execute();

        // Potem usuń zespół
        $stmt = $pdo->prepare("DELETE FROM zespoly WHERE ID_ZESP = :ID_ZESP");
        $stmt->bindParam(':ID_ZESP', $_POST['delete_id'], PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            $response['success'] = true;
            $response['message'] = 'Zespół został usunięty!';
        } else {
            $response['success'] = false;
            $response['message'] = 'Błąd podczas usuwania zespołu';
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['message'] = 'Błąd: ' . $e->getMessage();
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Nie podano ID zespołu';
}

header('Content-Type: application/json');
echo json_encode($response);
