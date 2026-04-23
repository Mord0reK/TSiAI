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

// Pobranie odpowiednich danych do formularza

$stmt = $pdo->prepare("SELECT * FROM pracownicy WHERE ID_PRAC = :ID_PRAC");
$stmt->bindParam(':ID_PRAC', $_GET['id']);
$stmt->execute();
$pracownik = $stmt->fetch(PDO::FETCH_ASSOC);


if (isset($_POST['submit'])) {


    // Sprawdzanie imienia
    if (isset($_POST['imie']) && !empty($_POST['imie']) && mb_strlen($_POST['imie']) > 20)
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_imie = "Imię pracownika nie może być dłuższe niż 20 znaków";
    }
    else if (isset($_POST['imie']) && !empty($_POST['imie']) && preg_match('/[^a-zA-ZąĆćęłńóśżźĄĆĘŁŃÓŚŻŹ-]/u', $_POST['imie']))
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
    if (isset($_POST['nazwisko']) && !empty($_POST['nazwisko']) && mb_strlen($_POST['nazwisko']) > 15)
    {
        $blad = "Tak";
        $zapisano = "Nie";
        $blad_nazwisko = "Nazwisko pracownika nie może być dłuższe niż 15 znaków";
    }
    else if (isset($_POST['nazwisko']) && !empty($_POST['nazwisko']) && preg_match('/[^a-zA-ZąĆćęłńóśżźĄĆĘŁŃÓŚŻŹ-]/u', $_POST['nazwisko']))
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
    if (isset($_POST['szef']) && $_POST['szef'] == "0")
    {
        $szef = Null;
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
        $input_date = DateTime::createFromFormat('Y-m-d', $_POST['data_zatrudnienia']);
        $today = new DateTime();
        $min_date = (clone $today)->modify('-100 years');
        $max_date = (clone $today)->modify('+100 years');

        if ($input_date < $min_date || $input_date > $max_date) {
            $zapisano = "Nie";
            $blad = "Tak";
            $blad_data_zatrudnienia = "Data nie może być o 100 lat do tyłu oraz 100 lat do przodu";
        } else {
            $data_zatrudnienia = $input_date->format('Y-m-d');
        }
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
            $id_pracownika = $_GET['id'];

            $stmt = $pdo->prepare("UPDATE pracownicy SET NAZWISKO = :nazwisko, IMIE = :imie, ETAT = :etat, ID_SZEFA = :szef, ZATRUDNIONY = :data_zatrudnienia, PLACA_POD = :placa_pod, PLACA_DOD = :placa_dod, ID_ZESP = :id_zesp WHERE ID_PRAC = :id_pracownika");
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
            
            $stmt = $pdo->prepare("SELECT * FROM pracownicy WHERE ID_PRAC = :ID_PRAC");
            $stmt->bindParam(':ID_PRAC', $id_pracownika);
            $stmt->execute();
            $pracownik = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>Edytowanie Pracownika</title>
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
        </ul>
        <div class="row">
            <div class="col-12">
                <h3 class="mt-4">Edytowanie pracownika</h3>

                <!--Query do uzupełniania-->

                <?php
                    $stmt = $pdo->prepare('SELECT NAZWA FROM etaty');
                    $stmt->execute();
                    $etaty = $stmt->fetchAll();

                    $stmt = $pdo->prepare('SELECT ID_PRAC, CONCAT(IMIE, " ", NAZWISKO) AS NAZWA FROM pracownicy');
                    $stmt->execute();
                    $szefy = $stmt->fetchAll();

                    $stmt = $pdo->prepare('SELECT ID_ZESP, NAZWA FROM zespoly');
                    $stmt->execute();
                    $zespoly = $stmt->fetchAll();

                    $stmt = $pdo->prepare('SELECT ID_PRAC FROM pracownicy');
                    $stmt->execute();
                    $pracownicy_id = $stmt->fetchAll();
                ?>

                <?php if ($zapisano == "Tak"): ?>
                    <div class="alert alert-success">Poprawnie edytowano pracownika!</div>
                <?php endif; ?>


                <?php if (in_array($_GET['id'], array_column($pracownicy_id, 'ID_PRAC'))): ?>
                    <form class="mt-4" method="post" action="" novalidate>

                        <!--Kod Imie-->

                        <div class="form-floating mb-3">
                            <?php if ($blad_imie != ""): ?>
                                <input type="text" name="imie" class="form-control <?php if ($blad != ""): ?> is-invalid <?php endif; ?> " id="floatingInputImie" placeholder="Jan" value="<?php echo isset($_POST['imie']) ? htmlspecialchars($_POST['imie']) : ''; ?>">
                                <label for="floatingInputImie">Imię</label>
                                <div class="invalid-feedback">
                                    <?php echo $blad_imie; ?>
                                </div>
                            <?php else: ?>
                                <input type="text" name="imie" class="form-control" id="floatingInputImie" placeholder="Jan" value="<?php echo isset($_POST['imie']) ? htmlspecialchars($_POST['imie']) : $pracownik['IMIE']; ?>">
                                <label for="floatingInputImie">Imię</label>
                            <?php endif; ?>
                        </div>

                        <!--Kod Nazwisko-->

                        <div class="form-floating mb-3">
                            <?php if ($blad_nazwisko != ""): ?>
                                <input type="text" name="nazwisko" class="form-control <?php if ($blad != ""): ?> is-invalid <?php endif; ?>" id="floatingInputNazwisko" placeholder="Kowalski" value="<?php echo isset($_POST['nazwisko']) ? htmlspecialchars($_POST['nazwisko']) : ''; ?>">
                                <label for="floatingInputNazwisko">Nazwisko</label>
                                <div class="invalid-feedback">
                                    <?php echo $blad_nazwisko; ?>
                                </div>
                            <?php else: ?>
                                <input type="text" name="nazwisko" class="form-control" id="floatingInputNazwisko" placeholder="Kowalski" value="<?php echo isset($_POST['nazwisko']) ? htmlspecialchars($_POST['nazwisko']) : $pracownik['NAZWISKO']; ?>">
                                <label for="floatingInputNazwisko">Nazwisko</label>
                            <?php endif; ?>
                        </div>

                        <!--Kod Etat-->

                        <div class="form-floating mb-3">
                            <?php if ($blad_etat != ""): ?>
                                <select class="form-select mb-3 <?php if ($blad != ""): ?> is-invalid <?php endif; ?> " id="Etat" name="etat" aria-label="Default select example">
                                    <option value="" <?php echo (!isset($etat) || $etat === "") ? 'selected' : ''; ?>>Wybierz etat</option>
                                    <?php
                                        foreach ($etaty as $row) {
                                            $etat_selected = (isset($etat) && $etat === $row['NAZWA']) || (!isset($_POST['etat']) && $pracownik['ETAT'] === $row['NAZWA']) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($row['NAZWA']) . '" ' . $etat_selected . '>' . htmlspecialchars($row['NAZWA']) . '</option>';
                                        };
                                    ?>
                                </select>
                                <label for="Etat">Etat</label>
                                <div class="invalid-feedback">
                                    <?php echo $blad_etat; ?>
                                </div>
                            <?php else: ?>
                            <select class="form-select mb-3" id="Etat" name="etat" aria-label="Default select example">
                                <option value="" <?php echo (!isset($pracownik['ETAT']) || $pracownik['ETAT'] === "") ? 'selected' : ''; ?>>Wybierz etat</option>
                                <?php foreach ($etaty as $row): ?>
                                    <?php $etat_selected = (isset($pracownik['ETAT']) && $pracownik['ETAT'] === $row['NAZWA']) ? 'selected' : ''; ?>
                                    <option value="<?php echo htmlspecialchars($row['NAZWA']); ?>" <?php echo $etat_selected; ?>><?php echo htmlspecialchars($row['NAZWA']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <label for="Etat">Etat</label>
                            <?php endif; ?>
                        </div>

                        <!--Kod Szef-->

                        <div class="form-floating mb-3">
                            <?php if ($blad_szef != ""): ?>
                                <select class="form-select mb-3 <?php if ($blad != ""): ?> is-invalid <?php endif; ?>" name="szef" id="floatingSelectSzef" aria-label="Default select example">
                                    <?php $szef_sel = (!isset($_POST['szef']) || $_POST['szef'] == "0") ? ' selected' : ''; ?>
                                    <option value="0"<?php echo $szef_sel; ?>>Brak szefa</option>
                                    <?php
                                    foreach ($szefy as $row)
                                    {
                                        $sel = (isset($_POST['szef']) && $_POST['szef'] == $row['ID_PRAC']) || (!isset($_POST['szef']) && $row['ID_PRAC'] == $pracownik['ID_SZEFA']) ? ' selected' : '';
                                        echo '<option value="' . $row['ID_PRAC'] . '"' . $sel . '>' . htmlspecialchars($row['NAZWA']) . '</option>';
                                    }
                                ?>
                                </select>
                                <label for="floatingSelectSzef">Szef</label>
                                <div class="invalid-feedback">
                                    <?php echo $blad_szef; ?>
                                </div>
                            <?php else: ?>
                                <select class="form-select mb-3" name="szef" id="floatingSelectSzef" aria-label="Default select example">
                                    <?php $szef_sel = (!isset($_POST['szef']) && !isset($pracownik['ID_SZEFA'])) || (isset($_POST['szef']) && $_POST['szef'] == "0") ? ' selected' : ''; ?>
                                    <option value="0" <?php echo $szef_sel; ?>>Brak szefa</option>
                                    <?php
                                    foreach ($szefy as $row) {
                                        $sel = (isset($_POST['szef']) && $_POST['szef'] == $row['ID_PRAC']) || (!isset($_POST['szef']) && $row['ID_PRAC'] == $pracownik['ID_SZEFA']) ? ' selected' : '';
                                        echo '<option value="' . $row['ID_PRAC'] . '"' . $sel . '>' . htmlspecialchars($row['NAZWA']) . '</option>';
                                    }
                                    ?>
                                </select>
                                <label for="floatingSelectSzef">Szef</label>
                            <?php endif; ?>
                        </div>

                        <!--Kod Zespół-->

                        <div class="form-floating mb-3">
                            <?php if ($blad_zespol != ""): ?>
                                <select class="form-select mb-3 <?php if ($blad != ""): ?> is-invalid <?php endif; ?> " name="zespol" id="zespol">
                                    <?php $zespol_sel = (!isset($_POST['zespol']) || $_POST['zespol'] == "0") ? ' selected' : ''; ?>
                                    <option value="0"<?php echo $zespol_sel; ?>>Brak Zespołu</option>
                                    <?php
                                    foreach ($zespoly as $row) {
                                        $sel = (isset($_POST['zespol']) && $_POST['zespol'] == $row['ID_ZESP']) || (!isset($_POST['zespol']) && $row['ID_ZESP'] == $pracownik['ID_ZESP']) ? ' selected' : '';
                                        echo '<option value="' . $row['ID_ZESP'] . '"' . $sel . '>' . htmlspecialchars($row['NAZWA']) . '</option>';
                                    }
                                    ?>
                                </select>
                                <label for="zespol">Zespół</label>
                                <div class="invalid-feedback">
                                    <?php echo $blad_zespol; ?>
                                </div>
                            <?php else: ?>
                                <select class="form-select mb-3" name="zespol" id="zespol">
                                    <?php $zespol_sel = (!isset($_POST['zespol']) && !isset($pracownik['ID_ZESP'])) || (isset($_POST['zespol']) && $_POST['zespol'] == "0") ? ' selected' : ''; ?>
                                    <option value="0" <?php echo $zespol_sel; ?>>Brak Zespołu</option>
                                    <?php
                                    foreach ($zespoly as $row) {
                                        $sel = (isset($_POST['zespol']) && $_POST['zespol'] == $row['ID_ZESP']) || (!isset($_POST['zespol']) && $row['ID_ZESP'] == $pracownik['ID_ZESP']) ? ' selected' : '';
                                        echo '<option value="' . $row['ID_ZESP'] . '"' . $sel . '>' . htmlspecialchars($row['NAZWA']) . '</option>';
                                    }
                                    ?>
                                </select>
                                <label for="zespol">Zespół</label>
                            <?php endif; ?>
                        </div>

                        <!--Kod Data zatrudnienia-->

                        <div class="form-floating mb-3">
                            <?php if ($blad_data_zatrudnienia != ""): ?>
                                <input type="date" class="form-control <?php if ($blad != ""): ?> is-invalid <?php endif; ?>" name="data_zatrudnienia" id="data_zatrudnienia" value="<?php echo isset($_POST['data_zatrudnienia']) ? htmlspecialchars($_POST['data_zatrudnienia']) : $pracownik['ZATRUDNIONY']; ?>">
                                <label for="data_zatrudnienia">Data zatrudnienia</label>
                                <div class="invalid-feedback">
                                    <?php echo $blad_data_zatrudnienia; ?>
                                </div>
                            <?php else: ?>
                                <input type="date" class="form-control" name="data_zatrudnienia" id="data_zatrudnienia" value="<?php echo isset($_POST['data_zatrudnienia']) ? htmlspecialchars($_POST['data_zatrudnienia']) : $pracownik['ZATRUDNIONY']; ?>">
                                <label for="data_zatrudnienia">Data zatrudnienia</label>
                            <?php endif; ?>
                        </div>

                        <!--Kod Płaca podstawowa-->

                        <div class="form-floating mb-3">
                            <?php if ($blad_placa_pod != ""): ?>
                                <input type="number" class="form-control is-invalid" name="placa_pod" id="floatingInputPłaca" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_pod']) ? htmlspecialchars($_POST['placa_pod']) : ''; ?>">
                                <label for="floatingInputPłaca">Płaca podstawowa</label>
                                <div class="invalid-feedback">
                                    <?php echo $blad_placa_pod; ?>
                                </div>
                            <?php else: ?>
                                <input type="number" class="form-control" name="placa_pod" id="floatingInputPłaca" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_pod']) ? htmlspecialchars($_POST['placa_pod']) : $pracownik['PLACA_POD']; ?>">
                                <label for="floatingInputPłaca">Płaca podstawowa</label>
                            <?php endif; ?>
                        </div>

                        <!--Kod Płaca dodatkowa-->

                        <div class="form-floating mb-3">
                            <?php if ($blad_placa_dod != ""): ?>
                                <input type="number" class="form-control is-invalid" name="placa_dod" id="floatingInputPłacaDodatkowa" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_dod']) ? htmlspecialchars($_POST['placa_dod']) : ''; ?>">
                                <label for="floatingInputPłacaDodatkowa">Płaca dodatkowa</label>
                                <div class="invalid-feedback">
                                    <?php echo $blad_placa_dod; ?>
                                </div>
                            <?php else: ?>
                                <input type="number" class="form-control" name="placa_dod" id="floatingInputPłacaDodatkowa" placeholder="1000" min="0" value="<?php echo isset($_POST['placa_dod']) ? htmlspecialchars($_POST['placa_dod']) : $pracownik['PLACA_DOD']; ?>">
                                <label for="floatingInputPłacaDodatkowa">Płaca dodatkowa</label>
                            <?php endif; ?>
                        </div>

                        <button type="submit" name="submit" class="btn btn-success"><i class="bi bi-plus-lg m-auto"></i>Zapisz dane</button>
                        <a href="../index.php"><button type="button" name="wroc" class="btn btn-secondary" onclick="wroc()"><i class="bi bi-arrow-left"></i>Wróć</button></a>
                    </form>
                <?php else: ?>
                    <div class="alert alert-danger">Nie znaleziono pracownika o podanym ID! <br> Nie baw się w hackerman'a</div>
                    <a href="../index.php"><button type="button" name="wroc" class="btn btn-secondary" onclick="wroc()"><i class="bi bi-arrow-left"></i>Wróć</button></a>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
