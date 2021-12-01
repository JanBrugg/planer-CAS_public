<?php

// Verbindung zur Datenbank
$conn = mysqli_connect($hostname, $username, $password, $database)

// für spätere Gründe
$angemeldet = False;


// inlcudes the functions for this page
include "../functions.php";

// wenn man nach einer Account-erstellung kommt, wird hier der Account erstellt bzw. überprüft ob es möglich wäre
if (isset($_POST["account_erstellen"])) {

    // Daten speichern in Variablen
    $username = $_POST["username"];
    $username = str_replace("'", "''", $username);
    $vorname = $_POST["vorname"];
    $vorname = str_replace("'", "''", $vorname);
    $nachname = $_POST["nachname"];
    $nachname = str_replace("'", "''", $nachname);
    $email = $_POST["email"];
    $email = str_replace("'", "''", $email);
    $passwort = $_POST["passwort"];
    $passwort = str_replace("'", "''", $passwort);
    $passwort_bestätigen = $_POST["passwort_bestätigen"];
    $passwort_bestätigen = str_replace("'", "''", $passwort_bestätigen);

    // Passwortübereinstimmung prüfen
    if ($passwort != $passwort_bestätigen) {
        header("Location: https://alpha.kswe.ginf.ch/planer/account_erstellen.php?grund=passwort_falsch"); // stimmt nicht überein
        exit();
    }

    // Prüfen ob es die Email schon gibt
    $sql = "select email from users where BINARY email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows != 0) {
        header("Location: https://alpha.kswe.ginf.ch/planer/account_erstellen.php?grund=email_gibs"); // gibts schon
        exit();
    }

    // Überprüfen ob es den Usernamen schon gibt
    $sql = "select username from users where BINARY username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows != 0) {
        header("Location: https://alpha.kswe.ginf.ch/planer/account_erstellen.php?grund=username_gibs"); // gibts schon
        exit();
    }

    // Wenn es keine Übereinstimmung mit bereits existierenden Usern gibt, kommt man bis hier
    // Einzigartige User_ID erstellen
    $user_id = create_user_id($conn);

    // passwort hashen
    $hashed_pass = password_hash($passwort, PASSWORD_DEFAULT);

    // Daten einfügen
    $sql = "insert into users values ('$user_id', '$username', '$vorname', '$nachname', '$email', '$hashed_pass')";
    $result = $conn->query($sql);


    // Bei der Accounterstellung auch einen Cookie setzen (Länge: Session)
    setcookie("user_id", $user_id, 0, "/", "alpha.kswe.ginf.ch");
    $angemeldet = True;

}

// User_ID-Variable setzen
$user_id = $_COOKIE["user_id"];

unset($_COOKIE["jahr_id"]);
setcookie("jahr_id", "", 1, "/", "alpha.kswe.ginf.ch");

unset($_COOKIE["fach_id"]);
setcookie("fach_id", "", 1, "/", "alpha.kswe.ginf.ch");

// Falls man von der Anmeldung kommt
if (isset($_POST["anmelden"])) {
    // Daten speichern
    $username = $_POST["username"];
    $passwort = $_POST["passwort"];

    // überprüfen ob es den Username gibt
    $sql = "select vorname, user_id, passwort from users where BINARY username = '$username'";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) {
        header("Location: https://alpha.kswe.ginf.ch/planer/anmeldung.php?grund=username_gibs_nicht"); // gibts nicht
        exit();
    }

    $row = $result->fetch_assoc();

    // Überprüfen ob das Passwort stimmt
    if (!password_verify($passwort, $row["passwort"])) {
        header("Location: https://alpha.kswe.ginf.ch/planer/anmeldung.php?grund=passwort_falsch");
        exit();
    }

    // User_ID vom Username bekommen
    $user_id = $row["user_id"];

    // wenn man angemeldet bleiben will, für zwei wochen angemeldet lassen
    if ($_POST["angemeldet_bleiben"] == "ja") {
        $zeit = time() + 60 * 60 * 24 * 365;
        setcookie("user_id", $user_id, $zeit, "/", "alpha.kswe.ginf.ch");
    } else {
        setcookie("user_id", $user_id, 0, "/", "alpha.kswe.ginf.ch");
    }

    // für die untere Kondition
    $angemeldet = True;

}

// Schuljahr erstellen
if (isset($_POST["erstellen"])) {

    // Daten bekommen
    $jahr_id = create_jahr_id($conn);
    $schuljahr = $_POST["schuljahr"];
    $schuljahr = str_replace("'", "''", $schuljahr);
    $user_id = $_COOKIE["user_id"];

    $created = time();

    // Daten speichern
    $sql = "insert into schuljahr values('$jahr_id', '$schuljahr', '$user_id', '$created')";
    $res = mysqli_query($conn, $sql);
}

