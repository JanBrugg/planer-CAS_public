<?php

$conn = mysqli_connect($hostname, $username, $password, $database)

$angemeldet = false;

include "../functions.php";

// Falls man von der Anmeldung kommt
if (isset($_POST["anmelden"])) {
    setcookie("jahr_id", "", 1, "/", "alpha.kswe.ginf.ch");
    setcookie("fach_id", "", 1, "/", "alpha.kswe.ginf.ch");


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
        $zeit = time() + 60 * 60 * 24 * 30 * 365;
        setcookie("user_id", $user_id, $zeit, "/", "alpha.kswe.ginf.ch");
    } else {
        setcookie("user_id", $user_id, 0, "/", "alpha.kswe.ginf.ch");
    }

    // für die untere Kondition
    $angemeldet = True;

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

if (isset($_POST["type"])) {
    $id = $_POST["id"];
    $sql = "delete from hausaufgaben where BINARY hausaufgabe_id = '$id'";
    $result = $conn->query($sql);
    header("Location: https://alpha.kswe.ginf.ch/planer/dashboard");
    exit();
}


if (isset($_POST["id"])) {
    $id = $_POST["id"];
    $sql = "select hausaufgabe, datum, status, beschreibung from hausaufgaben where BINARY hausaufgabe_id = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();

    $datum_bearbeiten = $row["datum"];

    $hausaufgabe_bearbeiten = $row["hausaufgabe"];

    $status_bearbeiten = $row["status"];

    $beschreibung_bearbeiten = $row["beschreibung"];
}

if (isset($_POST["speichern"])) {
    $hausaufgabe = $_POST["hausaufgabe"];
    $hausaufgabe = str_replace("'", "''", $hausaufgabe);
    $datum = $_POST["datum"];
    $datum = str_replace("'", "''", $datum);
    $beschreibung = $_POST["beschreibung"];
    $beschreibung = strval($beschreibung);
    $beschreibung = str_replace("'", "''", $beschreibung);
    $id = $_POST["id_bearbeiten"];
    $sql = "update hausaufgaben set hausaufgabe = '$hausaufgabe', datum = '$datum', beschreibung = '$beschreibung' where BINARY hausaufgabe_id = '$id'";
    $result = $conn->query($sql);
    header("Location: https://alpha.kswe.ginf.ch/planer/dashboard");
    exit();
}

if (isset($_POST["erledigt"])) {
    $id = $_POST["id_bearbeiten"];
    $sql = "select status from hausaufgaben where BINARY hausaufgabe_id = '$id'";
    $result = $conn->query($sql);
    if ($result->fetch_assoc()["status"] == "done") {
        $set_update = "working";
    } else {
        $set_update = "done";
    }
    $sql = "update hausaufgaben set status = '$set_update' where BINARY hausaufgabe_id = '$id'";
    $result = $conn->query($sql);
    header("Location: https://alpha.kswe.ginf.ch/planer/dashboard");
    exit();
}

?>

    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard von <?php
            $sql = "select username from users where BINARY user_id = '$user_id'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $username = $row["username"];
            echo $username;

            ?></title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/aussehen.css">
        <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/icons/zeugnis.png">
        <script src="https://alpha.kswe.ginf.ch/planer/commands.js"></script>

        <script>
            function show_checkmark() {
                var text = document.getElementById("hausaufgabe_bearbeiten").innerHTML;
                text += '</h2>';
                text += '<h4 style="text-align: center; padding-top: 0">✓ ✖ •</h4>';
                document.getElementById("hausaufgabe_bearbeiten").outerHTML = '<h2 style="text-align: center; padding-bottom: 0">' + text;
            }
        </script>

        <?php
        style_echo();
        ?>


    </head>
    <body style="margin: 0">

    <div style="margin: 5px">

        <?php

        $today = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));

        $today_day = date("N");
        $start_next = 8 - $today_day;
        $start_next_week = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + $start_next, date("Y")));
        $end_next_week = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + $start_next + 7, date("Y")));

        $sql_mixed = "select * from (select hausaufgabe_id as id,
       hausaufgabe    as name,
       datum,
       hausaufgaben.fach_id,
       hausaufgaben.jahr_id,
       hausaufgaben.user_id,
       status         as status_note,
       beschreibung   as beschreibung_gewichtung,
       fach_titel as fach
