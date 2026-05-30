<?php
/**
 * GET /api/rezerwacje/list.php
 * Zwraca listę rezerwacji.
 * Admin widzi wszystkie, czytelnik tylko swoje.
 *
 * Opcjonalne parametry GET:
 *   status — filtruje po statusie (aktywna, zrealizowana, anulowana)
 *   czytelnik_id — filtruje po czytelniku (tylko admin)
 */

require_once __DIR__ . '/../../config/auth.php';

$uzytkownik = zalogowany_uzytkownik();
$status     = $_GET['status'] ?? '';
$czytelnik_id_filter = intval($_GET['czytelnik_id'] ?? 0);

try {
    $warunki  = [];
    $parametry = [];

    // Czytelnik widzi tylko swoje rezerwacje
    if ($uzytkownik['typ'] === 'czytelnik') {
        $warunki[] = 'r.czytelnik_id = :moj_id';
        $parametry[':moj_id'] = $uzytkownik['id'];
    } elseif ($czytelnik_id_filter > 0) {
        // Admin może filtrować po czytelniku
        $warunki[] = 'r.czytelnik_id = :czytelnik_id';
        $parametry[':czytelnik_id'] = $czytelnik_id_filter;
    }

    if (!empty($status)) {
        $warunki[] = 'r.status = :status';
        $parametry[':status'] = $status;
    }

    $sql = "SELECT r.id, r.czytelnik_id, r.ksiazka_id, r.data_rezerwacji, r.status,
                   CONCAT(c.imie, ' ', c.nazwisko) AS czytelnik_nazwa,
                   k.tytul AS ksiazka_tytul, k.autor AS ksiazka_autor
            FROM rezerwacje r
            JOIN czytelnicy c ON c.id = r.czytelnik_id
            JOIN ksiazki k ON k.id = r.ksiazka_id";

    if (!empty($warunki)) {
        $sql .= " WHERE " . implode(' AND ', $warunki);
    }

    $sql .= " ORDER BY r.data_rezerwacji DESC";

    $zapytanie = $pdo->prepare($sql);

    foreach ($parametry as $nazwa => $wartosc) {
        $typ = is_int($wartosc) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $zapytanie->bindValue($nazwa, $wartosc, $typ);
    }

    $zapytanie->execute();
    $rezerwacje = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status"      => true,
        "rezerwacje"  => $rezerwacje
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
