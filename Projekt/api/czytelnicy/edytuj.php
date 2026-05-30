<?php
/**
 * POST /api/czytelnicy/edytuj.php
 * Edytuje dane czytelnika.
 * Wymaga zalogowania jako admin.
 *
 * Dane z formularza (POST):
 *   id            — identyfikator czytelnika (wymagane)
 *   imie          — imię
 *   nazwisko      — nazwisko
 *   adres         — adres zamieszkania
 *   nr_dokumentu  — numer dokumentu tożsamości
 *   identyfikator — identyfikator (login)
 *   haslo         — nowe hasło (opcjonalne, jeśli puste — bez zmiany)
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

$id            = intval($_POST['id'] ?? 0);
$imie          = trim($_POST['imie'] ?? '');
$nazwisko      = trim($_POST['nazwisko'] ?? '');
$adres         = trim($_POST['adres'] ?? '');
$nr_dokumentu  = trim($_POST['nr_dokumentu'] ?? '');
$identyfikator = trim($_POST['identyfikator'] ?? '');
$haslo         = $_POST['haslo'] ?? '';

if ($id <= 0 || empty($imie) || empty($nazwisko) || empty($adres) || empty($nr_dokumentu) || empty($identyfikator)) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Wypełnij wszystkie wymagane pola"]);
    exit;
}

try {
    // Sprawdź czy czytelnik istnieje
    $sprawdzenie = $pdo->prepare("SELECT id FROM czytelnicy WHERE id = :id");
    $sprawdzenie->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie->execute();

    if (!$sprawdzenie->fetch()) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Czytelnik nie został znaleziony"]);
        exit;
    }

    // Sprawdź unikalność identyfikatora (jeśli się zmienia)
    $sprawdzenie2 = $pdo->prepare(
        "SELECT id FROM czytelnicy WHERE identyfikator = :identyfikator AND id != :id"
    );
    $sprawdzenie2->bindValue(':identyfikator', $identyfikator, PDO::PARAM_STR);
    $sprawdzenie2->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie2->execute();

    if ($sprawdzenie2->fetch()) {
        http_response_code(409);
        echo json_encode(["status" => false, "komunikat" => "Identifikator jest już zajęty"]);
        exit;
    }

    // Aktualizuj dane (bez hasła)
    $zapytanie = $pdo->prepare(
        "UPDATE czytelnicy
         SET imie = :imie, nazwisko = :nazwisko, adres = :adres,
             nr_dokumentu = :nr_dokumentu, identyfikator = :identyfikator
         WHERE id = :id"
    );

    $zapytanie->bindValue(':imie',          $imie,          PDO::PARAM_STR);
    $zapytanie->bindValue(':nazwisko',      $nazwisko,      PDO::PARAM_STR);
    $zapytanie->bindValue(':adres',         $adres,         PDO::PARAM_STR);
    $zapytanie->bindValue(':nr_dokumentu',  $nr_dokumentu,  PDO::PARAM_STR);
    $zapytanie->bindValue(':identyfikator', $identyfikator, PDO::PARAM_STR);
    $zapytanie->bindValue(':id',            $id,            PDO::PARAM_INT);
    $zapytanie->execute();

    // Opcjonalna zmiana hasła
    if (!empty($haslo)) {
        $haslo_hash = password_hash($haslo, PASSWORD_BCRYPT);

        $zapytanie2 = $pdo->prepare("UPDATE czytelnicy SET haslo = :haslo WHERE id = :id");
        $zapytanie2->bindValue(':haslo', $haslo_hash, PDO::PARAM_STR);
        $zapytanie2->bindValue(':id',    $id,         PDO::PARAM_INT);
        $zapytanie2->execute();
    }

    echo json_encode([
        "status"    => true,
        "komunikat" => "Dane czytelnika zostały zaktualizowane"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
