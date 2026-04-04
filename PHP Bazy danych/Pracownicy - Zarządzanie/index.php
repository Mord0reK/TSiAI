<?php
require_once 'database.php';

if (isset($_POST['delete']) && isset($_POST['delete_id'])){

    $stmt = $pdo->prepare("UPDATE pracownicy SET ID_SZEFA = NULL WHERE ID_SZEFA = :ID_SZEFA");
    $stmt->bindParam(':ID_SZEFA', $_POST['delete_id'], PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $pdo->prepare("DELETE FROM pracownicy WHERE ID_PRAC = :ID_PRAC");
    $stmt->bindParam(':ID_PRAC', $_POST['delete_id'], PDO::PARAM_INT);
    $stmt->execute();

    # tu jest szpont zeby nie wywalalo komunikatu fikusnego. Mozna bylo to zostawic tak jak jest ale przy testowaniu irytuje jak cholera

    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>Bazy danych - Pracownicy</title>
</head>
<body>

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

    ?>

    <div class="container">
        <ul class="nav nav-tabs mt-2">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="#">Pracownicy</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="etaty.php">Etaty</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="zespoly.php">Zespoły</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="polaczone.php">Połączone</a>
            </li>
        </ul>
        <form action="" method="post">
            <div class="row my-5">
                <div class="col-md-4 input-group" style="width: 40%;" >
                    <input type="text" class="form-control" name="search" value="<?php echo isset($_POST['reset']) ? '' : (isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''); ?>" />
                    <button class="btn btn-primary" type="submit" name="submit">Szukaj</button>
                </div>
                <div class="col-md-1 text-left">
                    <input type="submit" class="btn btn-danger" name="reset" value="Resetuj" />
                </div>
                <div class="col-md-6 text-left d-flex justify-content-end">
                    <a href="dodaj/pracownik.php" class="btn btn-success">Dodaj nowego pracownika</a>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Id prac</th>
                        <th>Imię</th>
                        <th>Nazwisko</th>
                        <th>Etat</th>
                        <th>Id szefa</th>
                        <th>Zatrudniony</th>
                        <th>Placa pod</th>
                        <th>Placa dod</th>
                        <th>id zesp</th>
                        <th colspan="2">Akcje</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($stmt as $row){
                        echo '<tr>';
                        echo '<td>'.$row['ID_PRAC'].'</td>';
                        echo '<td>'.$row['IMIE'].'</td>';
                        echo '<td>'.$row['NAZWISKO'].'</td>';
                        echo '<td>'.$row['ETAT'].'</td>';
                        echo '<td>'.$row['ID_SZEFA'].'</td>';
                        echo '<td>'.$row['ZATRUDNIONY'].'</td>';
                        echo '<td>'.$row['PLACA_POD'].'</td>';
                        echo '<td>'.$row['PLACA_DOD'].'</td>';
                        echo '<td>'.$row['ID_ZESP'].'</td>';
                        echo '<td><a href="edytuj/pracownik.php?id='.$row['ID_PRAC'].'"><button type="button" class="btn btn-outline-secondary me-2"><i class="bi bi-pencil-square"></i></button></a>';
                        echo '<button type="button" class="btn btn-outline-danger" 
                                    tabindex="0"
                                    data-bs-toggle="popover"
                                    data-bs-placement="top"
                                    data-bs-trigger="focus"
                                    data-bs-title="Potwierdź usunięcie"
                                    data-bs-content="<p class=\'mb-2\'>Czy na pewno chcesz usunąć pracownika o ID: '. $row['ID_PRAC'] .'?</p>
                                    <form method=\'post\' class=\'d-inline\'>
                                        <input type=\'hidden\' name=\'delete_id\' value=\''. $row['ID_PRAC'] .'\'>
                                        <button type=\'submit\' name=\'delete\' class=\'btn btn-danger btn-sm\'>Usuń</button>
                                    </form> 
                                    <button type=\'button\' class=\'btn btn-secondary btn-sm\' data-bs-dismiss=\'popover\'>Anuluj</button>">
                                <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                            ';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                return new bootstrap.Popover(popoverTriggerEl, {
                    trigger: 'focus',
                    html: true,
                    sanitize: false
                });
            });
        });
    </script>
</body>
</html>