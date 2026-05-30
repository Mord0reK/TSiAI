<?php
/**
 * GET /api/wypozyczenia/list.php
 * Zwraca listę wypożyczeń.
 * Admin widzi wszystkie, czytelnik tylko swoje.
 *
 * Opcjonalne parametry GET:
 *   status — filtruje po statusie (aktywne, zwrocone)
 *   czytelnik_id — filtruje po czytelniku (tylko admin)
 */

require_once __DIR__ . '/../../config/auth.php';

$uzytkownik = zalogowany_uzytkownik();
$status     = $_GET['status'] ?? '';
$czytelnik_id_filter = intval($_GET['czytelnik_id'] ?? 0);

try {
    $warunki  = [];
    $parametry = [];

    if ($uzytkownik['typ'] === 'czytelnik') {
        $warunki[] = 'w.czytelnik_id = :moj_id';
        $parametry[':moj_id'] = $uzytkownik['id'];
    } elseif ($czytelnik_id_filter > 0) {
        $warunki[] = 'w.czytelnik_id = :czytelnik_id';
        $parametry[':czytelnik_id'] = $czytelnik_id_filter;
    }

    if (!empty($status)) {
        $warunki[] = 'w.status = :status';
        $parametry[':status'] = $status;
    }

    $sql = "SELECT w.id, w.czytelnik_id, w.ksiazka_id, w.data_wypozyczenia,
                   w.termin_zwrotu, w.data_zwrotu, w.status,
                   CONCAT(c.imie, ' ', c.nazwisko) AS czytelnik_nazwa,
                   k.tytul AS ksiazka_tytul, k.autor AS ksiazka_autor
            FROM wypozyczenia w
            JOIN czytelnicy c ON c.id = w.czytelnik_id
            JOIN ksiazki k ON k.id = w.ksiazka_id";

    if (!empty($warunki)) {
        $sql .= " WHERE " . implode(' AND ', $warunki);
    }

    $sql .= " ORDER BY w.data_wypozyczenia DESC";

    $zapytanie = $pdo->prepare($sql);

    foreach ($parametry as $nazwa => $wartosc) {
        $typ = is_int($wartosc) ? PDO::PARAM_INT : PDO::PARAM_STR;
        $zapytanie->bindValue($nazwa, $wartosc, $typ);
    }

    $zapytanie->execute();
    $wypozyczenia = $zapytanie->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status"       => true,
        "wypozyczenia" => $wypozyczenia
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => false, "komunikat" => "Błąd bazy danych"]);
}
