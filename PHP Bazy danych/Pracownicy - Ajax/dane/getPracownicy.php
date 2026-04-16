<?php
if(isset($_POST['submit']) && $_POST['search']!=''){
    $stmt = $pdo->prepare("SELECT * FROM pracownicy WHERE IMIE LIKE :imie OR NAZWISKO LIKE :nazwisko");
    $stmt -> bindValue(':imie', '%'.$_POST['search'].'%', PDO::PARAM_STR);
    $stmt -> bindValue(':nazwisko', '%'.$_POST['search'].'%', PDO::PARAM_STR);
    $stmt->execute();
}
else if (isset($_POST['reset'])){
    $stmt = $pdo->query('SELECT * FROM pracownicy');
}
else
{
    $stmt = $pdo->query('SELECT * FROM pracownicy');
}

foreach ($stmt as $row) {

    $html = "";
    $html .= '<tr>';
    $html .= '<td>' . $row['ID_PRAC'] . '</td>';
    $html .= '<td>' . $row['IMIE'] . '</td>';
    $html .=  '<td>' . $row['NAZWISKO'] . '</td>';
    $html .=  '<td>' . $row['ETAT'] . '</td>';
    $html .=  '<td>' . $row['ID_SZEFA'] . '</td>';
    $html .=  '<td>' . $row['ZATRUDNIONY'] . '</td>';
    $html .=  '<td>' . $row['PLACA_POD'] . '</td>';
    $html .=  '<td>' . $row['PLACA_DOD'] . '</td>';
    $html .=  '<td>' . $row['ID_ZESP'] . '</td>';
    $html .=  '<td><a href="edytuj/pracownik.php?id=' . $row['ID_PRAC'] . '"><button type="button" class="btn btn-outline-secondary me-2"><i class="bi bi-pencil-square"></i></button></a>';
    $html .=  '<button type="button" class="btn btn-outline-danger" 
                                    tabindex="0"
                                    data-bs-toggle="popover"
                                    data-bs-placement="top"
                                    data-bs-trigger="focus"
                                    data-bs-title="Potwierdź usunięcie"
                                    data-bs-content="<p class=\'mb-2\'>Czy na pewno chcesz usunąć pracownika o ID: ' . $row['ID_PRAC'] . '?</p>
                                    <form method=\'post\' class=\'d-inline\'>
                                        <input type=\'hidden\' name=\'delete_id\' value=\'' . $row['ID_PRAC'] . '\'>
                                        <button type=\'submit\' name=\'delete\' class=\'btn btn-danger btn-sm\'>Usuń</button>
                                    </form> 
                                    <button type=\'button\' class=\'btn btn-secondary btn-sm\' data-bs-dismiss=\'popover\'>Anuluj</button>">
                                <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                            ';
    $html .=  '</tr>';
}

echo $html;

?>