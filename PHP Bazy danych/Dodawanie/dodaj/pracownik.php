<?php
require_once 'database.php';

$zapisano = "";
$blad = "";

if (isset($_POST['submit'])) {

    // Sprawdzanie imienia
    if (isset($_POST['imie']) && !empty($_POST['imie']))
    {
        $imie = $_POST['imie'];
    }
    else if ($zapisano != "Nie")
    {
        $zapisano = "Nie";
        $blad = "Nie podano imienia!";
    }

    // Sprawdzanie nazwiska
    if (isset($_POST['nazwisko']) && !empty($_POST['nazwisko']))
    {
        $nazwisko = $_POST['nazwisko'];
    }
    else if ($zapisano != "Nie")
    {
        $zapisano = "Nie";
        $blad = "Nie podano nazwiska!";
    }

    // Sprawdzanie etatu
    if (isset($_POST['etat']) && !empty($_POST['etat']))
    {
        $etat = $_POST['etat'];
    }
    else if ($zapisano != "Nie")
    {
        $zapisano = "Nie";
        $blad = "Nie wybrano etatu!";
    }

    // Sprawdzanie szefa
    if (isset($_POST['szef']) && !empty($_POST['szef']) && $_POST['szef'] != 0)
    {
        $szef = $_POST['szef'];
    }
    else if ($zapisano != "Nie")
    {
        $zapisano = "Nie";
        $blad = "Nie wybrano szefa!";
    }

    // Sprawdzanie zespolu
    if (isset($_POST['zespol']) && !empty($_POST['zespol']) && $_POST['zespol'] != 0)
    {
        $zespol = $_POST['zespol'];
    }
    else if ($zapisano != "Nie")
    {
        $zapisano = "Nie";
        $blad = "Nie wybrano zespołu!";
    }

    // Sprawdzanie daty zatrudnienia
    if (isset($_POST['data_zatrudnienia']) && !empty($_POST['data_zatrudnienia']))
    {
        $data_zatrudnienia = DateTime::createFromFormat('Y-m-d', $_POST['data_zatrudnienia'])->format('Y-m-d');
    }
    else if ($zapisano != "Nie")
    {
        $zapisano = "Nie";
        $blad = "Podano złą datę zatrudnienia!";
    }

    // Sprawdzanie placy podstawowej
    if (isset($_POST['placa_pod']) && !empty($_POST['placa_pod']) && $_POST['placa_pod'] >= 0 && is_numeric($_POST['placa_pod']))
    {
        $placa_pod = $_POST['placa_pod'];
    }
    else if ($zapisano != "Nie")
    {
        $zapisano = "Nie";
        $blad = "Wprowadzoną złą płacę podstawową!";
    }

    if (isset($_POST['placa_dod']) && $_POST['placa_dod'] >= 0 && is_numeric($_POST['placa_dod']))
    {
        $placa_dod = $_POST['placa_dod'];
    }
    else if (!empty($_POST['placa_dod']))
    {
        $zapisano = "Nie";
        $blad = "Podano złą płacę dodatkową!";
    }
    else if ($zapisano != "Nie")
    {
        $placa_dod = NULL;
    }

    if ($zapisano == "")
    {
        try
        {
            $id_pracownika = $pdo->query("SELECT MAX(ID_PRAC) AS ID FROM pracownicy")->fetch(PDO::FETCH_ASSOC)['ID'] + 10;

            $stmt = $pdo->prepare("INSERT INTO pracownicy (ID_PRAC, NAZWISKO, IMIE, ETAT, ID_SZEFA, ZATRUDNIONY, PLACA_POD, PLACA_DOD, ID_ZESP) VALUES (:id_pracownika, :nazwisko, :imie, :etat, :szef, :data_zatrudnienia, :placa_pod, :placa_dod , :id_zesp)");
            $stmt->bindParam(':id_pracownika', $id_pracownika);
            $stmt->bindParam(':nazwisko', $nazwisko);
            $stmt->bindParam(':imie', $imie);
            $stmt->bindParam(':etat', $etat);
            $stmt->bindParam(':szef', $szef);
            $stmt->bindParam(':data_zatrudnienia', $data_zatrudnienia);
            $stmt->bindParam(':placa_pod', $placa_pod);
            $stmt->bindParam(':placa_dod', $placa_dod);
            $stmt->bindParam(':id_zesp', $zespol);
            $stmt->execute();
        } catch (PDOException $e) {
            $zapisano = "Nie";
            $blad = $e->getMessage();
        } finally
        {
            $zapisano = "Tak";
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
    <title>Dodawanie Pracownika</title>
</head>
<body>

    <div class="container">
        <ul class="nav nav-tabs">
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
                <h3 class="mt-4">Dodawania pracownika</h3>
                <?php
                    $stmt = $pdo->prepare('SELECT NAZWA FROM etaty');
                    $stmt->execute();
                    $etaty = $stmt->fetchAll();

                    $stmt = $pdo->prepare('SELECT CONCAT(IMIE, " ", NAZWISKO) AS NAZWA FROM pracownicy');
                    $stmt->execute();
                    $szefy = $stmt->fetchAll();

                    $stmt = $pdo->prepare('SELECT NAZWA FROM zespoly');
                    $stmt->execute();
                    $zespoly = $stmt->fetchAll();
                ?>
                <?php if ($zapisano == "Tak"): ?>
                    <div class="alert alert-success">Poprawnie dodano pracownika!</div>
                <?php elseif ($zapisano == "Nie" && $blad != ""): ?>
                    <div class="alert alert-danger">Wystąpił błąd w trakcie dodawania pracownika! <br><?php echo $blad ?></div>
                <?php endif; ?>

                <form class="mt-4" method="post" action="" novalidate>

                    <div class="form-floating mb-3">
                        <input type="imie" name="imie" class="form-control" id="floatingInputImie" placeholder="Jan">
                        <label for="floatingInputImie">Imię</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="Nazwisko" name="nazwisko" class="form-control" id="floatingInputNazwisko" placeholder="Kowalski">
                        <label for="floatingInputNazwisko">Nazwisko</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select mb-3" id="Etat" name="etat" aria-label="Default select example">
                            <option value="" selected>Wybierz etat</option>
                            <?php
                                foreach ($etaty as $etat) {
                                    echo '<option value="' . htmlspecialchars($etat['NAZWA']) . '">' . htmlspecialchars($etat['NAZWA']) . '</option>';
                                }
                            ?>      
                        </select>
                        <label for="Etat">Etat</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select mb-3" name="szef" id="floatingSelectSzef" aria-label="Default select example">
                            <option value="0" selected>Brak szefa</option>
                            <?php
                            $id = 100;
                            foreach ($szefy as $szef) {
                                echo '<option value="' . $id . '">' . htmlspecialchars($szef['NAZWA']) . '</option>';
                                $id += 10;
                            }
                        ?>
                        </select>
                        <label for="floatingSelectSzef">Szef</label>
                    </div>

                    <div class="form-floating mb-3">
                        <select class="form-select mb-3" name="zespol" id="zespol">
                            <option value="0" selected>Brak Zespołu</option>
                            <?php
                            $id = 10;
                            foreach ($zespoly as $zespol) {
                                echo '<option value="' . $id . '">' . htmlspecialchars($zespol['NAZWA']) . '</option>';
                                $id += 10;
                            }
                            ?>
                        </select>
                        <label for="zespol">Zespół</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" name="data_zatrudnienia" id="data_zatrudnienia">
                        <label for="data_zatrudnienia">Data zatrudnienia</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" name="placa_pod" id="floatingInputPłaca" placeholder="1000" min="0">
                        <label for="floatingInputPłaca">Płaca podstawowa</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" name="placa_dod" id="floatingInputPłacaDodatkowa" placeholder="1000" min="0">
                        <label for="floatingInputPłacaDodatkowa">Płaca dodatkowa</label>
                    </div>

                    <button type="submit" name="submit" class="btn btn-success"><i class="bi bi-plus-lg m-auto"></i>Zapisz dane</button>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
