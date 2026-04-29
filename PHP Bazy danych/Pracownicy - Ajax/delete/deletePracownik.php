<?php

require_once 'database.php';

if (isset($_POST['delete_id'])) {
    try {
        $stmt = $pdo->prepare("UPDATE pracownicy SET ID_SZEFA = NULL WHERE ID_SZEFA = :ID_SZEFA");
        $stmt->bindParam(':ID_SZEFA', $_POST['delete_id'], PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $pdo->prepare("DELETE FROM pracownicy WHERE ID_PRAC = :ID_PRAC");
        $stmt->bindParam(':ID_PRAC', $_POST['delete_id'], PDO::PARAM_INT);
        $stmt->execute();

        echo '<div class="alert alert-success">Pracownik został usunięty!</div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Błąd podczas usuwania pracownika</div>';
    }
} else {
    echo '<div class="alert alert-danger">Nie podano ID pracownika</div>';
}
