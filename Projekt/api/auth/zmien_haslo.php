<?php
/**
 * POST /api/auth/zmien_haslo.php
 * Zmiana hasła.
 *
 * Czytelnik: wymaga nowe_haslo (sesja wystarczy jako autoryzacja).
 * Admin: wymaga czytelnik_id + nowe_haslo (resetuje hasło czytelnika).
 *
 * Dane z formularza (POST):
 *   nowe_haslo     — nowe hasło (wymagane)
 *   czytelnik_id   — ID czytelnika (wymagane dla admina)
 */

require_once __DIR__ . '/../../config/auth.php';

$uzytkownik   = zalogowany_uzytkownik();
$nowe_haslo   = $_POST['nowe_haslo'] ?? '';
$czytelnik_id = intval($_POST['czytelnik_id'] ?? 0);

if (empty($nowe_haslo)) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe dane"]);
    exit;
}

// Określ ID użytkownika którego hasło zmieniamy
if ($uzytkownik['typ'] === 'admin') {
    // Admin resetuje hasło czytelnika
    if ($czytelnik_id <= 0) {
        http_response_code(400);
        echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe dane"]);
        exit;
    }
    $id = $czytelnik_id;
} else {
    // Czytelnik zmienia swoje hasło
    // Sesja wystarczy jako dowód autoryzacji (właśnie się zalogował)
    $id = $uzytkownik['id'];
}

try {
    // Pobierz hasło
    $zapytanie = $pdo->prepare("SELECT haslo FROM czytelnicy WHERE id = :id");
    $zapytanie->bindValue(':id', $id, PDO::PARAM_INT);
    $zapytanie->execute();
    $czytelnik = $zapytanie->fetch(PDO::FETCH_ASSOC);

    if (!$czytelnik) {
        http_response_code(404);
        echo json_encode(["status" => false, "komunikat" => "Nieprawidłowe dane"]);
        exit;
    }

    // Hashuj nowe hasło
    $nowe_hash = password_hash($nowe_haslo, PASSWORD_BCRYPT);

    // Aktualizuj hasło i wyłącz flagę zmiany
    $zapytanie2 = $pdo->prepare(
        "UPDATE czytelnicy SET haslo = :haslo, zmien_haslo = 0 WHERE id = :id"
    );
    $zapytanie2->bindValue(':haslo', $nowe_hash, PDO::PARAM_STR);
    $zapytanie2->bindValue(':id',    $id,        PDO::PARAM_INT);
    $zapytanie2->execute();

    echo json_encode([
        "status"    => true,
        "komunikat" => "Hasło zostało zmienione"
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
