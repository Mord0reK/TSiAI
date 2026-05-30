<?php
/**
 * POST /api/wypozyczenia/zwrot.php
 * Przyjmuje zwrot książki.
 * Tylko admin.
 *
 * Dane z formularza (POST):
 *   id — ID wypożyczenia do zwrotu
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe ID wypożyczenia"]);
    exit;
}

try {
    // Pobierz wypożyczenie
    $sprawdzenie = $pdo->prepare(
        "SELECT id, ksiazka_id, status FROM wypozyczenia WHERE id = :id"
    );
    $sprawdzenie->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie->execute();
    $wypozyczenie = $sprawdzenie->fetch(PDO::FETCH_ASSOC);

    if (!$wypozyczenie) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Wypożyczenie nie zostało znalezione"]);
        exit;
    }

    if ($wypozyczenie['status'] !== 'aktywne') {
        http_response_code(409);
        echo json_encode([
            "status"    => false,
            "komunikat" => "Wypożyczenie nie jest aktywne (status: {$wypozyczenie['status']})"
        ]);
        exit;
    }

    $pdo->beginTransaction();

    // Oznacz jako zwrócone
    $zapytanie = $pdo->prepare(
        "UPDATE wypozyczenia SET status = 'zwrocone', data_zwrotu = NOW() WHERE id = :id"
    );
    $zapytanie->bindValue(':id', $id, PDO::PARAM_INT);
    $zapytanie->execute();

    // Zwiększ dostępność książki
    $zapytanie2 = $pdo->prepare(
        "UPDATE ksiazki SET ilosc_dostepnych = ilosc_dostepnych + 1 WHERE id = :id"
    );
    $zapytanie2->bindValue(':id', $wypozyczenie['ksiazka_id'], PDO::PARAM_INT);
    $zapytanie2->execute();

    $pdo->commit();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Zwrot został zarejestrowany"
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
