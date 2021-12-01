<?php

if (!isset($_POST["type"])) {
    header("Location: https://alpha.kswe.ginf.ch");
    exit();
}

$type = $_POST["type"];

$conn = mysqli_connect($hostname, $username, $password, $database) // mit DB verbinden


/*
switch ($type) {
    case "pruefung":
        $i = -1;
        break;
    case "fach":
        $i = 0;
        break;
    case "schuljahr":
        $i = 1;
        break;
    case "user":
        $i = 2;
        break;

} */

switch ($type) {
    case "Schuljahr":
        $jahr_id = $_POST["id"];
        $sql = "select schuljahr from schuljahr where jahr_id = '$jahr_id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $schuljahr = $row["schuljahr"];

        $action = "faecher/";
        $inputs = '<label for="schuljahr">Schuljahr: </label>';
        $inputs = $inputs . '<input type="text" name="schuljahr" id="schuljahr" value="' . $schuljahr . '" required>';
        $titel = $schuljahr;
        break;

    case "Fach":
        $fach_id = $_POST["id"];
        $sql = "select fach_titel, gewichtung from faecher where fach_id = '$fach_id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        $fach = $row["fach_titel"];
        $gewichtung = $row["gewichtung"];

        $action = "pruefungen/";
        $inputs = '<label for="fach">Fach: </label>';
        $inputs = $inputs . '<input type="text" name="fach" id="fach" value="' . $fach . '" required>';

        $inputs = $inputs . '<label for="gewichtung">Gewichtung: </label>';
        $inputs = $inputs . '<input type="number" name="gewichtung" id="gewichtung" step=".01" value="' . $gewichtung . '">';
        $titel = $fach;
        break;

    case "Prüfung":
        $note_id = $_POST["id"];
        $sql = "select note_titel, note, gewichtung, datum from noten where note_id = '$note_id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        $pruefung = $row["note_titel"];
        $note = $row["note"];
        $gewichtung = $row["gewichtung"];
        $datum = $row["datum"];

        $action = "pruefungen/";
        $inputs = '<label for="pruefung">Prüfung: </label>';
        $inputs = $inputs . '<input type="text" name="pruefung" id="pruefung" value="' . $pruefung . '" required>';

        $inputs = $inputs . '<label for="note">Note: </label>';
        $inputs = $inputs . '<input type="number" name="note" id="note" step=".01" value="' . $note . '" required>';

        $inputs = $inputs . '<label for="gewichtung">Gewichtung: </label>';
        $inputs = $inputs . '<input type="number" name="gewichtung" id="gewichtung" step=".01" value="' . $gewichtung . '" required>';


        $inputs = $inputs . '<label for="datum">Datum: </label>';
        $inputs = $inputs . '<input type="date" name="datum" id="datum" value="' . $datum . '">';

        if ($_POST["where"] == "dashboard") {
            $inputs = $inputs . '<input type="hidden" name="where" id="where" value="dashboard">';
        }

        $titel = $pruefung;
        break;
    case "User":
        $user_id = $_POST["id"];
        $sql = "select username, vorname, nachname, email from users where BINARY user_id = '$user_id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        $username = $row["username"];
        $vorname = $row["vorname"];
        $nachname = $row["nachname"];
        $email = $row["email"];

        $action = 'schuljahre/';

        $inputs = '<label for="username">Username: </label>';
        $inputs .= '<input type="text" name="username" id="username" value="' . $username . '" required>';

        $inputs .= '<label for="vorname">Vorname: </label>';
        $inputs .= '<input type="text" name="vorname" id="vorname" value="' . $vorname . '" required>';

        $inputs .= '<label for="nachname">Nachname: </label>';
        $inputs .= '<input type="text" name="nachname" id="nachname" value="' . $nachname . '" required>';

        $inputs .= '<label for="email">Email: </label>';
        $inputs .= '<input type="email" name="email" id="email" value="' . $email . '" required>';

        $inputs .= '<label for="passwort">Passwort:</label>';
        $inputs .= '<input type="password" id="passwort" name="passwort" required onfocusout="password_checker()">';
        $inputs .= '<label for="passwort_bestätigen">Passwort bestätigen:</label>';
        $inputs .= '<input type="password" id="passwort_bestätigen" name="passwort_bestätigen" required onfocusout="password_checker()">';
        $inputs .= '<p style="color: #FF0000" id="passwort_ungleich"></p>';
        break;
}


$form = '<form id="form" method="post" action="' . $action . '">';

$form = $form . $inputs;
$form = $form . '<input type="hidden" name="type" id="type" value="' . $type . '">';
$form = $form . '<input type="hidden" name="id" id="id" value="' . $_POST["id"] . '">';

$form = $form . '<br><button type="submit" name="aendern" id="aendern">Speichern</button>';
if ($type == "User") {
    $form .= '<span style="float: right; background: red; color: white;" name="loeschen" id="loeschen" onclick="loeschen(' . "'" . $user_id . "'" . ')">Löschen</span>';
} else {
    $form .= '<button style="float: right; background: red; color: white;" name="loeschen" id="loeschen">Löschen</button>';
}
$form = $form . "</form>";

?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titel; ?> ändern</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/aussehen.css">
    <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/icons/zeugnis.png">
    <script src="https://alpha.kswe.ginf.ch/planer/commands.js"></script>

    </head>
<body>

<?php
echo $form;
echo $delete;

if (isset($_POST["grund"])) {
    switch ($_POST["grund"]) {
        case "email":
            echo "Email bereits vorhanden";
            break;
        case "username":
            echo "Username bereits vorhanden";
            break;
        case "passwort":
            echo "Passwörter stimmen nicht überein";
            break;
        default:
            echo "hi";
            break;
    }
}
?>

</body>
</html>

<?php mysqli_close($conn); ?>