<?php
/**
 * POST /api/ksiazki/usun.php
 * Usuwa książkę z bazy.
 * Wymaga zalogowania jako admin.
 *
 * Dane z formularza (POST):
 *   id — identyfikator książki do usunięcia
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe ID książki"]);
    exit;
}

try {
    // Sprawdzamy czy książka istnieje
    $zapytanie = $pdo->prepare("SELECT id FROM ksiazki WHERE id = :id");
    $zapytanie->bindValue(':id', $id, PDO::PARAM_INT);
    $zapytanie->execute();

    if (!$zapytanie->fetch()) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Książka nie została znaleziona"]);
        exit;
    }

    // Sprawdź czy są aktywne rezerwacje lub wypożyczenia
    $sprawdzenie = $pdo->prepare(
        "SELECT COUNT(*) FROM rezerwacje WHERE ksiazka_id = :id AND status = 'aktywna'"
    );
    $sprawdzenie->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie->execute();
    $aktywne_rezerwacje = $sprawdzenie->fetchColumn();

    $sprawdzenie2 = $pdo->prepare(
        "SELECT COUNT(*) FROM wypozyczenia WHERE ksiazka_id = :id AND status = 'aktywne'"
    );
    $sprawdzenie2->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie2->execute();

    $aktywne_wypozyczenia = $sprawdzenie2->fetchColumn();

    if ($aktywne_rezerwacje > 0 || $aktywne_wypozyczenia > 0) {
        http_response_code(409);
        echo json_encode([
            "status"  => false,
            "komunikat" => "Nie można usunąć książki — posiada aktywne rezerwacje lub wypożyczenia"
        ]);
        exit;
    }

    // Usuń książkę (tu jest kaskadowe usuwanie w db wiec wywali też rezerwacje i wypozyczenia)
    $zapytanie = $pdo->prepare("DELETE FROM ksiazki WHERE id = :id");
    $zapytanie->bindValue(':id', $id, PDO::PARAM_INT);
    $zapytanie->execute();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Książka została usunięta"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
