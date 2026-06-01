<?php
/**
 * POST /api/ksiazki/edytuj.php
 * Edytuje dane książki.
 * Wymaga zalogowania jako admin.
 *
 * Dane z formularza (POST):
 *   id            — ID książki (wymagane)
 *   autor         — imię i nazwisko autora (max 100 znaków)
 *   tytul         — tytuł książki (max 100 znaków)
 *   wydawnictwo   — nazwa wydawnictwa (max 100 znaków)
 *   rok_wydania   — rok wydania (1901 - aktualny rok, ograniczenie typu YEAR w MySQL)
 *   ilosc         — ilość egzemplarzy (min. 1)
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

$id            = intval($_POST['id'] ?? 0);
$autor         = trim($_POST['autor'] ?? '');
$tytul         = trim($_POST['tytul'] ?? '');
$wydawnictwo   = trim($_POST['wydawnictwo'] ?? '');
$rok_wydania   = intval($_POST['rok_wydania'] ?? 0);
$ilosc         = max(1, intval($_POST['ilosc'] ?? 1));

// Granice walidacji
$aktualny_rok = (int)date('Y');
$min_rok      = 1901;
$max_dl_tekst = 100;

// Walidacja ID
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe ID książki"]);
    exit;
}

// Walidacja pól tekstowych
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

// Walidacja roku
if ($rok_wydania < $min_rok || $rok_wydania > $aktualny_rok) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Rok wydania musi być w zakresie $min_rok - $aktualny_rok"]);
    exit;
}

try {
    // Sprawdź czy książka istnieje
    $sprawdzenie = $pdo->prepare("SELECT id, ilosc_calkowita, ilosc_dostepnych FROM ksiazki WHERE id = :id");
    $sprawdzenie->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie->execute();
    $ksiazka = $sprawdzenie->fetch(PDO::FETCH_ASSOC);

    if (!$ksiazka) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Książka nie została znaleziona"]);
        exit;
    }

    // Oblicz różnicę w ilości by zaktualizować dostępność
    $roznica = $ilosc - $ksiazka['ilosc_calkowita'];
    $nowa_dostepnosc = $ksiazka['ilosc_dostepnych'] + $roznica;

    // Nie pozwól by dostępność była ujemna
    if ($nowa_dostepnosc < 0) {
        $nowa_dostepnosc = 0;
    }

    $zapytanie = $pdo->prepare(
        "UPDATE ksiazki
         SET autor = :autor, tytul = :tytul, wydawnictwo = :wydawnictwo,
             rok_wydania = :rok_wydania, ilosc_calkowita = :ilosc, ilosc_dostepnych = :dostepne
         WHERE id = :id"
    );

    $zapytanie->bindValue(':autor',         $autor,         PDO::PARAM_STR);
    $zapytanie->bindValue(':tytul',         $tytul,         PDO::PARAM_STR);
    $zapytanie->bindValue(':wydawnictwo',   $wydawnictwo,   PDO::PARAM_STR);
    $zapytanie->bindValue(':rok_wydania',   $rok_wydania,   PDO::PARAM_INT);
    $zapytanie->bindValue(':ilosc',         $ilosc,         PDO::PARAM_INT);
    $zapytanie->bindValue(':dostepne',      $nowa_dostepnosc, PDO::PARAM_INT);
    $zapytanie->bindValue(':id',            $id,            PDO::PARAM_INT);
    $zapytanie->execute();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Dane książki zostały zaktualizowane"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
