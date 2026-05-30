<?php
/**
 * GET /api/czytelnicy/list.php
 * Zwraca listę wszystkich czytelników.
 * Wymaga zalogowania jako admin.
 *
 * Opcjonalne parametry GET:
 *   szukaj — filtruje po imieniu, nazwisku lub identyfikatorze
 */

require_once __DIR__ . '/../../config/auth.php';
wymagaj_admin();

$szukaj = $_GET['szukaj'] ?? '';

try {
    if (!empty($szukaj)) {
        $zapytanie = $pdo->prepare(
            "SELECT id, imie, nazwisko, adres, nr_dokumentu, identyfikator, zmien_haslo
             FROM czytelnicy
             WHERE imie LIKE :szukaj OR nazwisko LIKE :szukaj OR identyfikator LIKE :szukaj
             ORDER BY nazwisko ASC, imie ASC"
        );
        $zapytanie->bindValue(':szukaj', "%$szukaj");
    } else {
        $zapytanie = $pdo->query(
            "SELECT id, imie, nazwisko, adres, nr_dokumentu, identyfikator, zmien_haslo
             FROM czytelnicy
             ORDER BY nazwisko ASC, imie ASC"
        );
    }

    $zapytanie->execute();
    $czytelnicy = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status"     => true,
        "czytelnicy" => $czytelnicy
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
