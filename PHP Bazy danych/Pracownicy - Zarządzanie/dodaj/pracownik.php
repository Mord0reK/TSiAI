<?php
require_once 'database.php';

$zapisano = "";
$blad = "";
$blad_imie = "";
$blad_nazwisko = "";
$blad_etat = "";
$blad_szef = "";
$blad_zespol = "";
$blad_data_zatrudnienia = "";
$blad_placa_pod = "";
$blad_placa_dod = "";

if (isset($_POST['submit'])) {





    // Sprawdzanie imienia
    if (isset($_POST['imie']) && !empty($_POST['imie']) && preg_match('/[^a-zA-ZąĆćęłńóśżźĄĆĘŁŃÓŚŻŹ-]/', $_POST['imie']))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_imie = "W polu znalazły się inne znaki niż litery";
    }
    else if (isset($_POST['imie']) && empty($_POST['imie']))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_imie = "Nie podano imienia";
    }

    else if (isset($_POST['imie']) && !empty($_POST['imie']))
    {
        $imie = $_POST['imie'];
    }





    // Sprawdzanie nazwiska
    if (isset($_POST['nazwisko']) && !empty($_POST['nazwisko']) && preg_match('/[^a-zA-ZąĆćęłńóśżźĄĆĘŁŃÓŚŻŹ-]/', $_POST['nazwisko']))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_nazwisko = "W polu znalazły się inne znaki niż litery";
    }
    else if (isset($_POST['nazwisko']) && empty($_POST['nazwisko']))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_nazwisko = "Nie podano nazwiska";
    }

    else if (isset($_POST['nazwisko']) && !empty($_POST['nazwisko']))
    {
        $nazwisko = $_POST['nazwisko'];
    }





    // Sprawdzanie etatu
    if (isset($_POST['etat']) && (empty($_POST['etat']) || $_POST['etat'] == "Wybierz etat"))
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_etat = "Nie wybrano etatu";
    }
    else if (isset($_POST['etat']))
    {
        $etat = $_POST['etat'];
    }





    // Sprawdzanie szefa
    if (!isset($_POST['szef']) || $_POST['szef'] == "0")
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_szef = "Nie wybrano szefa";
    }
    else if (isset($_POST['szef']))
    {
        $szef = $_POST['szef'];
    }





    // Sprawdzanie zespolu
    if (!isset($_POST['zespol']) || $_POST['zespol'] == "0")
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_zespol = "Nie podano zespołu";
    }
    else if (isset($_POST['zespol']))
    {
        $zespol = $_POST['zespol'];
    }





    // Sprawdzanie daty zatrudnienia
    if (isset($_POST['data_zatrudnienia']) && empty($_POST['data_zatrudnienia']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_data_zatrudnienia = "Nie podano daty zatrudnienia";
    }
    else if (isset($_POST['data_zatrudnienia']) && !empty($_POST['data_zatrudnienia']))
    {
        $data_zatrudnienia = DateTime::createFromFormat('Y-m-d', $_POST['data_zatrudnienia'])->format('Y-m-d');
    }





    // Sprawdzanie placy podstawowej
    if (isset($_POST['placa_pod']) && empty($_POST['placa_pod']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_pod = "Nie podano plac podstawowej";
    }
    else if (isset($_POST['placa_pod']) && !is_numeric($_POST['placa_pod']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_pod = "Wprowadzono złą płacę";
    }
    else if (isset($_POST['placa_pod']) && $_POST['placa_pod'] <= 0)
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_pod = "Płaca nie może być mniejsza od 0";
    }
    else if (isset($_POST['placa_pod']))
    {
        $placa_pod = $_POST['placa_pod'];
    }





    // Sprawdzanie placy dodatkowej
    if (!empty($_POST['placa_dod']) && !is_numeric($_POST['placa_dod']))
    {
        $zapisano = "Nie";
        $blad = "Tak";
        $blad_placa_dod = "Wprowadzono złą płacę dodatkową";
    }
    else if (!empty($_POST['placa_dod']))
    {
        $placa_dod = $_POST['placa_dod'];
    }
    else
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
    <title>Dodawanie Pracownika</title>
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
                <?php endif; ?>

                <form class="mt-4" method="post" action="" novalidate>

                    <div class="form-floating mb-3">
                        <?php if ($blad_imie != ""): ?>
                            <input type="text" name="imie" class="form-control <?php if ($blad != ""): ?> is-invalid <?php endif; ?> " id="floatingInputImie" placeholder="Jan" value="<?php echo isset($_POST['imie']) ? htmlspecialchars($_POST['imie']) : ''; ?>">
                            <label for="floatingInputImie">Imię</label>
                            <div class="invalid-feedback">
                                <?php echo $blad_imie; ?>
                            </div>
                        <?php else: ?>
                            <input type="text" name="imie" class="form-control" id="floatingInputImie" placeholder="Jan" value="<?php echo isset($_POST['imie']) ? htmlspecialchars($_POST['imie']) : ''; ?>">
                            <label for="floatingInputImie">Imię</label>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating mb-3">
                        <?php if ($blad_nazwisko != ""): ?>
                            <input type="text" name="nazwisko" class="form-control <?php if ($blad != ""): ?> is-invalid <?php endif; ?>" id="floatingInputNazwisko" placeholder="Kowalski" value="<?php echo isset($_POST['nazwisko']) ? htmlspecialchars($_POST['nazwisko']) : ''; ?>">
                            <label for="floatingInputNazwisko">Nazwisko</label>
                            <div class="invalid-feedback">
                                <?php echo $blad_nazwisko; ?>
                            </div>
                        <?php else: ?>
                            <input type="text" name="nazwisko" class="form-control" id="floatingInputNazwisko" placeholder="Kowalski" value="<?php echo isset($_POST['nazwisko']) ? htmlspecialchars($_POST['nazwisko']) : ''; ?>">
                            <label for="floatingInputNazwisko">Nazwisko</label>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating mb-3">
                        <?php if ($blad_etat != ""): ?>
                            <select class="form-select mb-3 <?php if ($blad != ""): ?> is-invalid <?php endif; ?> " id="Etat" name="etat" aria-label="Default select example">
                                <option value="" <?php if (!isset($etat) || $etat == ""): ?>selected<?php endif; ?>>Wybierz etat</option>
                                <?php
                                foreach ($etaty as $row) {
                                    $sel = (isset($etat) && $etat == $row['NAZWA']) ? ' selected' : '';
                                    echo '<option value="' . htmlspecialchars($row['NAZWA']) . '"' . $sel . '>' . htmlspecialchars($row['NAZWA']) . '</option>';
                                }
                                ?>
                            </select>
                            <label for="Etat">Etat</label>
                            <div class="invalid-feedback">
                                <?php echo $blad_etat; ?>
                            </div>
                        <?php else: ?>
                        <select class="form-select mb-3" id="Etat" name="etat" aria-label="Default select example">
                            <option value="" selected>Wybierz etat</option>
                            <?php
                                foreach ($etaty as $row) {
                                    echo '<option value="' . htmlspecialchars($row['NAZWA']) . '">' . htmlspecialchars($row['NAZWA']) . '</option>';
                                }
                            ?>
                        </select>
                        <label for="Etat">Etat</label>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating mb-3">
                        <?php if ($blad_szef != ""): ?>
                            <select class="form-select mb-3 <?php if ($blad != ""): ?> is-invalid <?php endif; ?>" name="szef" id="floatingSelectSzef" aria-label="Default select example">
                                <?php $szef_sel = (!isset($szef) || $szef == "0") ? ' selected' : ''; ?>
                                <option value="0"<?php echo $szef_sel; ?>>Brak szefa</option>
                                <?php
                                $id = 100;
                                foreach ($szefy as $row) {
                                    $sel = (isset($szef) && $szef == $id) ? ' selected' : '';
                                    echo '<option value="' . $id . '"' . $sel . '>' . htmlspecialchars($row['NAZWA']) . '</option>';
                                    $id += 10;
                                }
                            ?>
                            </select>
                            <label for="floatingSelectSzef">Szef</label>
                            <div class="invalid-feedback">
                                <?php echo $blad_szef; ?>
                            </div>
                        <?php else: ?>
                            <select class="form-select mb-3" name="szef" id="floatingSelectSzef" aria-label="Default select example">
                                <option value="0" selected>Brak szefa</option>
                                <?php
                                $id = 100;
                                foreach ($szefy as $row) {
                                    echo '<option value="' . $id . '">' . htmlspecialchars($row['NAZWA']) . '</option>';
                                    $id += 10;
                                }
                                ?>
                            </select>
                            <label for="floatingSelectSzef">Szef</label>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating mb-3">
                        <?php if ($blad_zespol != ""): ?>
                            <select class="form-select mb-3 <?php if ($blad != ""): ?> is-invalid <?php endif; ?> " name="zespol" id="zespol">
                                <?php $zespol_sel = (!isset($zespol) || $zespol == "0") ? ' selected' : ''; ?>
                                <option value="0"<?php echo $zespol_sel; ?>>Brak Zespołu</option>
                                <?php
                                $id = 10;
                                foreach ($zespoly as $row) {
                                    $sel = (isset($zespol) && $zespol == $id) ? ' selected' : '';
                                    echo '<option value="' . $id . '"' . $sel . '>' . htmlspecialchars($row['NAZWA']) . '</option>';
                                    $id += 10;
                                }
                                ?>
                            </select>
                            <label for="zespol">Zespół</label>
                            <div class="invalid-feedback">
                                <?php echo $blad_zespol; ?>
                            </div>
                        <?php else: ?>
                            <select class="form-select mb-3" name="zespol" id="zespol">
                                <option value="0" selected>Brak Zespołu</option>
                                <?php
                                $id = 10;
                                foreach ($zespoly as $row) {
                                    echo '<option value="'  . $id . '">' . htmlspecialchars($row['NAZWA']) . '</option>';
                                    $id += 10;
                                }
                                ?>
                            </select>
                            <label for="zespol">Zespół</label>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating mb-3">
                        <?php if ($blad_data_zatrudnienia != ""): ?>
                            <input type="date" class="form-control <?php if ($blad != ""): ?> is-invalid <?php endif; ?>" name="data_zatrudnienia" id="data_zatrudnienia" value="<?php echo isset($_POST['data_zatrudnienia']) ? htmlspecialchars($_POST['data_zatrudnienia']) : ''; ?>">
                            <label for="data_zatrudnienia">Data zatrudnienia</label>
                            <div class="invalid-feedback">
                                <?php echo $blad_data_zatrudnienia; ?>
                            </div>
                        <?php else: ?>
                            <input type="date" class="form-control" name="data_zatrudnienia" id="data_zatrudnienia" value="<?php echo isset($_POST['data_zatrudnienia']) ? htmlspecialchars($_POST['data_zatrudnienia']) : ''; ?>">
                            <label for="data_zatrudnienia">Data zatrudnienia</label>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating mb-3">
                        <?php if ($blad_placa_pod != ""): ?>
                            <input type="number" class="form-control is-invalid" name="placa_pod" id="floatingInputPłaca" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_pod']) ? htmlspecialchars($_POST['placa_pod']) : ''; ?>">
                            <label for="floatingInputPłaca">Płaca podstawowa</label>
                            <div class="invalid-feedback">
                                <?php echo $blad_placa_pod; ?>
                            </div>
                        <?php else: ?>
                            <input type="number" class="form-control" name="placa_pod" id="floatingInputPłaca" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_pod']) ? htmlspecialchars($_POST['placa_pod']) : ''; ?>">
                            <label for="floatingInputPłaca">Płaca podstawowa</label>
                        <?php endif; ?>
                    </div>

                    <div class="form-floating mb-3">
                        <?php if ($blad_placa_dod != ""): ?>
                            <input type="number" class="form-control is-invalid" name="placa_dod" id="floatingInputPłacaDodatkowa" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_dod']) ? htmlspecialchars($_POST['placa_dod']) : ''; ?>">
                            <label for="floatingInputPłacaDodatkowa">Płaca dodatkowa</label>
                            <div class="invalid-feedback">
                                <?php echo $blad_placa_dod; ?>
                            </div>
                        <?php else: ?>
                            <input type="number" class="form-control" name="placa_dod" id="floatingInputPłacaDodatkowa" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_dod']) ? htmlspecialchars($_POST['placa_dod']) : ''; ?>">
                            <label for="floatingInputPłacaDodatkowa">Płaca dodatkowa</label>
                        <?php endif; ?>
                    </div>

                    <button type="submit" name="submit" class="btn btn-success"><i class="bi bi-plus-lg m-auto"></i>Zapisz dane</button>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
