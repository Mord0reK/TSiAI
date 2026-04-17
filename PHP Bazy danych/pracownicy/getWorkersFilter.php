<?php
require_once 'database.php';

$stmt = $pdo->prepare("SELECT * FROM pracownicy WHERE IMIE LIKE :imie OR NAZWISKO LIKE :nazwisko");
$stmt -> bindValue(':imie', '%'.$_POST['search'].'%', PDO::PARAM_STR);
$stmt -> bindValue(':nazwisko', '%'.$_POST['search'].'%', PDO::PARAM_STR);
$stmt->execute();

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
