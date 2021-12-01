<?php

if (!isset($_POST["type"])) {
    header("Location: https://alpha.kswe.ginf.ch/planer/schuljahre/");
    exit();
}

$type = $_POST["type"];

?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?php echo $type;?> erstellen</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/aussehen.css">
    <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/icons/zeugnis.png">
    <script src="https://alpha.kswe.ginf.ch/planer/commands.js"></script>
</head>
<body>

<?php

if ($type == "Schuljahr"){
    $action = "schuljahre/";
    $inputs = '<label for="schuljahr">Schuljahr: </label>';
    $inputs = $inputs . '<input type="text" name="schuljahr" id="schuljahr" required>';
} elseif ($type == "Fach"){
    $action = "faecher/";
    $inputs = '<label for="fach">Fach: </label>';
    $inputs = $inputs . '<input type="text" name="fach" id="fach" required>';

    $inputs = $inputs . '<label for="gewichtung">Gewichtung: </label>';
    $inputs = $inputs . '<input type="number" name="gewichtung" id="gewichtung" step=".01" value="1">';
} elseif ($type == "Prüfung"){
    $action = "pruefungen/";
    $inputs = '<label for="pruefung">Prüfung: </label>';
    $inputs = $inputs . '<input type="text" name="pruefung" id="pruefung" required>';

    $inputs = $inputs . '<label for="note">Note: </label>';
    $inputs = $inputs . '<input type="number" name="note" id="note" step=".01" required>';

    $inputs = $inputs . '<label for="gewichtung">Gewichtung: </label>';
    $inputs = $inputs . '<input type="number" name="gewichtung" id="gewichtung" value="1" step=".01" required>';

    // Der Heutige Tag wird als Standard alsgesehen, also wird es es ausfüllen
    $tag = date("d");
    $monat = date("m");
    $jahr = date("Y");
    $today = $jahr . "-" . $monat . "-" . $tag;

    $inputs = $inputs . '<label for="datum">Datum: </label>';
    $inputs = $inputs . '<input type="date" name="datum" id="datum" value="' . $today . '">';
}

$form = '<form method="post" action="' . $action . '">';
$form = $form . $inputs;

$form = $form . '<br><button type="submit" name="erstellen" id="erstellen">Erstellen</button>';
$form = $form . "</form>";

echo $form;

?>

</body>
</html>