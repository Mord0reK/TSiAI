<?php
/**
 * POST /api/czytelnicy/zmien_haslo.php
 * Zmienia hasło czytelnika.
 * Czytelnik zmienia swoje hasło. Wymaga podania aktualnego hasła.
 *
 * Dane z formularza (POST):
 *   stare_haslo  — aktualne hasło
 *   nowe_haslo   — nowe hasło
 */

require_once __DIR__ . '/../../config/auth.php';

$uzytkownik = zalogowany_uzytkownik();
$id         = $uzytkownik['id'];

$stare_haslo = $_POST['stare_haslo'] ?? '';
$nowe_haslo  = $_POST['nowe_haslo'] ?? '';

if (empty($stare_haslo) || empty($nowe_haslo)) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Wypełnij oba pola"]);
    exit;
}

if (strlen($nowe_haslo) < 6) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nowe hasło musi mieć co najmniej 6 znaków"]);
    exit;
}

try {
    // Pobierz aktualne hasło
    $sprawdzenie = $pdo->prepare("SELECT haslo FROM czytelnicy WHERE id = :id");
    $sprawdzenie->bindValue(':id', $id, PDO::PARAM_INT);
    $sprawdzenie->execute();
    $hash = $sprawdzenie->fetchColumn();

    if (!$hash) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Czytelnik nie został znaleziony"]);
        exit;
    }

    // Weryfikuj aktualne hasło
    if (!password_verify($stare_haslo, $hash)) {
        http_response_code(403);
        echo json_encode(["status" => false, "komunikat" => "Aktualne hasło jest nieprawidłowe"]);
        exit;
    }

    // Hash nowego hasła
    $nowy_hash = password_hash($nowe_haslo, PASSWORD_BCRYPT);

    // Aktualizuj hasło + wyłącz flagę zmiany hasła
    $zapytanie = $pdo->prepare(
        "UPDATE czytelnicy SET haslo = :haslo, zmien_haslo = 0 WHERE id = :id"
    );
    $zapytanie->bindValue(':haslo', $nowy_hash, PDO::PARAM_STR);
    $zapytanie->bindValue(':id',    $id,        PDO::PARAM_INT);
    $zapytanie->execute();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Hasło zostało zmienione"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
