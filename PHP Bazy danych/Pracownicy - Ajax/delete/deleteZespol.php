<?php

require_once 'database.php';

if (isset($_POST['delete_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE pracownicy SET id_zesp = NULL WHERE ID_ZESP = :ID_ZESP");
        $stmt->bindParam(':ID_ZESP', $_POST['delete_id'], PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $pdo->prepare("DELETE FROM zespoly WHERE ID_ZESP = :ID_ZESP");
        $stmt->bindParam(':ID_ZESP', $_POST['delete_id'], PDO::PARAM_INT);
        $stmt->execute();

        echo '<div class="alert alert-success">Zespół został usunięty!</div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Błąd podczas usuwania zespołu</div>';
    }
} else {
    echo '<div class="alert alert-danger">Nie podano ID zespołu</div>';
}
