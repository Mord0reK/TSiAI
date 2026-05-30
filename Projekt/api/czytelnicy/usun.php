<?php
/**
 * POST /api/czytelnicy/usun.php
 * Usuwa czytelnika z bazy.
 * Wymaga zalogowania jako admin.
 *
 * Dane z formularza (POST):
 *   id — identyfikator czytelnika do usunięcia
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe ID czytelnika"]);
    exit;
}

try {
    // Sprawdź czy czytelnik istnieje
    $sprawdzenie = $pdo->prepare("SELECT id FROM czytelnicy WHERE id = :id");
    $sprawdzenie->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie->execute();

    if (!$sprawdzenie->fetch()) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Czytelnik nie został znaleziony"]);
        exit;
    }

    // Sprawdź czy są aktywne rezerwacje lub wypożyczenia
    $sprawdzenie2 = $pdo->prepare(
        "SELECT COUNT(*) FROM rezerwacje WHERE czytelnik_id = :id AND status = 'aktywna'"
    );
    $sprawdzenie2->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie2->execute();
    $aktywne_rezerwacje = $sprawdzenie2->fetchColumn();

    $sprawdzenie3 = $pdo->prepare(
        "SELECT COUNT(*) FROM wypozyczenia WHERE czytelnik_id = :id AND status = 'aktywne'"
    );
    $sprawdzenie3->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie3->execute();
    $aktywne_wypozyczenia = $sprawdzenie3->fetchColumn();

    if ($aktywne_rezerwacje > 0 || $aktywne_wypozyczenia > 0) {
        http_response_code(409);
        echo json_encode([
            "status"    => false,
            "komunikat" => "Nie można usunąć czytelnika — posiada aktywne rezerwacje lub wypożyczenia"
        ]);
        exit;
    }

    // Usuń czytelnika (tu jest kaskadowe usuwanie w db wiec wywali też rezerwacje i wypozyczenia)
    $zapytanie = $pdo->prepare("DELETE FROM czytelnicy WHERE id = :id");
    $zapytanie->bindValue(':id', $id, PDO::PARAM_INT);
    $zapytanie->execute();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Czytelnik został usunięty"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
