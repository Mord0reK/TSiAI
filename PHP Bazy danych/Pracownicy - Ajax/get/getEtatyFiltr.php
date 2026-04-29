<?php

require_once 'database.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM etaty WHERE NAZWA LIKE :nazwa");
    $stmt->bindValue(':nazwa', '%'.$_POST['search'].'%', PDO::PARAM_STR);
    $stmt->execute();
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $html = '';
    if (empty($results)) {
        $html = '<tr><td colspan="4" class="text-center text-muted">Brak wyników wyszukiwania</td></tr>';
    } else {
        foreach ($results as $row) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row['NAZWA']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['PLACA_OD']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['PLACA_DO']) . '</td>';
            $html .= '<td>
                        <button type="button" class="btn btn-outline-secondary me-2 btn-edit-etat" data-bs-toggle="modal" data-bs-target="#modalEditEtat" data-edit-nazwa="' . htmlspecialchars($row['NAZWA']) . '">
                            <i class="bi bi-pencil-square"></i>
                        </button>';
            $html .= '<button type="button" class="btn btn-outline-danger btn-confirm-delete-etat" 
                                            tabindex="0"
                                            data-bs-toggle="popover"
                                            data-bs-placement="top"
                                            data-bs-trigger="focus"
                                            data-bs-title="Potwierdź usunięcie"
                                            data-delete-nazwa="' . htmlspecialchars($row['NAZWA']) . '"
                                            data-bs-content="<p class=\'mb-2\'>Czy na pewno chcesz usunąć etat: ' . htmlspecialchars($row['NAZWA']) . '?</p>
                                            <button type=\'button\' class=\'btn btn-danger btn-sm btn-confirm-delete-etat\' data-delete-nazwa=\''. htmlspecialchars($row['NAZWA']) .'\'>Usuń</button> 
                                            <button type=\'button\' class=\'btn btn-secondary btn-sm\' data-bs-dismiss=\'popover\'>Anuluj</button>">
                                        <i class="bi bi-trash3"></i>
                                        </button>
                                    </td>';
            $html .= '</tr>';
        }
    }
    
    echo $html;
} catch (Exception $e) {
    echo '<tr><td colspan="4" class="text-center text-danger">Błąd: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}