// User-Daten ändern
if (isset($_POST["aendern"])) {
    // Daten bekommen
    $username = $_POST["username"];
    $username = str_replace("'", "''", $username);
    $vorname = $_POST["vorname"];
    $vorname = str_replace("'", "''", $vorname);
    $nachname = $_POST["nachname"];
    $nachname = str_replace("'", "''", $nachname);
    $email = $_POST["email"];
    $email = str_replace("'", "''", $email);
    $passwort = $_POST["passwort"];
    $passwort = str_replace("'", "''", $passwort);
    $passwort_bestätigen = $_POST["passwort_bestätigen"];
    $passwort_bestätigen = str_replace("'", "''", $passwort_bestätigen);


    // überprüfen ob Passwörter übereinstimmen
    if ($passwort != $passwort_bestätigen) {
        wrong_change("passwort", $user_id); // wieder zur Page, aber mit Begründung
    }

    // Überprüfen ob es den Username schon bei einem anderem User gibt
    $sql = "select user_id from users where username = '$username' and BINARY user_id != '$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows != 0) {
        wrong_change("username", $user_id); // wieder zur Page, aber mit Begründung
    }

    // Überprüfen ob es die Email schon bei einem anderen User gibt
    $sql = "select user_id from users where email = '$email' and BINARY user_id != '$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows != 0) {
        wrong_change("email", $user_id); // wieder zur Page, aber mit Begründung
    }

    // Passwort hashen
    $hashed_pass = password_hash($passwort, PASSWORD_DEFAULT);

    // Daten einfügen / ändern
    $sql = "update users set username = '$username', vorname = '$vorname', ";
    $sql .= "nachname = '$nachname', email = '$email', passwort = '$hashed_pass' ";
    $sql .= "where BINARY user_id = '$user_id'";
    $result = $conn->query($sql);

}

if (isset($_POST["loeschen"])) {
    $user_id = $_POST["id"];

    $sql = "delete from noten where BINARY user_id = '$user_id'";
    $result = $conn->query($sql);

    $sql = "delete from faecher where BINARY user_id = '$user_id'";
    $result = $conn->query($sql);

    $sql = "delete from schuljahr where BINARY user_id = '$user_id'";
    $result = $conn->query($sql);

    $sql = "delete from users where BINARY user_id = '$user_id'";
    $result = $conn->query($sql);

    header("Location: http://alpha.kswe.ginf.ch/");
    exit();
}


// Falls man nicht von einer Anmeldung oder Accounterstellung kommt nicht überprüfen ob es den Cookie schon gibt.
if (!$angemeldet) {
    // Wenn von nichts gekommen ist und kein Cookie, zurück zur Anmeldung
    if (!isset($_COOKIE["user_id"])) {
        header("Location: https://alpha.kswe.ginf.ch/planer/anmeldung.php");
        exit();
    } else { // wenn es den Cookie gibt, "anmelden"
        $user_id = $_COOKIE["user_id"];
        $sql = "select vorname, user_id from users where BINARY user_id = '$user_id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
    }
}

?>

    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Schuljahre von <?php
            $sql = "select username, vorname from users where BINARY user_id = '$user_id'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $username = $row["username"];
            $vorname = $row["vorname"];
            echo $username;
            ?></title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/aussehen.css">
        <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/icons/zeugnis.png">
        <script src="https://alpha.kswe.ginf.ch/planer/commands.js"></script>


        <style>
            .test-box {
                width: 100%;
                height: 60px;
                border: 3px solid #29d932;
                padding: 4px;
                margin-top: 2px;
                margin-bottom: 0px;
                padding-left: 10px;
                box-shadow: 0 3px 6px #29d932;
                border-radius: 5px;
                background-color: #29d932;
                position: relative;
                z-index: 10;
            }

            .test-box:after {
                background-color: #FFFFFF;
                content: '';
                display: block;
                position: absolute;
                top: 0px;
                left: 5px;
                right: 0px;
                bottom: 0px;
                z-index: -1;
            }
        </style>


    </head>
    <body style="margin: 0">

