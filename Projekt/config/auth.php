<?php
/**
 * Helper autoryzacji — dołącz do każdego endpointu API.
 *
 * Po dołączeniu zmienna $uzytkownik zawiera dane zalogowanego usera:
 *   ['id', 'login', 'typ']  — dla admina
 *   ['id', 'login', 'imieNazwisko', 'typ']  — dla czytelnika
 *
 * Użycie:
 *   require_once __DIR__ . '/../../config/auth.php';          // wymaga zalogowania
 *   require_once __DIR__ . '/../../config/auth.php'; wymagaj_admin();  // wymaga admina
 */

require_once __DIR__ . '/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

/**
 * Sprawdza czy użytkownik jest zalogowany.
 * Jeśli nie — zwraca JSON z błędem 401 i kończy egzekucję.
 */
function wymagaj_zalogowania(): void {
    if (!isset($_SESSION['uzytkownik'])) {
        http_response_code(401);
        echo json_encode(["status" => false, "komunikat" => "Wymagane logowanie"]);
        exit;
    }
}

/**
 * Sprawdza czy zalogowany użytkownik jest adminem.
 * Jeśli nie — zwraca JSON z błędem 403 i kończy egzekucję.
 * Musi być wywołane PO wymagaj_zalogowania().
 */
function wymagaj_admin(): void {
    if ($_SESSION['uzytkownik']['typ'] !== 'admin') {
        http_response_code(403);
        echo json_encode(["status" => false, "komunikat" => "Brak uprawnień"]);
        exit;
    }
}

// Automatyczna weryfikacja sesji
// Każdy endpoint dołączający auth.php wymaga zalogowania.
wymagaj_zalogowania();

// Zwraca dane zalogowanego użytkownika
function zalogowany_uzytkownik(): array {
    return $_SESSION['uzytkownik'];
}
