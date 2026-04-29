<?php

require_once 'database.php';

// Helper function do bezpiecznego wyświetlania wartości nullable
function safe_display($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

try {
    $stmt = $pdo->prepare("SELECT * FROM pracownicy WHERE IMIE LIKE :imie OR NAZWISKO LIKE :nazwisko");
    $stmt->bindValue(':imie', '%'.$_POST['search'].'%', PDO::PARAM_STR);
    $stmt->bindValue(':nazwisko', '%'.$_POST['search'].'%', PDO::PARAM_STR);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $html = '';
    if (empty($results)) {
        $html = '<tr><td colspan="10" class="text-center text-muted">Brak wyników wyszukiwania</td></tr>';
    } else {
        foreach ($results as $row) {
            $html .=  '<tr>';
            $html .=  '<td>'.safe_display($row['ID_PRAC']).'</td>';
            $html .=  '<td>'.safe_display($row['IMIE']).'</td>';
            $html .=  '<td>'.safe_display($row['NAZWISKO']).'</td>';
            $html .=  '<td>'.safe_display($row['ETAT']).'</td>';
            $html .=  '<td>'.safe_display($row['ID_SZEFA']).'</td>';
            $html .=  '<td>'.safe_display($row['ZATRUDNIONY']).'</td>';
            $html .=  '<td>'.safe_display($row['PLACA_POD']).'</td>';
            $html .=  '<td>'.safe_display($row['PLACA_DOD']).'</td>';
            $html .=  '<td>'.safe_display($row['ID_ZESP']).'</td>';
            $html .=  '<td>
                        <button type="button" class="btn btn-outline-secondary me-2 btn-edit-pracownik" data-bs-toggle="modal" data-bs-target="#modalEditPracownik" data-edit-id="'.safe_display($row['ID_PRAC']).'">
                            <i class="bi bi-pencil-square"></i>
                        </button>';
            $html .=  '<button type="button" class="btn btn-outline-danger btn-delete-pracownik"
                    tabindex="0"
                    data-bs-toggle="popover"
                    data-bs-placement="top"
                    data-bs-trigger="focus"
                    data-bs-title="Potwierdź usunięcie"
                    data-delete-id="'.safe_display($row['ID_PRAC']).'"
                    data-bs-content="<p class=\'mb-2\'>Czy na pewno chcesz usunąć pracownika o ID: '. safe_display($row['ID_PRAC']) .'?</p>
                    <button type=\'button\' class=\'btn btn-danger btn-sm btn-confirm-delete\' data-delete-id=\''. safe_display($row['ID_PRAC']) .'\'>Usuń</button>
                    <button type=\'button\' class=\'btn btn-secondary btn-sm\' data-bs-dismiss=\'popover\'>Anuluj</button>">
                <i class="bi bi-trash3"></i>
                </button>
            </td>';
            $html .=  '</tr>';
        }
    }
    
    echo $html;
} catch (Exception $e) {
    echo '<tr><td colspan="10" class="text-center text-danger">Błąd: ' . safe_display($e->getMessage()) . '</td></tr>';
}