from hausaufgaben join faecher on hausaufgaben.fach_id = faecher.fach_id
where status = 'working' and BINARY hausaufgaben.user_id = '$user_id' and datum < '$start_next_week'
union
select note_id,
       note_titel,
       datum,
       noten.fach_id,
       noten.jahr_id,
       noten.user_id,
       note,
       noten.gewichtung,
       fach_titel
from noten join faecher on noten.fach_id = faecher.fach_id
where BINARY noten.user_id = '$user_id' and datum >= '$today' and datum < '$start_next_week' ) dumm order by datum asc";

        $result_mixed = $conn->query($sql_mixed);
        if ($result_mixed->num_rows != 0) {
            echo '<div><p style="margin-bottom: 0; margin-top: 0; padding-bottom: 10px">Diese Woche:</p></div>';
            while ($row = $result_mixed->fetch_assoc()) {
                $earlier = new DateTime($today);
                $later = new DateTime($row["datum"]);

                $abs_diff = $later->diff($earlier, false)->format("%a");

                if ($row["datum"] < $today) {
                    $abs_diff *= -1;
                }

                $color = 0;
                $div_class = "big-div";

                if ($abs_diff == 1) {
                    $tage_nachricht = "1 Tag!";
                    $color = 1;
                    $div_class = "big-div-rainbow";
                } elseif ($abs_diff == 0) {
                    $tage_nachricht = "Heute!";
                    $color = 2;
                    $div_class = "big-div-rainbow-today";
                } elseif ($abs_diff == -1) {
                    $tage_nachricht = "Vor 1 Tag!";
                } elseif ($abs_diff < -1) {
                    $tage_nachricht = "Vor " . -1 * $abs_diff . " Tagen!";
                } elseif ($abs_diff > 1) {
                    $tage_nachricht = $abs_diff . " Tage";
                }

                if (is_numeric($row["status_note"])) {
                    $zahl = 4;
                } else {
                    $zahl = 5.5;
                }

                if ($row["status_note"] == "working" or is_numeric($row["status_note"])) {
                    $display = '<div>';
                    $display .= '<div ';

                    $display .= 'style="border: 3px solid ';
                    if (is_numeric($row["status_note"])) {
                        $display .= '#c41818; ';
                    } else {
                        $display .= '#22a7e0; ';
                    }
                    $display .= 'box-shadow: 0 5px 10px ';
                    if (is_numeric($row["status_note"])) {
                        $display .= '#c41818; ';
                    } else {
                        $display .= '#22a7e0; ';
                    }
                    $display .= 'background-color: ';
                    if (is_numeric($row["status_note"])) {
                        $display .= '#c41818; ';
                    } else {
                        $display .= '#22a7e0; ';
                    }
                    $display .= '" class="' . $div_class . '" onmouseover="hover(this, ' . $zahl . ', ' . $color . ')"';
                    $display .= 'onmouseleave="leave(this, ' . $zahl . ', ' . $color . ')"';
                    if (!is_numeric($row["status_note"])) {
                        $display .= 'onclick="hausaufgaben_bearbeiten(' . "'" . $row["id"] . "'" . ')">';
                    } else {
                        $display .= 'onclick="aendern(' . "'pruefung'" . ', ' . "'" . $row["id"] . "'" . ', ' . "'dashboard'" . ')">';
                    }
                    $display .= '<span class="big-span">' . $row["name"] . '</span>';
                    $display .= '<span class="noten-span" style="float: right">';
                    if ($abs_diff < 1) {
                        $display .= '<strong>' . $tage_nachricht . '</strong>';
                    } else {
                        $display .= $tage_nachricht;
                    }
                    $display .= '</span><br>';
                    $display .= '<div class="small-div">';

                    $datum = explode("-", $row["datum"]);
                    $datum = $datum[2] . "." . $datum[1] . "." . $datum[0];

                    $display .= '<img style="height: 10px" src="/planer/icons/calendar.png"> ' . $datum;
                    $display .= '<span class="small-span"> <img style="height: 10px" src="/planer/icons/book.png"></span> ' . $row["fach"];
                    if (is_numeric($row["status_note"])) {
                        $display .= "<em style='font-size: 10px'> (Test)</em>";
                    } else {
                        $display .= "<em style='font-size: 10px'> (HA)</em>";
                    }
                    $display .= '</div></div></div><br>';


                    echo $display;
                }
            }
        } else {
            echo '<div><p style="color: #787878; margin-bottom: 0; margin-top: 0; padding-bottom: 10px"><em>Diese Woche nichts</em></p></div>';
        }

        $sql_mixed = "select * from (select hausaufgabe_id as id,
       hausaufgabe    as name,
       datum,
       hausaufgaben.fach_id,
       hausaufgaben.jahr_id,
       hausaufgaben.user_id,
       status         as status_note,
       beschreibung   as beschreibung_gewichtung,
       fach_titel as fach
