<?php
$conn = mysqli_connect($hostname, $username, $password, $database)

include "../functions.php";

if (!isset($_COOKIE["user_id"])) {
    header("Location: https://alpha.kswe.ginf.ch/planer/anmeldung.php");
    exit();
} else { // wenn es den Cookie gibt, "anmelden"
    $user_id = $_COOKIE["user_id"];
}

if (isset($_POST["type"])) {
    $id = $_POST["id"];
    $sql = "delete from hausaufgaben where BINARY hausaufgabe_id = '$id'";
    $result = $conn->query($sql);
    header("Location: https://alpha.kswe.ginf.ch/planer/hausaufgaben");
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

if (isset($_POST["hausaufgabe_erstellen"])) {
    $hausaufgabe_id = create_hausaufgabe_id($conn);
    $hausaufgabe = $_POST["hausaufgabe"];
    $hausaufgabe = str_replace("'", "''", $hausaufgabe);
    $datum = $_POST["datum"];
    $beschreibung = $_POST["beschreibung"];
    $beschreibung = str_replace("'", "''", $beschreibung);
    $fach_id = $_POST["fach"];
    $jahr_id = $_POST["jahr"];

    $sql = "insert into hausaufgaben values('$hausaufgabe_id', '$hausaufgabe', '$datum', 'working', '$beschreibung', '$fach_id', '$jahr_id', '$user_id')";
    $result = $conn->query($sql);

    header("Location: https://alpha.kswe.ginf.ch/planer/hausaufgaben");
    exit();

}

if (isset($_POST["speichern"])) {
    $hausaufgabe = $_POST["hausaufgabe"];
    $hausaufgabe = str_replace("'", "''", $hausaufgabe);
    $datum = $_POST["datum"];
    $beschreibung = $_POST["beschreibung"];
    $beschreibung = strval($beschreibung);
    $beschreibung = str_replace("'", "''", $beschreibung);
    $id = $_POST["id_bearbeiten"];
    $sql = "update hausaufgaben set hausaufgabe = '$hausaufgabe', datum = '$datum', beschreibung = '$beschreibung' where BINARY hausaufgabe_id = '$id'";
    $result = $conn->query($sql);
    header("Location: https://alpha.kswe.ginf.ch/planer/hausaufgaben");
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
    header("Location: https://alpha.kswe.ginf.ch/planer/hausaufgaben");
    exit();
}
?>


    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Hausaufgaben von <?php
            $sql_username = "select username from users where BINARY user_id = '$user_id'";
            $result_username = $conn->query($sql_username);
            $username = $result_username->fetch_assoc()["username"];
            echo $username;
            ?></title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/aussehen.css">
        <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/icons/zeugnis.png">
        <script src="https://alpha.kswe.ginf.ch/planer/commands.js"></script>

        <?php style_echo(); ?>

    </head>
    <body style="margin: 0">

    <div style="margin: 5px">

        <div>
            <p class="text-top" style="margin-bottom: 10px"><strong>Hausaufgaben von <?php echo $username; ?></strong>
            </p>

            <form style="float: right" method="post" action="#jahr_auswählen">
                <button class="neu-button" type="submit" name="button" id="button">Hausaufgabe eintragen</button>
            </form>
        </div>

        <?php

        $style = 'style="border: 3px solid #22a7e0; ';
        $style .= 'box-shadow: 0 5px 10px #22a7e0; ';
        $style .= 'background-color: #22a7e0"';


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
            echo '<div><p style="margin-bottom: 0; margin-top: 0; padding-bottom: 10px">Hausaufgaben diese Woche (' . $result_up_next->num_rows . '):</p></div>';
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

        if ($_GET["erledigt_button"] == "anzeigen") {
            $sql_fortnight = "select fach_id, hausaufgabe_id, hausaufgabe, datum, status, beschreibung from hausaufgaben where BINARY user_id = '$user_id' and datum >= '$start_next_week' and datum < '$end_next_week' order by datum asc";
        } else {
            $sql_fortnight = "select fach_id, hausaufgabe_id, hausaufgabe, datum, status, beschreibung from hausaufgaben where status = 'working' and BINARY user_id = '$user_id' and datum >= '$start_next_week' and datum < '$end_next_week' order by datum asc";
        }
        $result_fortnight = $conn->query($sql_fortnight);
        if ($result_fortnight->num_rows != 0) {
            echo '<div><p style="margin-bottom: 0; margin-top: 0; margin-top: 10px; padding-bottom: 10px">Hausaufgaben nächste Woche (' . $result_fortnight->num_rows . '):</p></div>';
            while ($row = $result_fortnight->fetch_assoc()) {
                $earlier = new DateTime($today);
                $later = new DateTime($row["datum"]);

                $abs_diff = $later->diff($earlier, false)->format("%a");

                $fach_id = $row["fach_id"];

                $sql = "select fach_titel from faecher where BINARY fach_id = '$fach_id'";
                $result = $conn->query($sql);
                $fach_titel = $result->fetch_assoc()["fach_titel"];

                $display = '<div';
                if ($row["status"] == "done") {
                    $display .= ' style="opacity: 0.3;">';
                } else {
                    $display .= '>';
                }
                $display .= '
        <div title="' . $row["beschreibung"] . '" ' . $style . ' class="big-div" onmouseover="hover(this, 5.5)"';
                $display .= 'onmouseleave="leave(this, 5.5)"';
                $display .= 'onclick="hausaufgaben_bearbeiten(' . "'" . $row["hausaufgabe_id"] . "'" . ')">';
                $display .= '<span class="big-span">' . $row["hausaufgabe"] . '</span>';
                $display .= '<span class="noten-span" style="float: right">';
                $display .= $abs_diff . ' Tage';
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
            echo '<div><p style="margin-bottom: 0; margin-top: 0; padding-bottom: 10px; color: #787878"><em>Keine Hausaufgaben nächste Woche</em></p></div>';
        }

        if ($_GET["erledigt_button"] == "anzeigen") {
            $sql_later = "select fach_id, hausaufgabe_id, hausaufgabe, datum, status, beschreibung from hausaufgaben where BINARY user_id = '$user_id' and datum >= '$end_next_week' order by datum asc";
        } else {
            $sql_later = "select fach_id, hausaufgabe_id, hausaufgabe, datum, status, beschreibung from hausaufgaben where status = 'working' and BINARY user_id = '$user_id' and datum >= '$end_next_week' order by datum asc";
        }
        $result_later = $conn->query($sql_later);
        if ($result_later->num_rows != 0) {
            echo '<div><p style="margin-bottom: 0; margin-top: 0; margin-top: 10px; padding-bottom: 10px">Hausaufgaben irgendwann (' . $result_later->num_rows . '):</p></div>';
            while ($row = $result_later->fetch_assoc()) {
                $earlier = new DateTime($today);
                $later = new DateTime($row["datum"]);

                $abs_diff = $later->diff($earlier, false)->format("%a");

                $fach_id = $row["fach_id"];

                $sql = "select fach_titel from faecher where BINARY fach_id = '$fach_id'";
                $result = $conn->query($sql);
                $fach_titel = $result->fetch_assoc()["fach_titel"];

                $display = '<div';
                if ($row["status"] == "done") {
                    $display .= ' style="opacity: 0.3;">';
                } else {
                    $display .= '>';
                }
                $display .= '
        <div title="' . $row["beschreibung"] . '" ' . $style . ' class="big-div" onmouseover="hover(this, 5.5)"';
                $display .= 'onmouseleave="leave(this, 5.5)"';
                $display .= 'onclick="hausaufgaben_bearbeiten(' . "'" . $row["hausaufgabe_id"] . "'" . ')">';
                $display .= '<span class="big-span">' . $row["hausaufgabe"] . '</span>';
                $display .= '<span class="noten-span" style="float: right">';
                $display .= $abs_diff . ' Tage';
                $display .= '</span><br>';
                $display .= '<div class="small-div">';

                $datum = explode("-", $row["datum"]);
                $datum = $datum[2] . "." . $datum[1] . "." . $datum[0];

                $display .= '<img style="height: 10px" src="/planer/icons/calendar.png"> ' . $datum;
                $display .= '<span class="small-span"> <img style="height: 10px" src="/planer/icons/book.png"></span> ' . $fach_titel;
                $display .= '</div></div></div><br>';


                echo $display;
            }
        }

        ?>


        <div id="bearbeiten" class="overlay">
            <div class="popup">
                <h2 style="text-align: center">Hausaufgabe bearbeiten</h2>
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


        <div id="jahr_auswählen" class="overlay">
            <div class="popup">
                <h2 style="text-align: center">Hausaufgabe erstellen</h2>
                <a class="close" href="#">&times;</a>
                <div class="content">
                    <form method="post" action="#erstellen">

                        <label for="schuljahr">Schuljahr:</label><br>
                        <select name="schuljahr" id="schuljahr">
                            <?php
                            $sql = "select jahr_id, schuljahr from schuljahr where BINARY user_id = '$user_id' order by created desc";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                $jahr_id = $row["jahr_id"];
                                $schuljahr = $row["schuljahr"];

                                echo '<option value="' . $jahr_id . '">' . $schuljahr . '</option>';
                            }
                            ?>
                        </select>

                        <button style="width: 100%" type="submit" name="weiter">Weiter ></button>

                    </form>
                </div>
            </div>
        </div>

        <div id="erstellen" class="overlay">
            <div class="popup">
                <h2 style="text-align: center; padding-bottom: 0">Hausaufgabe erstellen</h2>
                <h4 style="text-align: center; padding-top: 0"><em><?php
                        $sql = "select schuljahr from schuljahr where BINARY jahr_id = '" . $_POST["schuljahr"] . "'";
                        echo $conn->query($sql)->fetch_assoc()["schuljahr"];
                        ?></em></h4>
                <a class="close" href="#">&times;</a>
                <div class="content">
                    <form method="post" action="#">

                        <label for="fach">Fach:</label><br>
                        <select name="fach" id="fach">
                            <?php
                            $sql = "select fach_titel, fach_id from faecher where BINARY jahr_id = '" . $_POST["schuljahr"] . "' order by fach_titel asc";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()) {
                                echo '<option value="' . $row["fach_id"] . '">' . $row["fach_titel"] . '</option>';
                            }
                            ?>
                        </select>

                        <label for="hausaufgabe">Hausaufgabe:</label><br>
                        <input type="text" id="hausaufgabe" name="hausaufgabe" required> <br>

                        <label for="datum">Datum:</label><br>
                        <input type="date" id="datum" name="datum" required value="<?php
                        $tag = date("d");
                        $monat = date("m");
                        $jahr = date("Y");
                        $today = $jahr . "-" . $monat . "-" . $tag;
                        echo $today;
                        ?>"> <br>

                        <label for="beschreibung">Notizen:</label><br>
                        <textarea style="resize: vertical; height: 250px;" type="text" id="beschreibung" name="beschreibung"></textarea><br>

                        <input type="hidden" name="jahr" id="jahr" value="<?php echo $_POST["schuljahr"]; ?>">
                        <button style="width: 100%" type="submit" name="hausaufgabe_erstellen"
                                id="hausaufgabe_erstellen">Erstellen
                        </button>
                    </form>
                </div>
            </div>
        </div>


        <form method="get" action="index.php">
            <button style="background: #787878" type="submit" name="erledigt_button" id="erledigt_button" value="<?php

            if ($_GET["erledigt_button"] == "anzeigen") {
                echo "nicht_anzeigen";
            } else {
                echo "anzeigen";
            }

            ?>"> <?php
                if ($_GET["erledigt_button"] == "anzeigen") {
                    echo "Erledigte ausblenden";
                } else {
                    echo "Erledigte einblenden";
                }
                ?></button>
        </form>
    </div>

    <br>
    <br>
    <br>


    <ul>
        <li><a href="/planer/dashboard">Dashboard</a></li>
        <li><a href="/planer/hausaufgaben" class="list-active">Hausaufgaben</a></li>
        <li><a href="/planer/kalender">Kalender</a></li>
        <li><a href="/planer/schuljahre">
                <?php
                $sql = "select username from users where BINARY user_id = '$user_id'";
                $result = $conn->query($sql);
                $username = $result->fetch_assoc()["username"];
                echo $username;
                ?>
            </a></li>
    </ul>

    <!--

    <div class="big-div" onmouseover="hover(this, '5.5')" onmouseleave="leave(this, '5.5')" onclick="hausaufgabe_bearbeiten()">
    <span class="big-span">Test</span>
    <span class="noten-span" style="float: right">Note bzw anzahl Tage</span>
    <br>
    <div class="small-div">
    <img style="height: 10px" src="/planer/icons/calendar.png"> datum
    <span class="small-span"> <img style="height: 10px" src="/planer/icons/weight.png"></span> Fach
    </div>
    </div>


    -->

    </body>
    </html>

<?php
mysqli_close($conn);
?>