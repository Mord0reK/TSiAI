<?php
/**
 * GET /api/ksiazki/list.php
 * Zwraca listę wszystkich książek.
 * Wymaga zalogowania (admin lub czytelnik).
 */

require_once __DIR__ . '/../../config/auth.php';

// Akceptowane parametry filtrowania (opcjonalne)
$szukaj = $_GET['szukaj'] ?? '';

try {
    if (!empty($szukaj)) {
        $zapytanie = $pdo->prepare(
            "SELECT * FROM ksiazki
             WHERE autor LIKE :szukaj OR tytul LIKE :szukaj OR wydawnictwo LIKE :szukaj
             ORDER BY tytul ASC"
        );
        $zapytanie->bindValue(':szukaj', "%$szukaj");
    } else {
        $zapytanie = $pdo->query("SELECT * FROM ksiazki ORDER BY tytul ASC");
    }

    $zapytanie->execute();
    $ksiazki = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status"   => true,
        "ksiazki"  => $ksiazki
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
