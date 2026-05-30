<?php
/**
 * POST /api/rezerwacje/edytuj.php
 * Zmienia status rezerwacji.
 * Admin: może zmienić status na dowolny.
 * Czytelnik: może anulować tylko swoją aktywną rezerwację.
 *
 * Dane z formularza (POST):
 *   id      — ID rezerwacji
 *   status  — nowy status (aktywna, zrealizowana, anulowana)
 */

require_once __DIR__ . '/../../config/auth.php';

$uzytkownik = zalogowany_uzytkownik();
$id         = intval($_POST['id'] ?? 0);
$nowy_status = $_POST['status'] ?? '';

$dozwolone_statusy = ['aktywna', 'zrealizowana', 'anulowana'];

if ($id <= 0 || !in_array($nowy_status, $dozwolone_statusy)) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe dane"]);
    exit;
}

try {
    // Pobierz rezerwację
    $sprawdzenie = $pdo->prepare(
        "SELECT id, czytelnik_id, ksiazka_id, status FROM rezerwacje WHERE id = :id"
    );
    $sprawdzenie->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie->execute();
    $rezerwacja = $sprawdzenie->fetch(PDO::FETCH_ASSOC);

    if (!$rezerwacja) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Rezerwacja nie została znaleziona"]);
        exit;
    }

    // Czytelnik może anulować tylko swoją aktywną rezerwację
    if ($uzytkownik['typ'] === 'czytelnik') {
        if ($rezerwacja['czytelnik_id'] !== $uzytkownik['id']) {
            http_response_code(403);
            echo json_encode(["status" => false, "komunikat" => "To nie jest Twoja rezerwacja"]);
            exit;
        }
        if ($nowy_status !== 'anulowana') {
            http_response_code(403);
            echo json_encode(["status" => false, "komunikat" => "Możesz tylko anulować rezerwację"]);
            exit;
        }
        if ($rezerwacja['status'] !== 'aktywna') {
            http_response_code(409);
            echo json_encode(["status" => false, "komunikat" => "Możesz anulować tylko aktywną rezerwację"]);
            exit;
        }
    }

    $stary_status = $rezerwacja['status'];

    // Nie zmieniaj jeśli status się nie zmienia
    if ($stary_status === $nowy_status) {
        echo json_encode(["status" => true, "komunikat" => "Status bez zmian"]);
        exit;
    }

    $pdo->beginTransaction();

    // Aktualizuj status
    $zapytanie = $pdo->prepare(
        "UPDATE rezerwacje SET status = :status WHERE id = :id"
    );
    $zapytanie->bindValue(':status', $nowy_status, PDO::PARAM_STR);
    $zapytanie->bindValue(':id',     $id,          PDO::PARAM_INT);
    $zapytanie->execute();

    // Aktualizuj dostępność książki
    if ($nowy_status === 'anulowana' && $stary_status === 'aktywna') {
        // Anulacja → zwiększ dostępność
        $zapytanie2 = $pdo->prepare(
            "UPDATE ksiazki SET ilosc_dostepnych = ilosc_dostepnych + 1 WHERE id = :id"
        );
        $zapytanie2->bindValue(':id', $rezerwacja['ksiazka_id'], PDO::PARAM_INT);
        $zapytanie2->execute();
    } elseif ($nowy_status === 'aktywna' && $stary_status !== 'aktywna') {
        // Przywrócenie do aktywnej → zmniejsz dostępność
        $sprawdzenie_dostepnosc = $pdo->prepare(
            "SELECT ilosc_dostepnych FROM ksiazki WHERE id = :id"
        );
        $sprawdzenie_dostepnosc->bindValue(':id', $rezerwacja['ksiazka_id'], PDO::PARAM_INT);
        $sprawdzenie_dostepnosc->execute();
        $dostepnosc = $sprawdzenie_dostepnosc->fetchColumn();

        if ($dostepnosc <= 0) {
            $pdo->rollBack();
            http_response_code(409);
            echo json_encode(["status" => false, "komunikat" => "Brak dostępnych egzemplarzy"]);
            exit;
        }

        $zapytanie2 = $pdo->prepare(
            "UPDATE ksiazki SET ilosc_dostepnych = ilosc_dostepnych - 1 WHERE id = :id"
        );
        $zapytanie2->bindValue(':id', $rezerwacja['ksiazka_id'], PDO::PARAM_INT);
        $zapytanie2->execute();
    }

    $pdo->commit();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Status rezerwacji został zmieniony na '$nowy_status'"
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
