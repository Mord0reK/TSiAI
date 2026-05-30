<?php
/**
 * POST /api/rezerwacje/usun.php
 * Usuwa rezerwację.
 * Tylko admin może usuwać rezerwacje.
 * Nie można usunąć aktywnej rezerwacji — najpierw anuluj.
 *
 * Dane z formularza (POST):
 *   id — ID rezerwacji do usunięcia
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe ID rezerwacji"]);
    exit;
}

try {
    // Pobierz rezerwację
    $sprawdzenie = $pdo->prepare(
        "SELECT id, status FROM rezerwacje WHERE id = :id"
    );
    $sprawdzenie->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie->execute();
    $rezerwacja = $sprawdzenie->fetch(PDO::FETCH_ASSOC);

    if (!$rezerwacja) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Rezerwacja nie została znaleziona"]);
        exit;
    }

    if ($rezerwacja['status'] === 'aktywna') {
        http_response_code(409);
        echo json_encode([
            "status"    => false,
            "komunikat" => "Nie można usunąć aktywnej rezerwacji — najpierw ją anuluj"
        ]);
        exit;
    }

    // Usuń rezerwację
    $zapytanie = $pdo->prepare("DELETE FROM rezerwacje WHERE id = :id");
    $zapytanie->bindValue(':id', $id, PDO::PARAM_INT);
    $zapytanie->execute();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Rezerwacja została usunięta"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
