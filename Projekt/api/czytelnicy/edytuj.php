<?php
/**
 * POST /api/czytelnicy/edytuj.php
 * Edytuje dane czytelnika.
 *
 * Admin: edytuje dowolnego czytelnika (wymaga parametru id).
 * Czytelnik: edytuje tylko swoje dane (id pobierane z sesji).
 *
 * Admin może zmieniać: imie, nazwisko, adres, nr_dokumentu, identyfikator, haslo.
 * Czytelnik może zmieniać: imie, nazwisko, adres, nr_dokumentu.
 *
 * Dane z formularza (POST):
 *   id            — ID czytelnika (wymagane tylko dla admina)
 *   imie          — imię
 *   nazwisko      — nazwisko
 *   adres         — adres zamieszkania
 *   nr_dokumentu  — numer dokumentu tożsamości
 *   identyfikator — identyfikator (tylko admin)
 *   haslo         — nowe hasło (tylko admin)
 *   zmien_haslo   — 1 = wymuś zmianę hasła przy logowaniu (tylko admin)
 */

require_once __DIR__ . '/../../config/auth.php';

$uzytkownik = zalogowany_uzytkownik();

// Określ ID czytelnika do edycji
if ($uzytkownik['typ'] === 'admin') {
    $id = intval($_POST['id'] ?? 0);
    if ($id <= 0) {
        http_response_code(400);
        echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe dane"]);
        exit;
    }
} else {
    // Czytelnik edytuje siebie
    $id = $uzytkownik['id'];
}

$imie          = trim($_POST['imie'] ?? '');
$nazwisko      = trim($_POST['nazwisko'] ?? '');
$adres         = trim($_POST['adres'] ?? '');
$nr_dokumentu  = trim($_POST['nr_dokumentu'] ?? '');
$identyfikator = trim($_POST['identyfikator'] ?? '');
$haslo         = $_POST['haslo'] ?? '';
$zmien_haslo   = intval($_POST['zmien_haslo'] ?? 0);

// Granice walidacji
$max_dl_imie         = 100;
$max_dl_nazwisko     = 100;
$max_dl_adres        = 255;
$max_dl_nr_dokumentu = 50;

// Walidacja wymaganych pól
if (empty($imie) || empty($nazwisko) || empty($adres) || empty($nr_dokumentu)) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe dane"]);
    exit;
}

// Admin musi podać identyfikator
if ($uzytkownik['typ'] === 'admin' && empty($identyfikator)) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe dane"]);
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
// Identyfikator — tylko admin może go zmieniać, max 50 znaków
if ($uzytkownik['typ'] === 'admin' && mb_strlen($identyfikator) > 50) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Identyfikator może mieć maksymalnie 50 znaków"]);
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

    // Admin: sprawdź unikalność identyfikatora (jeśli się zmienia)
    if ($uzytkownik['typ'] === 'admin') {
        $sprawdzenie2 = $pdo->prepare(
            "SELECT id FROM czytelnicy WHERE identyfikator = :identyfikator AND id != :id"
        );
        $sprawdzenie2->bindValue(':identyfikator', $identyfikator, PDO::PARAM_STR);
        $sprawdzenie2->bindValue(':id', $id, PDO::PARAM_INT);
        $sprawdzenie2->execute();

        if ($sprawdzenie2->fetch()) {
            http_response_code(409);
            echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe dane"]);
            exit;
        }
    }

    if ($uzytkownik['typ'] === 'admin') {
        // Admin aktualizuje wszystkie pola
        $zapytanie = $pdo->prepare(
            "UPDATE czytelnicy
             SET imie = :imie, nazwisko = :nazwisko, adres = :adres,
                 nr_dokumentu = :nr_dokumentu, identyfikator = :identyfikator,
                 zmien_haslo = :zmien_haslo
             WHERE id = :id"
        );
        $zapytanie->bindValue(':imie',          $imie,          PDO::PARAM_STR);
        $zapytanie->bindValue(':nazwisko',      $nazwisko,      PDO::PARAM_STR);
        $zapytanie->bindValue(':adres',         $adres,         PDO::PARAM_STR);
        $zapytanie->bindValue(':nr_dokumentu',  $nr_dokumentu,  PDO::PARAM_STR);
        $zapytanie->bindValue(':identyfikator', $identyfikator, PDO::PARAM_STR);
        $zapytanie->bindValue(':zmien_haslo',   $zmien_haslo,   PDO::PARAM_INT);
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
    } else {
        // Czytelnik aktualizuje tylko swoje dane osobowe
        $zapytanie = $pdo->prepare(
            "UPDATE czytelnicy
             SET imie = :imie, nazwisko = :nazwisko, adres = :adres, nr_dokumentu = :nr_dokumentu
             WHERE id = :id"
        );
        $zapytanie->bindValue(':imie',         $imie,         PDO::PARAM_STR);
        $zapytanie->bindValue(':nazwisko',     $nazwisko,     PDO::PARAM_STR);
        $zapytanie->bindValue(':adres',        $adres,        PDO::PARAM_STR);
        $zapytanie->bindValue(':nr_dokumentu', $nr_dokumentu, PDO::PARAM_STR);
        $zapytanie->bindValue(':id',           $id,           PDO::PARAM_INT);
        $zapytanie->execute();
    }

    echo json_encode([
        "status"    => true,
        "komunikat" => "Dane czytelnika zostały zaktualizowane"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
