<?php
require_once 'database.php';

$zapisano = "";
$blad = "";
$blad_nazwa = "";
$blad_placa_od = "";
$blad_placa_do = "";

$lista_etatow = array();

$stmt = $pdo->query('SELECT * FROM etaty');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $lista_etatow[] = $row;
}


if (isset($_POST['submit'])) {



    if (isset($_POST['nazwa']) && strlen($_POST['nazwa']) > 15)
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_nazwa = "Nazwa etatu nie może być dłuższa niż 15 znaków.";
    }
    else if (isset($_POST['nazwa']) && !empty($_POST['nazwa']) && preg_match('/[^a-zA-ZąĆćęłńóśżźĄĆĘŁŃÓŚŻŹ]/', $_POST['nazwa']))
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
    else if (isset($_POST['nazwa']) && !empty($_POST['nazwa'])) {

        $etat_istnieje = false;

        foreach ($lista_etatow as $row) {
            if ($row['NAZWA'] === $_POST['nazwa']) {
                $etat_istnieje = true;
                break;
            }
        }
        if ($etat_istnieje) {
            $blad = "Tak";
            $zapisano = "Nie";
            $blad_nazwa = "Taki etat już istnieje";
        } else {
            $nazwa = $_POST['nazwa'];
        }
    }





    // Sprawdzanie placy od
    if (isset($_POST['placa_od']) && empty($_POST['placa_od']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_od = "Nie podano początku zakresu płacy.";
    }
    else if (isset($_POST['placa_od']) && !is_numeric($_POST['placa_od']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_od = "Wprowadzono zły początkowy zakres płacy.";
    }
    else if (isset($_POST['placa_od']) && $_POST['placa_od'] <= 0)
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_od = "Płaca nie może być mniejsza od 0";
    }
    else if (isset($_POST['placa_od']))
    {
        $placa_od = $_POST['placa_od'];
    }





    // Sprawdzanie placy do
    if (!empty($_POST['placa_do']) && !is_numeric($_POST['placa_do']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_do = "Wprowadzono złą końcową płacę";
    }
    else if (isset($_POST['placa_do']) && !is_numeric($_POST['placa_do']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_do = "Wprowadzono zły końcowy zakres płacy.";
    }
    else if (isset($_POST['placa_do']) && $_POST['placa_do'] < $_POST['placa_od'])
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_do = "Końcowy zakres płac nie może być mniejszy niż początkowy.";
    }

    else if (isset($_POST['placa_do']) && $_POST['placa_do'] <= 0)
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_do = "Płaca nie może być mniejsza od lub równa 0";
    }
    else if (isset($_POST['placa_do']))
    {
        $placa_do = $_POST['placa_do'];
    }





    if ($zapisano == "")
    {
        try
        {
            $stmt = $pdo->prepare("INSERT INTO etaty (NAZWA, PLACA_OD, PLACA_DO) VALUES (:NAZWA, :placa_od, :placa_do)");
            $stmt->bindParam(':NAZWA', $nazwa);
            $stmt->bindParam(':placa_od', $placa_od);
            $stmt->bindParam(':placa_do', $placa_do);
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
    <title>Dodawanie etatu</title>
</head>
<body>

    <div class="container">
        <ul class="nav nav-tabs mt-2">
            <li class="nav-item">
                <a class="nav-link" href="../index.php">Pracownicy</a>
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
                <h3 class="mt-4">Dodawanie etatu</h3>
                <?php if ($zapisano == "Tak"): ?>
                    <div class="alert alert-success">Poprawnie dodano etat!</div>
                <?php endif; ?>

                <form class="mt-4" method="post" action="" novalidate>

                    <div class="form-floating mb-3">
                        <?php if ($blad_nazwa != ""): ?>
                            <input type="text" name="nazwa" class="form-control <?php if ($blad != ""): ?> is-invalid <?php endif; ?> " id="floatingInputnazwa" placeholder="Jan" value="<?php echo isset($_POST['nazwa']) ? htmlspecialchars($_POST['nazwa']) : ''; ?>">
                            <label for="floatingInputnazwa">Nazwa</label>
                            <div class="invalid-feedback">
                                <?php echo $blad_nazwa; ?>
                            </div>
                        <?php else: ?>
                            <input type="text" name="nazwa" class="form-control" id="floatingInputnazwa" placeholder="Jan" value="<?php echo isset($_POST['nazwa']) ? htmlspecialchars($_POST['nazwa']) : ''; ?>">
                            <label for="floatingInputnazwa">Nazwa</label>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating mb-3">
                        <?php if ($blad_placa_od != ""): ?>
                            <input type="number" class="form-control is-invalid" name="placa_od" id="floatingInputPłaca" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_od']) ? htmlspecialchars($_POST['placa_od']) : ''; ?>">
                            <label for="floatingInputPłaca">Płaca od</label>
                            <div class="invalid-feedback">
                                <?php echo $blad_placa_od; ?>
                            </div>
                        <?php else: ?>
                            <input type="number" class="form-control" name="placa_od" id="floatingInputPłaca" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_od']) ? htmlspecialchars($_POST['placa_od']) : ''; ?>">
                            <label for="floatingInputPłaca">Płaca od</label>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating mb-3">
                        <?php if ($blad_placa_do != ""): ?>
                            <input type="number" class="form-control is-invalid" name="placa_do" id="floatingInputPłacaDodatkowa" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_do']) ? htmlspecialchars($_POST['placa_do']) : ''; ?>">
                            <label for="floatingInputPłacaDodatkowa">Płaca do</label>
                            <div class="invalid-feedback">
                                <?php echo $blad_placa_do; ?>
                            </div>
                        <?php else: ?>
                            <input type="number" class="form-control" name="placa_do" id="floatingInputPłacaDodatkowa" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_do']) ? htmlspecialchars($_POST['placa_do']) : ''; ?>">
                            <label for="floatingInputPłacaDodatkowa">Płaca do</label>
                        <?php endif; ?>
                    </div>

                    <button type="submit" name="submit" class="btn btn-success"><i class="bi bi-plus-lg m-auto"></i>Zapisz dane</button>
                    <a href="../etaty.php"><button type="button" name="wroc" class="btn btn-secondary" onclick="wroc()"><i class="bi bi-arrow-left"></i>Wróć</button></a>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
