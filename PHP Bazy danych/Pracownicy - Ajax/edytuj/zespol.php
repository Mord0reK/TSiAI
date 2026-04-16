<?php
require_once 'database.php';

$zapisano = "";
$blad = "";
$blad_nazwa = "";
$blad_adres = "";


// Pobranie odpowiednich danych do formularza

$stmt = $pdo->prepare("SELECT * FROM zespoly WHERE ID_ZESP = :id_zesp");
$stmt->bindParam(':id_zesp', $_GET['id']);
$stmt->execute();
$zespol = $stmt->fetch(PDO::FETCH_ASSOC);


if (isset($_POST['submit'])) {




    // Sprawdzanie nazwy
    if (isset($_POST['nazwa']) && !empty($_POST['nazwa']) && strlen($_POST['nazwa']) > 20)
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_nazwa = "Nazwa zespołu nie może być dłuższa niż 20 znaków";
    }
    else if (isset($_POST['nazwa']) && !empty($_POST['nazwa']) && preg_match('/[^a-zA-ZąĆćęłńóśżźĄĆĘŁŃÓŚŻŹ\s]/', $_POST['nazwa']))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_nazwa = "W polu znalazły się inne znaki niż litery";
    }
    else if (isset($_POST['nazwa']) && empty($_POST['nazwa']))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_nazwa = "Nie podano nazwy";
    }
    else if (isset($_POST['nazwa']) && !empty($_POST['nazwa']))
    {
        $nazwa = $_POST['nazwa'];
    }





    if (isset($_POST['adres']) && !empty($_POST['adres']) && strlen($_POST['adres']) > 20)
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_adres = "Adres zespołu nie może być dłuższy niż 20 znaków";
    }
    else if (isset($_POST['adres']) && empty($_POST['adres']))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_adres = "Nie podano adresu";
    }
    else if (isset($_POST['adres']) && !empty($_POST['adres']))
    {
        $adres = $_POST['adres'];
    }





    if ($zapisano == "")
    {
        try
        {

            $id_zespolu = $_GET['id'];

            $stmt = $pdo->prepare("UPDATE zespoly SET NAZWA = :nazwa, ADRES = :adres WHERE ID_ZESP = :ID_ZESP");
            $stmt->bindParam(':ID_ZESP', $id_zespolu);
            $stmt->bindParam(':nazwa', $nazwa);
            $stmt->bindParam(':adres', $adres);
            $stmt->execute();
            $zapisano = "Tak";
        } catch (PDOException $e) {
            $zapisano = "Nie";
            $blad = $e->getMessage();
        }
    }
}

?>

<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>Edytowanie zespołu</title>
</head>
<body>

    <div class="container">
        <ul class="nav nav-tabs mt-2">
            <li class="nav-item">
                <a class="nav-link" href="../index.php ">Pracownicy</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../etaty.php">Etaty</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../zespoly.php">Zespoły</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../polaczone.php">Połączone</a>
            </li>
        </ul>
        <div class="row">
            <div class="col-12">
                <h3 class="mt-4">Edytowanie zespołu</h3>
                <?php if ($zapisano == "Tak"): ?>
                    <div class="alert alert-success">Poprawnie edytowano zespół!</div>
                <?php endif; ?>

                <!--Query do błędu-->

                <?php
                    $stmt = $pdo->prepare('SELECT ID_ZESP FROM zespoly');
                    $stmt->execute();
                    $zespoly_id = $stmt->fetchAll();
                ?>

                <?php if (in_array($_GET['id'], array_column($zespoly_id, 'ID_ZESP'))): ?>
                    <form class="mt-4" method="post" action="" novalidate>

                        <div class="form-floating mb-3">
                            <?php if ($blad_nazwa != ""): ?>
                                <input type="text" name="nazwa" class="form-control <?php if ($blad != ""): ?> is-invalid <?php endif; ?> " id="floatingInputnazwa" placeholder="Jan" value="<?php echo isset($_POST['nazwa']) ? htmlspecialchars($_POST['nazwa']) : $zespol['NAZWA']; ?>">
                                <label for="floatingInputnazwa">Nazwa</label>
                                <div class="invalid-feedback">
                                    <?php echo $blad_nazwa; ?>
                                </div>
                            <?php else: ?>
                                <input type="text" name="nazwa" class="form-control" id="floatingInputnazwa" placeholder="Jan" value="<?php echo isset($_POST['nazwa']) ? htmlspecialchars($_POST['nazwa']) : $zespol['NAZWA']; ?>">
                                <label for="floatingInputnazwa">Nazwa</label>
                            <?php endif; ?>
                        </div>


                        <div class="form-floating mb-3">
                            <?php if ($blad_adres != ""): ?>
                                <input type="text" name="adres" class="form-control <?php if ($blad != ""): ?> is-invalid <?php endif; ?> " id="floatingInputadres" value="<?php echo isset($_POST['adres']) ? htmlspecialchars($_POST['adres']) : $zespol['ADRES']; ?>">
                                <label for="floatingInputadres">Adres</label>
                                <div class="invalid-feedback">
                                    <?php echo $blad_adres; ?>
                                </div>
                            <?php else: ?>
                                <input type="text" name="adres" class="form-control" id="floatingInputadres"  value="<?php echo isset($_POST['adres']) ? htmlspecialchars($_POST['adres']) : $zespol['ADRES']; ?>">
                                <label for="floatingInputadres">Adres</label>
                            <?php endif; ?>
                        </div>

                        <button type="submit" name="submit" class="btn btn-success"><i class="bi bi-plus-lg m-auto"></i>Zapisz dane</button>
                        <a href="../zespoly.php"><button type="button" name="wroc" class="btn btn-secondary" onclick="wroc()"><i class="bi bi-arrow-left"></i>Wróć</button></a>
                    </form>
                <?php else: ?>
                    <div class="alert alert-danger">Nie znaleziono zespołu o podanym ID! <br> Nie baw się w hackerman'a</div>
                    <a href="../zespoly.php"><button type="button" name="wroc" class="btn btn-secondary" onclick="wroc()"><i class="bi bi-arrow-left"></i>Wróć</button></a>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
