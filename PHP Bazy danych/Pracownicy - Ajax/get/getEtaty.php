<?php

require_once 'database.php';

$stmt = $pdo->query('SELECT * FROM etaty');
$stmt->execute();

$html = '';

foreach ($stmt as $row) {
    $html .= '<tr>';
    $html .= '<td>' . $row['NAZWA'] . '</td>';
    $html .= '<td>' . $row['PLACA_OD'] . '</td>';
    $html .= '<td>' . $row['PLACA_DO'] . '</td>';
    $html .= '<td><a href="edytuj/etat.php?nazwa=' . $row['NAZWA'] . '"><button type="button" class="btn btn-outline-secondary me-2"><i class="bi bi-pencil-square"></i></button></a>';
    $html .= '<button type="button" class="btn btn-outline-danger" 
                                    tabindex="0"
                                    data-bs-toggle="popover"
                                    data-bs-placement="top"
                                    data-bs-trigger="focus"
                                    data-bs-title="Potwierdź usunięcie"
                                    data-bs-content="<p class=\'mb-2\'>Czy na pewno chcesz usunąć etat: ' . $row['NAZWA'] . '?</p>
                                    <form method=\'post\' class=\'d-inline\'>
                                        <input type=\'hidden\' name=\'delete_nazwa\' value=\'' . $row['NAZWA'] . '\'>
                                        <button type=\'submit\' name=\'delete\' class=\'btn btn-danger btn-sm\'>Usuń</button>
                                    </form> 
                                    <button type=\'button\' class=\'btn btn-secondary btn-sm\' data-bs-dismiss=\'popover\'>Anuluj</button>">
                                <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                            ';
    $html .= '</tr>';
}
