<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['uzytkownik'])) {
    http_response_code(401);
    echo json_encode(["status" => false, "komunikat" => "Nie jesteś zalogowany"]);
    exit;
}

session_destroy();
echo json_encode(["status" => true, "komunikat" => "Wylogowano"]);
