<?php
/**
 * GET /api/czytelnicy/profil.php
 * Zwraca dane zalogowanego czytelnika.
 */

require_once __DIR__ . '/../../config/auth.php';

$uzytkownik = zalogowany_uzytkownik();

if ($uzytkownik['typ'] !== 'czytelnik') {
    http_response_code(403);
    echo json_encode(["status" => false, "komunikat" => "Brak uprawnień"]);
    exit;
}

try {
    $zapytanie = $pdo->prepare(
        "SELECT id, imie, nazwisko, adres, nr_dokumentu, identyfikator
         FROM czytelnicy WHERE id = :id"
    );
    $zapytanie->bindValue(':id', $uzytkownik['id'], PDO::PARAM_INT);
    $zapytanie->execute();
    $czytelnik = $zapytanie->fetch(PDO::FETCH_ASSOC);

    if (!$czytelnik) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Nie znaleziono użytkownika"]);
        exit;
    }

    echo json_encode([
        "status"    => true,
        "czytelnik" => $czytelnik
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
