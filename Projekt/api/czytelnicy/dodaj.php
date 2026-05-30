<?php
/**
 * POST /api/czytelnicy/dodaj.php
 * Dodaje nowego czytelnika do bazy.
 * Wymaga zalogowania jako admin.
 *
 * Dane z formularza (POST):
 *   imie          — imię czytelnika
 *   nazwisko      — nazwisko czytelnika
 *   adres         — adres zamieszkania
 *   nr_dokumentu  — numer dokumentu tożsamości
 *   identyfikator — unikalny identyfikator (login do logowania)
 *   haslo         — hasło startowe (zostanie zahashowane)
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

// Pobranie danych
$imie          = trim($_POST['imie'] ?? '');
$nazwisko      = trim($_POST['nazwisko'] ?? '');
$adres         = trim($_POST['adres'] ?? '');
$nr_dokumentu  = trim($_POST['nr_dokumentu'] ?? '');
$identyfikator = trim($_POST['identyfikator'] ?? '');
$haslo         = $_POST['haslo'] ?? '';

// Walidacja
if (empty($imie) || empty($nazwisko) || empty($adres) || empty($nr_dokumentu) || empty($identyfikator) || empty($haslo)) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Wypełnij wszystkie wymagane pola"]);
    exit;
}

try {
    // Sprawdź czy identyfikator jest już zajęty
    $sprawdzenie = $pdo->prepare("SELECT id FROM czytelnicy WHERE identyfikator = :identyfikator");
    $sprawdzenie->bindValue(':identyfikator', $identyfikator, PDO::PARAM_STR);
    $sprawdzenie->execute();

    if ($sprawdzenie->fetch()) {
        http_response_code(409);
        echo json_encode(["status" => false, "komunikat" => "Identifikator jest już zajęty"]);
        exit;
    }

    // Hash hasła (bcrypt)
    $haslo_hash = password_hash($haslo, PASSWORD_BCRYPT);

    // Wstaw czytelnika
    $zapytanie = $pdo->prepare(
        "INSERT INTO czytelnicy (imie, nazwisko, adres, nr_dokumentu, identyfikator, haslo, zmien_haslo)
         VALUES (:imie, :nazwisko, :adres, :nr_dokumentu, :identyfikator, :haslo, 1)"
    );

    $zapytanie->bindValue(':imie',          $imie,          PDO::PARAM_STR);
    $zapytanie->bindValue(':nazwisko',      $nazwisko,      PDO::PARAM_STR);
    $zapytanie->bindValue(':adres',         $adres,         PDO::PARAM_STR);
    $zapytanie->bindValue(':nr_dokumentu',  $nr_dokumentu,  PDO::PARAM_STR);
    $zapytanie->bindValue(':identyfikator', $identyfikator, PDO::PARAM_STR);
    $zapytanie->bindValue(':haslo',         $haslo_hash,    PDO::PARAM_STR);
    $zapytanie->execute();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Czytelnik został dodany"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
