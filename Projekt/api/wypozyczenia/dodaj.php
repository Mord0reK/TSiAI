<?php
/**
 * POST /api/wypozyczenia/dodaj.php
 * Realizuje wypożyczenie książki na podstawie aktywnej rezerwacji.
 * Tylko admin.
 *
 * Dane z formularza (POST):
 *   rezerwacja_id  — ID aktywnej rezerwacji
 *   termin_zwrotu  — termin zwrotu (format: YYYY-MM-DD)
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

$rezerwacja_id = intval($_POST['rezerwacja_id'] ?? 0);
$termin_zwrotu = trim($_POST['termin_zwrotu'] ?? '');

if ($rezerwacja_id <= 0 || empty($termin_zwrotu)) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe dane"]);
    exit;
}

// Walidacja formatu daty
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $termin_zwrotu)) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowy format daty (wymagany: YYYY-MM-DD)"]);
    exit;
}

try {
    // Pobierz rezerwację
    $sprawdzenie = $pdo->prepare(
        "SELECT id, czytelnik_id, ksiazka_id, status FROM rezerwacje WHERE id = :id"
    );
    $sprawdzenie->bindValue(':id', $rezerwacja_id, PDO::PARAM_INT);
    $sprawdzenie->execute();
    $rezerwacja = $sprawdzenie->fetch(PDO::FETCH_ASSOC);

    if (!$rezerwacja) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Rezerwacja nie została znaleziona"]);
        exit;
    }

    if ($rezerwacja['status'] !== 'aktywna') {
        http_response_code(409);
        echo json_encode([
            "status"    => false,
            "komunikat" => "Rezerwacja nie jest aktywna (status: {$rezerwacja['status']})"
        ]);
        exit;
    }

    // Sprawdź czy nie ma już aktywnego wypożyczenia tej książki przez tego czytelnika
    $sprawdzenie2 = $pdo->prepare(
        "SELECT id FROM wypozyczenia
         WHERE czytelnik_id = :czytelnik_id AND ksiazka_id = :ksiazka_id AND status = 'aktywne'"
    );
    $sprawdzenie2->bindValue(':czytelnik_id', $rezerwacja['czytelnik_id'], PDO::PARAM_INT);
    $sprawdzenie2->bindValue(':ksiazka_id',   $rezerwacja['ksiazka_id'],   PDO::PARAM_INT);
    $sprawdzenie2->execute();

    if ($sprawdzenie2->fetch()) {
        http_response_code(409);
        echo json_encode([
            "status"    => false,
            "komunikat" => "Czytelnik ma już aktywne wypożyczenie tej książki"
        ]);
        exit;
    }

    $pdo->beginTransaction();

    // Utwórz wypożyczenie
    $zapytanie = $pdo->prepare(
        "INSERT INTO wypozyczenia (czytelnik_id, ksiazka_id, termin_zwrotu)
         VALUES (:czytelnik_id, :ksiazka_id, :termin_zwrotu)"
    );
    $zapytanie->bindValue(':czytelnik_id',  $rezerwacja['czytelnik_id'], PDO::PARAM_INT);
    $zapytanie->bindValue(':ksiazka_id',    $rezerwacja['ksiazka_id'],   PDO::PARAM_INT);
    $zapytanie->bindValue(':termin_zwrotu', $termin_zwrotu,              PDO::PARAM_STR);
    $zapytanie->execute();

    // Zmień status rezerwacji na "zrealizowana"
    $zapytanie2 = $pdo->prepare(
        "UPDATE rezerwacje SET status = 'zrealizowana' WHERE id = :id"
    );
    $zapytanie2->bindValue(':id', $rezerwacja_id, PDO::PARAM_INT);
    $zapytanie2->execute();

    $pdo->commit();

    $nowe_id = $pdo->lastInsertId();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Wypożyczenie zostało zrealizowane",
        "id"        => (int)$nowe_id
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
