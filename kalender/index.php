<?php
$conn = mysqli_connect($hostname, $username, $password, $database)

include "../functions.php";

if (!isset($_COOKIE["user_id"])) {
    header("Location: https://alpha.kswe.ginf.ch/planer/anmeldung.php");
    exit();
} else { // wenn es den Cookie gibt, "anmelden"
    $user_id = $_COOKIE["user_id"];
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Kalender von <?php
        $sql = "select username from users where BINARY user_id = '$user_id'";
        echo $conn->query($sql)->fetch_assoc()["username"];
        ?></title>
    <meta charset="UTF-8"/>
</head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/kalender/styles.css">
<link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/icons/zeugnis.png">

<body style="margin: 0">

<div style="margin: 5px">

    <div class="calendar-month">
        <!-- Top header Part -->
        <section class="calendar-month-header">
            <div id="selected-month" class="calendar-month-header-selected-month">

                <?php
                if (isset($_GET["monate_differenz"])) {
                    $monate_differenz = $_GET["monate_differenz"];
                } else {
                    $monate_differenz = 0;
                }

                $monat_vorraus = $monate_differenz - 1;
                $monat_danach = $monate_differenz + 1;


                $today = date("F", mktime(0, 0, 0, date("m") + $monate_differenz, date("d"), date("Y")));
                switch ($today) {
                    case "January":
                        $today = "Januar";
                        break;
                    case "February":
                        $today = "Februar";
                        break;
                    case "March":
                        $today = "März";
                        break;
                    case "April":
                        $today = "April";
                        break;
                    case "May":
                        $today = "Mai";
                        break;
                    case "June":
                        $today = "Juni";
                        break;
                    case "July":
                        $today = "Juli";
                        break;
                    case "August":
                        $today = "August";
                        break;
                    case "September":
                        $today = "September";
                        break;
                    case "October":
                        $today = "Oktober";
                        break;
                    case "November":
                        $today = "November";
                        break;
                    case "December":
                        $today = "Dezember";
                        break;
                }

                $jahr = date("Y", mktime(0, 0, 0, date("m") + $monate_differenz, date("d"), date("Y")));

                echo $today . " " . $jahr;
                ?>

            </div>

            <div class="calendar-month-header-selectors">
                <span id="previous-month-selector">
                    <a href="index.php?monate_differenz=<?php echo $monat_vorraus; ?>&hausaufgaben=<?php
                    if ($_GET["hausaufgaben"] == "anzeigen") {
                        echo "anzeigen";
                    } else {
                        echo "ausblenden";
                    }
                    ?>" style="text-decoration: none; color: #000000"><</a> </span>
                <span id="present-month-selector">
                    <a href="index.php?monate_differenz=0&hausaufgaben=<?php
                    if ($_GET["hausaufgaben"] == "anzeigen") {
                        echo "anzeigen";
                    } else {
                        echo "ausblenden";
                    }
                    ?>" style="text-decoration: none; color: #000000">Heute</a></span>
                <span id="next-month-selector">
                    <a href="index.php?monate_differenz=<?php echo $monat_danach; ?>&hausaufgaben=<?php
                    if ($_GET["hausaufgaben"] == "anzeigen") {
                        echo "anzeigen";
                    } else {
                        echo "ausblenden";
                    }
                    ?>" style="text-decoration: none; color: #000000">></a> </span>
            </div>
        </section>

        <!-- Weekdays -->
        <ol id="days-of-week" class="day-of-week">
            <li>Mo</li>
            <li>Di</li>
            <li>Mi</li>
            <li>Do</li>
            <li>Fr</li>
            <li>Sa</li>
            <li>So</li>
        </ol>

        <ol id="calendar-days" class="days-grid">

            <?php

            // VORHERIGER MONAT
            $ende_letster_tag_vorheriger_monat = date("d", mktime(0, 0, 0, date("m") + $monate_differenz, 0, date("Y")));

            $letster_tag_vorheriger_monat = date("N", mktime(0, 0, 0, date("m") + $monate_differenz, 1, date("Y")));
            $anzahl_tage_im_vorherigen_monat = $letster_tag_vorheriger_monat - 1;
            $letster_tag_vorheriger_monat = $ende_letster_tag_vorheriger_monat - $anzahl_tage_im_vorherigen_monat + 1;
            $vorheriger_monat = date("m", mktime(0, 0, 0, date("m") - 1 + $monate_differenz, date("d"), date("Y")));
            $vorheriger_monat_jahr = date("Y", mktime(0, 0, 0, date("m") - 1 + $monate_differenz, date("d"), date("Y")));

            for ($letster_tag_vorheriger_monat; $letster_tag_vorheriger_monat <= $ende_letster_tag_vorheriger_monat; $letster_tag_vorheriger_monat++) {
                $box = '<li class="calendar-day calendar-day calendar-day--not-current">';
                $box .= '<span>' . $letster_tag_vorheriger_monat . '</span><br>';
                echo $box;

                $datum = $vorheriger_monat_jahr . "-" . $vorheriger_monat . "-" . $letster_tag_vorheriger_monat;

                $sql = "select note_titel, fach_id from noten where BINARY user_id = '$user_id' and datum = '$datum'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $sql_fach = "select fach_titel from faecher where BINARY fach_id = '" . $row["fach_id"] . "'";
                    $result_fach = $conn->query($sql_fach);
                    $row_fach = $result_fach->fetch_assoc();

                    kalender_eintrag($row["note_titel"], $row_fach["fach_titel"], "prüfung");
                }

                if ($_GET["hausaufgaben"] == "anzeigen") {
                    $sql = "select hausaufgabe, fach_id from hausaufgaben where BINARY user_id = '$user_id' and datum = '$datum'";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        $sql_fach = "select fach_titel from faecher where BINARY fach_id = '" . $row["fach_id"] . "'";
                        $result_fach = $conn->query($sql_fach);
                        $row_fach = $result_fach->fetch_assoc();
                        kalender_eintrag($row["hausaufgabe"], $row_fach["fach_titel"], "hausaufgabe");
                    }
                }
            }
            echo "</li>";


            // JETZIGER MONAT
            $anzahl_tage = date("d", mktime(0, 0, 0, date("m") + 1 + $monate_differenz, 0, date("Y")));
            $monat = date("m", mktime(0, 0, 0, date("m") + $monate_differenz, date("d"), date("Y")));
            $jahr = date("Y", mktime(0, 0, 0, date("m") + $monate_differenz, date("d"), date("Y")));

            for ($tag_im_monat = 1; $tag_im_monat <= $anzahl_tage; $tag_im_monat++) {
                if (date("d", mktime(0, 0, 0, date("m") + $monate_differenz, date("d"), date("Y"))) == $tag_im_monat) {
                    if ($monate_differenz == 0) {
                        $box = '<li class="calendar-day calendar-day--today">';
                    } else {
                        $box = '<li class="calendar-day">';
                    }
                } else {
                    $box = '<li class="calendar-day">';
                }
                $box .= '<span>' . $tag_im_monat . '</span><br>';
                echo $box;

                if ($tag_im_monat < 10) {
                    $tag_im_monat = "0" . $tag_im_monat;
                }
                $datum = $jahr . "-" . $monat . "-" . $tag_im_monat;
                $sql = "select note_titel, fach_id from noten where BINARY user_id = '$user_id' and datum = '$datum'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $sql_fach = "select fach_titel from faecher where BINARY fach_id = '" . $row["fach_id"] . "'";
                    $result_fach = $conn->query($sql_fach);
                    $row_fach = $result_fach->fetch_assoc();

                    kalender_eintrag($row["note_titel"], $row_fach["fach_titel"], "prüfung");

                }

                if ($_GET["hausaufgaben"] == "anzeigen") {
                    $sql = "select hausaufgabe, fach_id from hausaufgaben where BINARY user_id = '$user_id' and datum = '$datum'";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        $sql_fach = "select fach_titel from faecher where BINARY fach_id = '" . $row["fach_id"] . "'";
                        $result_fach = $conn->query($sql_fach);
                        $row_fach = $result_fach->fetch_assoc();

                        kalender_eintrag($row["hausaufgabe"], $row_fach["fach_titel"], "hausaufgabe");

                    }
                }
            }
            echo "</li>";


            // NÄCHSTER MONAT
            $ende_tag_im_neuen_monat = 7 - date("N", mktime(0, 0, 0, date("m") + 1 + $monate_differenz, 0, date("Y")));

            $nächster_monat = date("m", mktime(0, 0, 0, date("m") + 1 + $monate_differenz, 1, date("Y")));
            $nächster_monat_jahr = date("Y", mktime(0, 0, 0, date("m") + 1 + $monate_differenz, 1, date("Y")));

            for ($tag_im_neuen_monat = 1; $tag_im_neuen_monat <= $ende_tag_im_neuen_monat; $tag_im_neuen_monat++) {
                $box = '<li class="calendar-day calendar-day calendar-day--not-current">';
                $box .= '<span>' . $tag_im_neuen_monat . '</span><br>';
                echo $box;

                if ($tag_im_neuen_monat < 10) {
                    $tag_im_neuen_monat = "0" . $tag_im_neuen_monat;
                }

                $datum = $nächster_monat_jahr . "-" . $nächster_monat . "-" . $tag_im_neuen_monat;
                $sql = "select note_titel, fach_id from noten where BINARY user_id = '$user_id' and datum = '$datum'";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $sql_fach = "select fach_titel from faecher where BINARY fach_id = '" . $row["fach_id"] . "'";
                    $result_fach = $conn->query($sql_fach);
                    $row_fach = $result_fach->fetch_assoc();

                    kalender_eintrag($row["note_titel"], $row_fach["fach_titel"], "prüfung");

                }

                if ($_GET["hausaufgaben"] == "anzeigen") {
                    $sql = "select hausaufgabe, fach_id from hausaufgaben where BINARY user_id = '$user_id' and datum = '$datum'";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()) {
                        $sql_fach = "select fach_titel from faecher where BINARY fach_id = '" . $row["fach_id"] . "'";
                        $result_fach = $conn->query($sql_fach);
                        $row_fach = $result_fach->fetch_assoc();

                        kalender_eintrag($row["hausaufgabe"], $row_fach["fach_titel"], "hausaufgabe");

                    }
                }
            }
            echo "</li>";

            ?>


            <!-- Example of the three types
                Normal day:
                <li class="calendar-day">
                <span>29</span>
                </li>

                Nicht dieser Monat:
                <li class="calendar-day calendar-day calendar-day--not-current">
                <span>29</span>
                </li>

                Heute:
                <li class="calendar-day calendar-day--today">
                <span>29</span>
                </li>
                -->
        </ol>
    </div>

    <p id="hausaufgaben_anzeige_button"> <a
                href="index.php?monate_differenz=<?php echo $monate_differenz; ?>&hausaufgaben=<?php
                if ($_GET["hausaufgaben"] == "anzeigen") {
                    echo "ausblenden";
                } else {
                    echo "anzeigen";
                }
                ?>">Hausaufgaben <?php
            if ($_GET["hausaufgaben"] == "anzeigen") {
                echo "ausblenden";
            } else {
                echo "anzeigen";
            }
            ?></a></p>
<br><br>
</div>

<ul>
    <li><a href="/planer/dashboard">Dashboard</a></li>
    <li><a href="/planer/hausaufgaben">Hausaufgaben</a></li>
    <li><a href="/planer/kalender" class="list-active">Kalender</a></li>
    <li><a href="/planer/schuljahre">
            <?php
            $sql = "select username from users where BINARY user_id = '$user_id'";
            $result = $conn->query($sql);
            $username = $result->fetch_assoc()["username"];
            echo $username;
            ?>
        </a></li>
</ul>

</body>
</html>