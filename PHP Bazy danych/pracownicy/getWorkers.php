<?php
require_once 'database.php';

$stmt = $pdo->query('SELECT * FROM pracownicy');

$html = '';
foreach ($stmt as $row){
    $html .=  '<tr>';
    $html .=  '<td>' . $row['ID_PRAC'] . '</td>';
    $html .=  '<td>' . $row['IMIE'] . '</td>';
    $html .=  '<td>' . $row['NAZWISKO'] . '</td>';
    $html .=  '<td>' . $row['ETAT'] . '</td>';
    $html .=  '<td>' . $row['ID_SZEFA'] . '</td>';
    $html .=  '<td>' . $row['ZATRUDNIONY'] . '</td>';
    $html .=  '<td>' . $row['PLACA_POD'] . '</td>';
    $html .=  '<td>' . $row['PLACA_DOD'] . '</td>';
    $html .=  '<td>' . $row['ID_ZESP'] . '</td>';
    $html .=  '</tr>';
}

sleep(1);

echo $html;
