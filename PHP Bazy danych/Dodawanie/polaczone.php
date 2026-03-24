<?php
require_once 'database.php';
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Bazy danych - Pracownicy</title>
</head>
<body>

    <?php
        if(isset($_POST['submit']) && $_POST['search']!=''){
            $stmt = $pdo->prepare("SELECT
                pracownicy.ID_PRAC,
                pracownicy.IMIE,
                pracownicy.NAZWISKO,
                pracownicy.ETAT,
                pracownicy.PLACA_POD,
                pracownicy.PLACA_DOD,
                pracownicy.ZATRUDNIONY,
                CONCAT(p.IMIE, ' ', p.NAZWISKO) AS SZEF,
                zespoly.NAZWA AS ZESPOL
            FROM pracownicy
            LEFT JOIN zespoly ON pracownicy.ID_ZESP = zespoly.ID_ZESP
            LEFT JOIN pracownicy AS p ON pracownicy.ID_SZEFA = p.ID_PRAC
            WHERE pracownicy.IMIE LIKE :imie OR pracownicy.NAZWISKO LIKE :nazwisko");
            $stmt -> bindValue(':imie', '%'.$_POST['search'].'%', PDO::PARAM_STR);
            $stmt -> bindValue(':nazwisko', '%'.$_POST['search'].'%', PDO::PARAM_STR);
            $stmt->execute();
        }
        else if (isset($_POST['reset'])){ {
            $stmt = $pdo->query("SELECT
                pracownicy.ID_PRAC,
                pracownicy.IMIE,
                pracownicy.NAZWISKO,
                pracownicy.ETAT,
                pracownicy.PLACA_POD,
                pracownicy.PLACA_DOD,
                pracownicy.ZATRUDNIONY,
                CONCAT(p.IMIE, ' ', p.NAZWISKO) AS SZEF,
                zespoly.NAZWA AS ZESPOL
            FROM pracownicy
            LEFT JOIN zespoly ON pracownicy.ID_ZESP = zespoly.ID_ZESP
            LEFT JOIN pracownicy AS p ON pracownicy.ID_SZEFA = p.ID_PRAC");
        }}
        else
        {
            $stmt = $pdo->query("SELECT
                pracownicy.ID_PRAC,
                pracownicy.IMIE,
                pracownicy.NAZWISKO,
                pracownicy.ETAT,
                pracownicy.PLACA_POD,
                pracownicy.PLACA_DOD,
                pracownicy.ZATRUDNIONY,
                CONCAT(p.IMIE, ' ', p.NAZWISKO) AS SZEF,
                zespoly.NAZWA AS ZESPOL
            FROM pracownicy
            LEFT JOIN zespoly ON pracownicy.ID_ZESP = zespoly.ID_ZESP
            LEFT JOIN pracownicy AS p ON pracownicy.ID_SZEFA = p.ID_PRAC");
        }
    ?>

    <div class="container">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" aria-current="page" href="index.php ">Pracownicy</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="etaty.php">Etaty</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="zespoly.php">Zespoły</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="polaczone.php">Połączone</a>
            </li>
        </ul>
        <form action="" method="post">
            <div class="row my-5">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="search" value="<?php echo isset($_POST['reset']) ? '' : (isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''); ?>" />
                </div>
                <div class="col-md-1 text-left">
                    <input type="submit" class="btn btn-primary" name="submit" value="Szukaj" />
                </div>
                <div class="col-md-1 text-left">
                    <input type="submit" class="btn btn-danger" name="reset" value="Resetuj" />
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
                        <th>Placa pod</th>
                        <th>Placa dod</th>
                        <th>Zatrudniony</th>
                        <th>Szef</th>
                        <th>Zespół</th>
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
                        echo '<td>'.$row['PLACA_POD'].'</td>';
                        echo '<td>'.$row['PLACA_DOD'].'</td>';
                        echo '<td>'.$row['ZATRUDNIONY'].'</td>';
                        echo '<td>'.$row['SZEF'].'</td>';
                        echo '<td>'.$row['ZESPOL'].'</td>';
                        echo '</tr>';
                    }
                    ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html> 