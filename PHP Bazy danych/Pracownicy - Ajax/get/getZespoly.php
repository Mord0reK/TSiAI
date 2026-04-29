<?php

require_once 'database.php';

try {
    $stmt = $pdo->query('SELECT * FROM zespoly');
    $zespoly = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $html = '';
    
    if (empty($zespoly)) {
        $html = '<tr><td colspan="4" class="text-center text-muted">Brak zespołów</td></tr>';
    } else {
        foreach ($zespoly as $row) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($row['ID_ZESP']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['NAZWA']) . '</td>';
            $html .= '<td>' . htmlspecialchars($row['ADRES']) . '</td>';
            $html .= '<td>
                        <button type="button" class="btn btn-outline-secondary me-2 btn-edit-zespol" data-bs-toggle="modal" data-bs-target="#modalEditZespol" data-edit-id="'.htmlspecialchars($row['ID_ZESP']).'">
                            <i class="bi bi-pencil-square"></i>
                        </button>';
            $html .= '<button type="button" class="btn btn-outline-danger btn-delete-zespol" 
                                            tabindex="0"
                                            data-bs-toggle="popover"
                                            data-bs-placement="top"
                                            data-bs-trigger="focus"
                                            data-bs-title="Potwierdź usunięcie"
                                            data-delete-id="' . htmlspecialchars($row['ID_ZESP']) . '"
                                            data-bs-content="<p class=\'mb-2\'>Czy na pewno chcesz usunąć zespół o ID: ' . htmlspecialchars($row['ID_ZESP']) . '?</p>
                                            <button type=\'button\' class=\'btn btn-danger btn-sm btn-confirm-delete-zespol\' data-delete-id=\''. htmlspecialchars($row['ID_ZESP']) .'\'>Usuń</button> 
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
