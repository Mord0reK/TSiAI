<?php
/**
 * POST /api/czytelnicy/dodaj.php
 * Dodaje nowego czytelnika do bazy.
 * Wymaga zalogowania jako admin.
 *
 * Dane z formularza (POST):
 *   imie          — imię czytelnika (max 100 znaków)
 *   nazwisko      — nazwisko czytelnika (max 100 znaków)
 *   adres         — adres zamieszkania (max 255 znaków)
 *   nr_dokumentu  — numer dokumentu tożsamości (max 50 znaków)
 *   identyfikator — unikalny identyfikator (max 50 znaków)
 *   haslo         — hasło startowe (zostanie zahashowane)
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

// Granice walidacji
$max_dl_imie         = 100;
$max_dl_nazwisko     = 100;
$max_dl_adres        = 255;
$max_dl_nr_dokumentu = 50;
$max_dl_ident        = 50;

// Pobranie danych
$imie          = trim($_POST['imie'] ?? '');
$nazwisko      = trim($_POST['nazwisko'] ?? '');
$adres         = trim($_POST['adres'] ?? '');
$nr_dokumentu  = trim($_POST['nr_dokumentu'] ?? '');
$identyfikator = trim($_POST['identyfikator'] ?? '');
$haslo         = $_POST['haslo'] ?? '';

// Walidacja pól wymaganych
if (empty($imie) || empty($nazwisko) || empty($adres) || empty($nr_dokumentu) || empty($identyfikator) || empty($haslo)) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Wypełnij wszystkie wymagane pola"]);
    exit;
}

// Walidacja długości pól tekstowych
if (mb_strlen($imie) > $max_dl_imie) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Imię może mieć maksymalnie $max_dl_imie znaków"]);
    exit;
}
if (mb_strlen($nazwisko) > $max_dl_nazwisko) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nazwisko może mieć maksymalnie $max_dl_nazwisko znaków"]);
    exit;
}
if (mb_strlen($adres) > $max_dl_adres) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Adres może mieć maksymalnie $max_dl_adres znaków"]);
    exit;
}
if (mb_strlen($nr_dokumentu) > $max_dl_nr_dokumentu) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Numer dokumentu może mieć maksymalnie $max_dl_nr_dokumentu znaków"]);
    exit;
}
if (mb_strlen($identyfikator) > $max_dl_ident) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Identyfikator może mieć maksymalnie $max_dl_ident znaków"]);
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
