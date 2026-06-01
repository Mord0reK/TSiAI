<?php
/**
 * POST /api/ksiazki/dodaj.php
 * Dodaje nową książkę do bazy.
 * Wymaga zalogowania jako admin.
 *
 * Dane z formularza (POST):
 *   autor         — imię i nazwisko autora (max 100 znaków)
 *   tytul         — tytuł książki (max 100 znaków)
 *   wydawnictwo   — nazwa wydawnictwa (max 100 znaków)
 *   rok_wydania   — rok wydania (1901 - aktualny rok, ograniczenie typu YEAR w MySQL)
 *   ilosc         — ilość egzemplarzy (min. 1)
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

// Pobranie danych
$autor       = trim($_POST['autor'] ?? '');
$tytul       = trim($_POST['tytul'] ?? '');
$wydawnictwo = trim($_POST['wydawnictwo'] ?? '');
$rok_wydania = intval($_POST['rok_wydania'] ?? 0);
$ilosc       = max(1, intval($_POST['ilosc'] ?? 1));

// Granice walidacji
$aktualny_rok = (int)date('Y');
$min_rok      = 1901; // ograniczenie typu YEAR w MySQL
$max_dl_tekst = 100;

// Walidacja pól tekstowych — wymagane + długość
if ($tytul === '' || mb_strlen($tytul) > $max_dl_tekst) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Tytuł jest wymagany (max $max_dl_tekst znaków)"]);
    exit;
}
if ($autor === '' || mb_strlen($autor) > $max_dl_tekst) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Autor jest wymagany (max $max_dl_tekst znaków)"]);
    exit;
}
if ($wydawnictwo === '' || mb_strlen($wydawnictwo) > $max_dl_tekst) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Wydawnictwo jest wymagane (max $max_dl_tekst znaków)"]);
    exit;
}

// Walidacja roku — zakres 1901..aktualny rok (typ YEAR w MySQL)
if ($rok_wydania < $min_rok || $rok_wydania > $aktualny_rok) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Rok wydania musi być w zakresie $min_rok - $aktualny_rok"]);
    exit;
}

try {
    $zapytanie = $pdo->prepare(
        "INSERT INTO ksiazki (autor, tytul, wydawnictwo, rok_wydania, ilosc_calkowita, ilosc_dostepnych)
         VALUES (:autor, :tytul, :wydawnictwo, :rok_wydania, :ilosc, :ilosc)"
    );

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
