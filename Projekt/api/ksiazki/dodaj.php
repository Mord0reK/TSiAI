<?php
/**
 * POST /api/ksiazki/dodaj.php
 * Dodaje nową książkę do bazy.
 * Wymaga zalogowania jako admin.
 *
 * Dane z formularza (POST):
 *   autor         — imię i nazwisko autora
 *   tytul         — tytuł książki
 *   wydawnictwo   — nazwa wydawnictwa
 *   rok_wydania   — rok wydania (4-cyfrowy)
 *   ilosc         — ilość egzemplarzy (domyślnie 1)
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

// Pobranie i walidacja danych
$autor       = trim($_POST['autor'] ?? '');
$tytul       = trim($_POST['tytul'] ?? '');
$wydawnictwo = trim($_POST['wydawnictwo'] ?? '');
$rok_wydania = intval($_POST['rok_wydania'] ?? 0);
$ilosc       = max(1, intval($_POST['ilosc'] ?? 1));

if (empty($autor) || empty($tytul) || empty($wydawnictwo) || $rok_wydania < 1000 || $rok_wydania > 2100) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Wypełnij wszystkie wymagane pola (rok: 1000-9999)"]);
    exit;
}

try {
    $zapytanie = $pdo->prepare(
        "INSERT INTO ksiazki (autor, tytul, wydawnictwo, rok_wydania, ilosc_calkowita, ilosc_dostepnych)
         VALUES (:autor, :tytul, :wydawnictwo, :rok_wydania, :ilosc, :ilosc)"
    );

    // bindujemy value z zmiennych
    $zapytanie->bindValue(':autor',       $autor,       PDO::PARAM_STR);
    $zapytanie->bindValue(':tytul',       $tytul,       PDO::PARAM_STR);
    $zapytanie->bindValue(':wydawnictwo', $wydawnictwo, PDO::PARAM_STR);
    $zapytanie->bindValue(':rok_wydania', $rok_wydania, PDO::PARAM_INT);
    $zapytanie->bindValue(':ilosc',       $ilosc,       PDO::PARAM_INT);
    $zapytanie->execute();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Książka została dodana"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
