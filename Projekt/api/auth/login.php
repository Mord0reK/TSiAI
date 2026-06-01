<?php

require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

$login      = trim($_POST['login'] ?? '');
$haslo      = $_POST['haslo'] ?? '';
$nowe_haslo = $_POST['nowe_haslo'] ?? '';

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
    session_start();
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

if (!$czytelnik || !password_verify($haslo, $czytelnik['haslo'])) {
    http_response_code(401);
    echo json_encode(["status" => false, "komunikat" => "Nieprawidłowy login lub hasło"]);
    exit;
}

// === Czytelnik — hasło poprawne ===

// Jeśli przysłano nowe_haslo — najpierw zmień hasło
if (!empty($nowe_haslo)) {
    if (strlen($nowe_haslo) < 6) {
        http_response_code(400);
        echo json_encode(["status" => false, "komunikat" => "Hasło musi mieć co najmniej 6 znaków"]);
        exit;
    }

    $hash = password_hash($nowe_haslo, PASSWORD_BCRYPT);
    $update = $pdo->prepare("UPDATE czytelnicy SET haslo = :haslo, zmien_haslo = 0 WHERE id = :id");
    $update->bindValue(':haslo', $hash, PDO::PARAM_STR);
    $update->bindValue(':id', $czytelnik['id'], PDO::PARAM_INT);
    $update->execute();
}

// Jeśli wymagana zmiana hasła i nie wysłano nowego — zwróć flagę
if ($czytelnik['zmien_haslo'] && empty($nowe_haslo)) {
    echo json_encode([
        "status"       => true,
        "typ"          => "czytelnik",
        "zmien_haslo"  => true,
        "imieNazwisko" => $czytelnik['imie'] . ' ' . $czytelnik['nazwisko']
    ]);
    exit;
}

// Normalne logowanie (lub po zmianie hasła)
session_start();
$_SESSION['uzytkownik'] = [
    'id'           => $czytelnik['id'],
    'login'        => $czytelnik['identyfikator'],
    'imieNazwisko' => $czytelnik['imie'] . ' ' . $czytelnik['nazwisko'],
    'typ'          => 'czytelnik'
];

echo json_encode([
    "status"       => true,
    "typ"          => "czytelnik",
    "zmien_haslo"  => false,
    "imieNazwisko" => $czytelnik['imie'] . ' ' . $czytelnik['nazwisko']
]);