from hausaufgaben join faecher on hausaufgaben.fach_id = faecher.fach_id
where status = 'working' and BINARY hausaufgaben.user_id = '$user_id' and datum >= '$start_next_week' and datum < '$end_next_week'
union
select note_id,
       note_titel,
       datum,
       noten.fach_id,
       noten.jahr_id,
       noten.user_id,
       note,
       noten.gewichtung,
       fach_titel
from noten join faecher on noten.fach_id = faecher.fach_id
where BINARY noten.user_id = '$user_id' and datum >= '$start_next_week' and datum < '$end_next_week' ) dumm order by datum asc ";

        $result_mixed = $conn->query($sql_mixed);
        if ($result_mixed->num_rows != 0) {
            echo '<div><p style="margin-bottom: 0; margin-top: 0; padding-bottom: 10px">Nächste Woche:</p></div>';
            while ($row = $result_mixed->fetch_assoc()) {
                $earlier = new DateTime($today);
                $later = new DateTime($row["datum"]);

                $abs_diff = $later->diff($earlier, false)->format("%a");

                if ($row["datum"] < $today) {
                    $abs_diff *= -1;
                }

                $color = 0;
                $div_class = "big-div";

                if ($abs_diff == 1) {
                    $tage_nachricht = "1 Tag!";
                    $color = 1;
                    $div_class = "big-div-rainbow";
                } elseif ($abs_diff == 0) {
                    $tage_nachricht = "Heute!";
                    $color = 2;
                    $div_class = "big-div-rainbow-today";
                } elseif ($abs_diff == -1) {
                    $tage_nachricht = "Vor 1 Tag!";
                } elseif ($abs_diff < -1) {
                    $tage_nachricht = "Vor " . -1 * $abs_diff . " Tagen!";
                } elseif ($abs_diff > 1) {
                    $tage_nachricht = $abs_diff . " Tage";
                }

                if (is_numeric($row["status_note"])) {
                    $zahl = 4;
                } else {
                    $zahl = 5.5;
                }

                if ($row["status_note"] == "working" or is_numeric($row["status_note"])) {
                    $display = '<div>';
                    $display .= '<div ';

                    $display .= 'style="border: 3px solid ';
                    if (is_numeric($row["status_note"])) {
                        $display .= '#c41818; ';
                    } else {
                        $display .= '#22a7e0; ';
                    }
                    $display .= 'box-shadow: 0 5px 10px ';
                    if (is_numeric($row["status_note"])) {
                        $display .= '#c41818; ';
                    } else {
                        $display .= '#22a7e0; ';
                    }
                    $display .= 'background-color: ';
                    if (is_numeric($row["status_note"])) {
                        $display .= '#c41818; ';
                    } else {
                        $display .= '#22a7e0; ';
                    }
                    $display .= '" class="' . $div_class . '" onmouseover="hover(this, ' . $zahl . ', ' . $color . ')"';
                    $display .= 'onmouseleave="leave(this, ' . $zahl . ', ' . $color . ')"';
                    if (!is_numeric($row["status_note"])) {
                        $display .= 'onclick="hausaufgaben_bearbeiten(' . "'" . $row["id"] . "'" . ')">';
                    } else {
                        $display .= 'onclick="aendern(' . "'pruefung'" . ', ' . "'" . $row["id"] . "'" . ')">';
                    }
                    $display .= '<span class="big-span">' . $row["name"] . '</span>';
                    $display .= '<span class="noten-span" style="float: right">';
                    if ($abs_diff < 1) {
                        $display .= '<strong>' . $tage_nachricht . '</strong>';
                    } else {
                        $display .= $tage_nachricht;
                    }
                    $display .= '</span><br>';
                    $display .= '<div class="small-div">';

                    $datum = explode("-", $row["datum"]);
                    $datum = $datum[2] . "." . $datum[1] . "." . $datum[0];

                    $display .= '<img style="height: 10px" src="/planer/icons/calendar.png"> ' . $datum;
                    $display .= '<span class="small-span"> <img style="height: 10px" src="/planer/icons/book.png"></span> ' . $row["fach"];
                    if (is_numeric($row["status_note"])) {
                        $display .= "<em style='font-size: 10px'> (Test)</em>";
                    } else {
                        $display .= "<em style='font-size: 10px'> (HA)</em>";
                    }
                    $display .= '</div></div></div><br>';


                    echo $display;
                }
            }
        }
        /*
         select hausaufgabe_id as id,
           hausaufgabe    as name,
           datum,
           hausaufgaben.fach_id,
           hausaufgaben.jahr_id,
           hausaufgaben.user_id,
           status         as status_note,
           beschreibung   as beschreibung_gewichtung,
           fach_titel
    from hausaufgaben join faecher on hausaufgaben.fach_id = faecher.fach_id
    where status = 'working' and BINARY hausaufgaben.user_id = 'TooCoolForSchool' and datum < '2021-10-11'
    union
    select note_id,
           note_titel,
           datum,
           noten.fach_id,
           noten.jahr_id,
           noten.user_id,
           note,
           noten.gewichtung,
           fach_titel
    from noten join faecher on noten.fach_id = faecher.fach_id
    where BINARY noten.user_id = 'TooCoolForSchool' and datum >= '2021-10-09' and datum < '2021-10-11'
         */
        ?>


        <br><br><br><br><br><br><br>
        <hr>

        <strong>Diese Woche: </strong> <br>
        <?php
        $today = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));

        $today_day = date("N");
        $start_next = 8 - $today_day;
        $start_next_week = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + $start_next, date("Y")));
        $end_next_week = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + $start_next + 7, date("Y")));

        $sql_up_next = "select note_titel, datum from noten where BINARY user_id = '$user_id' and datum >= '$today' and datum < '$start_next_week' order by datum ASC ";
        $result_up_next = $conn->query($sql_up_next);
        if ($result_up_next->num_rows != 0) {

            while ($row_up_next = $result_up_next->fetch_assoc()) {
                $datum = explode("-", $row_up_next["datum"]);
                $datum = $datum[2] . "." . $datum[1] . "." . $datum[0];
                $text = $datum . ": <em>" . $row_up_next["note_titel"] . "</em><br>";
                echo $text;
            }
        } else {
            echo "<span style='color: #787878'><em>Keine Prüfung!</em></span><br>";
        }

        ?>
        <strong>Nächste Woche:</strong> <br>
        <?php
        $sql_fortnight = "select note_titel, datum from noten where BINARY user_id = '$user_id' and datum >= '$start_next_week' and datum < '$end_next_week' order by datum ASC ";
        $result_next_week = $conn->query($sql_fortnight);
        if ($result_next_week->num_rows != 0) {
            while ($row_next_week = $result_next_week->fetch_assoc()) {

                $datum = explode("-", $row_next_week["datum"]);
                $datum = $datum[2] . "." . $datum[1] . "." . $datum[0];
                $text = $datum . ": <em>" . $row_next_week["note_titel"] . "</em><br>";
                echo $text;
            }
        } else {
            echo "<span style='color: #787878'><em>Keine Prüfung!</em></span><br>";
        }


        /*
        $today = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d"), date("Y")));

            $today_day = date("N");
            $start_next = 8 - $today_day;
            $start_next_week = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + $start_next, date("Y")));
            $end_next_week = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") + $start_next + 7, date("Y")));


            if ($_GET["erledigt_button"] == "anzeigen") {
                $sql_up_next = "select fach_id, hausaufgabe_id, hausaufgabe, datum, status, beschreibung from hausaufgaben where BINARY user_id = '$user_id' and datum < '$start_next_week' order by datum asc";
            } else {
                $sql_up_next = "select fach_id, hausaufgabe_id, hausaufgabe, datum, status, beschreibung from hausaufgaben where status = 'working' and BINARY user_id = '$user_id' and datum < '$start_next_week' order by datum asc";
            }
            $result_up_next = $conn->query($sql_up_next);
            if ($result_up_next->num_rows != 0) {
                echo '<div><p style="margin-bottom: 0; margin-top: 0; padding-bottom: 10px">Hausaufgaben diese Woche:</p></div>';

                while ($row = $result_up_next->fetch_assoc()) {
                    $earlier = new DateTime($today);
                    $later = new DateTime($row["datum"]);

                    $abs_diff = $later->diff($earlier, false)->format("%a");

                    if ($row["datum"] < $today) {
                        $abs_diff *= -1;
                    }
                    $fach_id = $row["fach_id"];

                    $sql = "select fach_titel from faecher where BINARY fach_id = '$fach_id'";
                    $result = $conn->query($sql);
                    $fach_titel = $result->fetch_assoc()["fach_titel"];

                    $color = 0;
                    $div_class = "big-div";

                    if ($abs_diff == 1) {
                        $tage_nachricht = "1 Tag!";
                        $color = 1;
                        $div_class = "big-div-rainbow";
                    } elseif ($abs_diff == 0) {
                        $tage_nachricht = "Heute!";
                        $color = 2;
                        $div_class = "big-div-rainbow-today";
                    } elseif ($abs_diff == -1) {
                        $tage_nachricht = "Vor 1 Tag!";
                    } elseif ($abs_diff < -1) {
                        $tage_nachricht = "Vor " . -1 * $abs_diff . " Tagen!";
                    } elseif ($abs_diff > 1) {
                        $tage_nachricht = $abs_diff . " Tage";
                    }


                    $display = '<div';
                    if ($row["status"] == "done") {
                        $display .= ' style="opacity: 0.3;">';
                    } else {
                        $display .= '>';
                    }
                    $display .= '
            <div title="' . $row["beschreibung"] . '" ' . $style . ' class="' . $div_class . '" onmouseover="hover(this, 5.5, ' . $color . ')"';
                    $display .= 'onmouseleave="leave(this, 5.5, ' . $color . ')"';
                    $display .= 'onclick="hausaufgaben_bearbeiten(' . "'" . $row["hausaufgabe_id"] . "'" . ')">';
                    $display .= '<span class="big-span">' . $row["hausaufgabe"] . '</span>';
                    $display .= '<span class="noten-span" style="float: right">';
                    if ($abs_diff < 1) {
                        $display .= '<strong>' . $tage_nachricht . '</strong>';
                    } else {
                        $display .= $tage_nachricht;
                    }
                    $display .= '</span><br>';
                    $display .= '<div class="small-div">';

                    $datum = explode("-", $row["datum"]);
                    $datum = $datum[2] . "." . $datum[1] . "." . $datum[0];

                    $display .= '<img style="height: 10px" src="/planer/icons/calendar.png"> ' . $datum;
                    $display .= '<span class="small-span"> <img style="height: 10px" src="/planer/icons/book.png"></span> ' . $fach_titel;
                    $display .= '</div></div></div><br>';


                    echo $display;
                }
            } else {
                echo '<div><p style="margin-bottom: 0; margin-top: 0; padding-bottom: 10px; color: #787878"><em>Keine Hausaufgaben diese Woche</em></p></div><hr>';
            }
         */
        ?>
        <a href="/planer/hausaufgaben">Zu den hausaufgaben</a>
        <br>
        <br>
        <br>

        <div id="bearbeiten" class="overlay">
            <div class="popup">
                <h2 style="text-align: center; cursor: pointer" onclick="show_checkmark()" id="hausaufgabe_bearbeiten">Hausaufgabe bearbeiten</h2>
                <img style="height: 30px; display: inline; float: right; position: absolute; top: 23px; right: 55px; cursor: pointer;" src="/planer/icons/trash.png" onclick="hausaufgabe_loeschen('<?php echo $id;?>')">
                <a class="close" href="#">&times;</a>
                <div class="content">
                    <form method="post" action="#">

                        <label for="hausaufgabe">Hausaufgabe:</label><br>
                        <input type="text" id="hausaufgabe" name="hausaufgabe" required
                               value="<?php echo $hausaufgabe_bearbeiten; ?>"> <br>

                        <label for="datum">Datum:</label><br>
                        <input type="date" id="datum" name="datum" required value="<?php echo $datum_bearbeiten; ?>">
                        <br>

                        <label for="beschreibung">Notizen:</label><br>
                        <textarea style="resize: vertical; height: 250px" type="text" id="beschreibung"
                                  name="beschreibung"><?php echo $beschreibung_bearbeiten; ?></textarea><br>

                        <button style="float: left" type="submit" name="speichern" id="speichern">Speichern</button>
                        <button style="float: right; background: #2ecc40" type="submit" name="erledigt" id="erledigt">
                            <?php
                            if ($status_bearbeiten == "working") {
                                echo "Erledigt";
                            } else {
                                echo "In Bearbeitung";
                            }
                            ?></button>
                        <input type="hidden" id="id_bearbeiten" name="id_bearbeiten" value="<?php echo $id; ?>">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <ul>
        <li><a href="/planer/dashboard" class="list-active">Dashboard</a></li>
        <li><a href="/planer/hausaufgaben">Hausaufgaben</a></li>
        <li><a href="/planer/kalender">Kalender</a></li>
        <li><a href="/planer/schuljahre">
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