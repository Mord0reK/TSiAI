<?php

require_once __DIR__ . '/../../config/db.php';

session_start();

header('Content-Type: application/json');

// Jeśli już zalogowany, nie loguj ponownie
if (isset($_SESSION['uzytkownik'])) {
    echo json_encode([
        "status" => true,
        "komunikat" => "Jesteś już zalogowany",
        "uzytkownik" => $_SESSION['uzytkownik']
    ]);
    exit;
}

$login = trim($_POST['login'] ?? '');
$haslo = $_POST['haslo'] ?? '';

if (empty($login) || empty($haslo)) {
    http_response_code(400);
    echo json_encode(["status" => false, "komunikat" => "Login i hasło są wymagane"]);
    exit;
}

// Szukaj w adminach
$zapytanie = $pdo->prepare("SELECT * FROM adminy WHERE login = :login");
$zapytanie->bindValue(':login', $login);
$zapytanie->execute();
$admin = $zapytanie->fetch(PDO::FETCH_ASSOC);

if ($admin && password_verify($haslo, $admin['haslo'])) {
    $_SESSION['uzytkownik'] = [
        'id'    => $admin['id'],
        'login' => $admin['login'],
        'typ'   => 'admin'
    ];
    echo json_encode(["status" => true, "typ" => "admin"]);
    exit;
}

// Szukaj w czytelnikach
$zapytanie = $pdo->prepare("SELECT * FROM czytelnicy WHERE identyfikator = :login");
$zapytanie->bindValue(':login', $login);
$zapytanie->execute();
$czytelnik = $zapytanie->fetch(PDO::FETCH_ASSOC);

if ($czytelnik && password_verify($haslo, $czytelnik['haslo'])) {
    $_SESSION['uzytkownik'] = [
        'id'    => $czytelnik['id'],
        'login' => $czytelnik['identyfikator'],
        'imieNazwisko'  => $czytelnik['imie'] . ' ' . $czytelnik['nazwisko'],
        'typ'   => 'czytelnik'
    ];
    echo json_encode([
        "status"         => true,
        "typ"        => "czytelnik",
        "zmien_haslo" => (bool)$czytelnik['zmien_haslo']
    ]);
    exit;
}

// Nic nie pasuje
http_response_code(401);
echo json_encode(["status" => false, "komunikat" => "Nieprawidłowy login lub hasło"]);

