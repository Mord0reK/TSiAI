<?php
/**
 * POST /api/rezerwacje/dodaj.php
 * Dodaje nową rezerwację.
 * Czytelnik rezerwuje dla siebie, admin może rezerwować za czytelnika.
 *
 * Dane z formularza (POST):
 *   ksiazka_id     — ID książki do zarezerwowania
 *   czytelnik_id   — ID czytelnika (tylko admin, opcjonalne — domyślnie zalogowany)
 */

require_once __DIR__ . '/../../config/auth.php';

$uzytkownik = zalogowany_uzytkownik();
$ksiazka_id   = intval($_POST['ksiazka_id'] ?? 0);
$czytelnik_id = intval($_POST['czytelnik_id'] ?? 0);

// Dla czytelnika — zawsze rezerwuje na siebie
if ($uzytkownik['typ'] === 'czytelnik') {
    $czytelnik_id = $uzytkownik['id'];
}

// Walidacja
if ($ksiazka_id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe ID książki"]);
    exit;
}

if ($czytelnik_id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe ID czytelnika"]);
    exit;
}

// Admin może rezerwować za czytelnika, czytelnik nie
if ($uzytkownik['typ'] === 'czytelnik' && $czytelnik_id !== $uzytkownik['id']) {
    http_response_code(403);
    echo json_encode(["status" => false, "komunikat" => "Nie możesz rezerwować za innego czytelnika"]);
    exit;
}

try {
    // Sprawdź czy książka istnieje i ma dostępne egzemplarze
    $sprawdzenie = $pdo->prepare(
        "SELECT id, ilosc_dostepnych FROM ksiazki WHERE id = :id"
    );
    $sprawdzenie->bindValue(':id', $ksiazka_id, PDO::PARAM_INT);
    $sprawdzenie->execute();
    $ksiazka = $sprawdzenie->fetch(PDO::FETCH_ASSOC);

    if (!$ksiazka) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Książka nie została znaleziona"]);
        exit;
    }

    if ($ksiazka['ilosc_dostepnych'] <= 0) {
        http_response_code(409);
        echo json_encode(["status" => false, "komunikat" => "Brak dostępnych egzemplarzy"]);
        exit;
    }

    // Sprawdź czy czytelnik nie ma już aktywnej rezerwacji na tę książkę
    $sprawdzenie2 = $pdo->prepare(
        "SELECT id FROM rezerwacje
         WHERE czytelnik_id = :czytelnik_id AND ksiazka_id = :ksiazka_id AND status = 'aktywna'"
    );
    $sprawdzenie2->bindValue(':czytelnik_id', $czytelnik_id, PDO::PARAM_INT);
    $sprawdzenie2->bindValue(':ksiazka_id',   $ksiazka_id,   PDO::PARAM_INT);
    $sprawdzenie2->execute();

    if ($sprawdzenie2->fetch()) {
        http_response_code(409);
        echo json_encode(["status" => false, "komunikat" => "Masz już aktywną rezerwację na tę książkę"]);
        exit;
    }

    // Rozpocznij transakcję
    $pdo->beginTransaction();

    // Dodaj rezerwację
    $zapytanie = $pdo->prepare(
        "INSERT INTO rezerwacje (czytelnik_id, ksiazka_id) VALUES (:czytelnik_id, :ksiazka_id)"
    );
    $zapytanie->bindValue(':czytelnik_id', $czytelnik_id, PDO::PARAM_INT);
    $zapytanie->bindValue(':ksiazka_id',   $ksiazka_id,   PDO::PARAM_INT);
    $zapytanie->execute();

    // Zmniejsz dostępność
    $zapytanie2 = $pdo->prepare(
        "UPDATE ksiazki SET ilosc_dostepnych = ilosc_dostepnych - 1 WHERE id = :id"
    );
    $zapytanie2->bindValue(':id', $ksiazka_id, PDO::PARAM_INT);
    $zapytanie2->execute();

    $pdo->commit();

    $nowe_id = $pdo->lastInsertId();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Rezerwacja została utworzona",
        "id"        => (int)$nowe_id
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
