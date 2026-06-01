<?php
/**
 * POST /api/ksiazki/edytuj.php
 * Edytuje dane książki.
 * Wymaga zalogowania jako admin.
 *
 * Dane z formularza (POST):
 *   id            — ID książki (wymagane)
 *   autor         — imię i nazwisko autora
 *   tytul         — tytuł książki
 *   wydawnictwo   — nazwa wydawnictwa
 *   rok_wydania   — rok wydania (4-cyfrowy)
 *   ilosc         — ilość egzemplarzy
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

$id            = intval($_POST['id'] ?? 0);
$autor         = trim($_POST['autor'] ?? '');
$tytul         = trim($_POST['tytul'] ?? '');
$wydawnictwo   = trim($_POST['wydawnictwo'] ?? '');
$rok_wydania   = intval($_POST['rok_wydania'] ?? 0);
$ilosc         = max(1, intval($_POST['ilosc'] ?? 1));

if ($id <= 0 || empty($autor) || empty($tytul) || empty($wydawnictwo) || $rok_wydania < 1000 || $rok_wydania > 9999) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe dane"]);
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