<div style="margin: 5px">

    <div>

        <p class="text-top"><strong> Willkommen, <?php echo $vorname; ?> </strong></p>

        <img class="pen" src="/planer/icons/pencil.png" onclick="aendern('user', '<?php echo $user_id; ?>')">



        <form style="float: right;" method="post" action="/planer/erstellen.php">
            <input type="hidden" name="type" id="type" value="Schuljahr">
            <button class="neu-button" type="submit" name="button" id="button">Neues Schuljahr</button>
        </form>
    </div>

    <?php

    $sql = "select schuljahr, jahr_id from schuljahr where binary user_id = '$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows != 0) {


        while ($row = $result->fetch_assoc()) {


            $jahr_id = $row["jahr_id"];

            $sql_fach_select = "select * from faecher where BINARY jahr_id = '$jahr_id' order by fach_titel asc";
            $result_fach_select = $conn->query($sql_fach_select);

            $schnitt_jahr = 0;
            $gewichtung_jahr = 0;

            while ($row_fach_select = $result_fach_select->fetch_assoc()) {


                $sql_schnitt_fach = "select gewichtung, note from noten where BINARY fach_id = '" . $row_fach_select["fach_id"] . "'";
                $result_schnitt_fach = $conn->query($sql_schnitt_fach);

                $schnitt_fach = 0;
                $gewichtung = 0;


                while ($row_schnitt_fach = $result_schnitt_fach->fetch_assoc()) {
                    if ($row_schnitt_fach["note"] != 0) {
                        $schnitt_fach += $row_schnitt_fach["note"] * $row_schnitt_fach["gewichtung"];
                        $gewichtung += $row_schnitt_fach["gewichtung"];
                    }
                }

                $schnitt_fach = $schnitt_fach / $gewichtung;
                $schnitt_fach = round($schnitt_fach, 2);

                $schnitt_fach = round($schnitt_fach * 2) / 2;

                if (!is_nan($schnitt_fach)) {
                    if ($schnitt_fach != 0) {
                        $schnitt_jahr += $schnitt_fach * $row_fach_select["gewichtung"];
                        $gewichtung_jahr += $row_fach_select["gewichtung"];
                    }
                }
            }

            $gesamtsschnitt_jahr = $schnitt_jahr / $gewichtung_jahr;
            $gesamtsschnitt_jahr = round($gesamtsschnitt_jahr, 2);


            if (!is_nan($gesamtsschnitt_jahr)) {
                if ($gesamtsschnitt_jahr >= 5.75) {
                    $div_num = 6;
                    $color = "#3881ff";
                } elseif ($gesamtsschnitt_jahr >= 5.25) {
                    $div_num = 5.5;
                    $color = "#22a7e0";
                } elseif ($gesamtsschnitt_jahr >= 4.75) {
                    $div_num = 5;
                    $color = "#29d932";
                } elseif ($gesamtsschnitt_jahr >= 4.25) {
                    $div_num = 4.5;
                    $color = "#f5b50c";
                } elseif ($gesamtsschnitt_jahr >= 3.75) {
                    $div_num = 4;
                    $color = "#c41818";
                } else {
                    $div_num = 1;
                    $color = "#000000";
                }
            } else {
                $div_num = 1;
                $color = "#000000";
            }

            $style = 'style="border: 3px solid ' . $color . '; ';
            $style .= 'box-shadow: 0 5px 10px ' . $color . '; ';
            $style .= 'background-color: ' . $color . '"';

            $box = '
<div ' . $style . 'class="big-div"
onmouseover="hover(this, ' . $div_num . ')"
onmouseleave="leave(this, ' . $div_num . ')"
onclick="select_schuljahr(' . "'" . $row["jahr_id"] . "'" . ')">
    <span class="big-span">';

            $box = $box . $row["schuljahr"];
            $box = $box . '
</span><span class="noten-span" style="float: right">';


            if (!is_nan($gesamtsschnitt_jahr)) {
                $box = $box . $gesamtsschnitt_jahr;
            }
            $box = $box . '</span><br>
    <div class="small-div">
        <img style="height: 10px" src="/planer/icons/list.png"> ';

            $sql_list = "select count(fach_id) as anzahl from faecher where BINARY jahr_id = '" . $row["jahr_id"] . "'";
            $result_list = $conn->query($sql_list);
            $row_list = $result_list->fetch_assoc();

            $box = $box . $row_list["anzahl"];
            $box = $box . '
    </div>
</div>
<br>';

            echo $box;
        }
    } else {
        echo "<p style='float: right;margin-top: 0px; padding-top: 0px'>
Noch keine Schuljahre eingetragen
<span style='font-size: 50px'>🠕</span></p>";
    }

    ?>


    <form action="/planer/anmeldung.php" method="post">
        <button style="background: red; color: white;" type="submit" name="abmelden" id="abmelden" value="abmelden">
            <strong>Abmelden</strong></button>
    </form>

</div>

<ul>
    <li><a href="/planer/dashboard">Dashboard</a></li>
    <li><a href="/planer/schuljahre" class="list-active">
            <?php
            echo $username;
            ?>
        </a></li>
</ul>

    </body>
    </html>


<?php
mysqli_close($conn);
?>