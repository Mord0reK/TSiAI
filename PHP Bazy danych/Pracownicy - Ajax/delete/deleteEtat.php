<?php

require_once 'database.php';

if (isset($_POST['delete_nazwa'])) {
    try {
        $stmt = $pdo->prepare("UPDATE pracownicy SET ETAT = NULL WHERE ETAT = :NAZWA");
        $stmt->bindParam(':NAZWA', $_POST['delete_nazwa'], PDO::PARAM_STR);
        $stmt->execute();

        $stmt = $pdo->prepare("DELETE FROM etaty WHERE NAZWA = :NAZWA");
        $stmt->bindParam(':NAZWA', $_POST['delete_nazwa'], PDO::PARAM_STR);
        $stmt->execute();

        echo '<div class="alert alert-success">Etat został usunięty!</div>';
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Błąd podczas usuwania etatu</div>';
    }
} else {
    echo '<div class="alert alert-danger">Nie podano nazwy etatu</div>';
}
