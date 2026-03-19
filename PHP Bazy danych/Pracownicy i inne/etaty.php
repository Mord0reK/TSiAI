<?php
require_once 'database.php';
?>
<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Bazy danych - Etaty</title>
</head>
<body>

<?php

if(isset($_POST['submit']) && $_POST['search']!=''){
    $stmt = $pdo->prepare("SELECT * FROM etaty WHERE NAZWA LIKE :nazwa");
    $stmt -> bindValue(':nazwa', '%'.$_POST['search'].'%', PDO::PARAM_STR);
    $stmt->execute();
}
else if (isset($_POST['reset'])){
    $stmt = $pdo->query('SELECT * FROM etaty');
}
else
{
    $stmt = $pdo->query('SELECT * FROM etaty');
}
?>

<div class="container">
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="index.php">Pracownicy</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="#">Etaty</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="zespoly.php">Zespoły</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="zespoly.php">Połączone</a>
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
                    <th scope="col">Nazwa</th>
                    <th scope="col">Placa od</th>
                    <th scope="col">Placa do</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($stmt as $row){
                    echo '<tr>';
                    echo '<td>'.$row['NAZWA'].'</td>';
                    echo '<td>'.$row['PLACA_OD'].'</td>';
                    echo '<td>'.$row['PLACA_DO'].'</td>';
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