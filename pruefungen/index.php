<?php

$conn = mysqli_connect($hostname, $username, $password, $database) // mit DB verbinden

include "../functions.php";

if (isset($_POST["aendern"])) {

    switch ($_POST["type"]) {
        case "Prüfung":
            $note_titel = $_POST["pruefung"];
            $note_titel = str_replace("'", "''", $note_titel);
            $note = $_POST["note"];
            $gewichtung = $_POST["gewichtung"];
            $datum = $_POST["datum"];
            $note_id = $_POST["id"];

            $sql = "update noten set note_titel = '$note_titel', note = $note, gewichtung = $gewichtung, datum = '$datum' where BINARY note_id = '$note_id'";
            $result = $conn->query($sql);

            if ($_POST["where"] == "dashboard") {
                header("Location: https://alpha.kswe.ginf.ch/planer/dashboard/");
                exit();
            }
            break;
        case "Fach":
            $fach_titel = $_POST["fach"];
            $fach_titel = str_replace("'", "''", $fach_titel);
            $gewichtung = $_POST["gewichtung"];
            $fach_id = $_POST["id"];

            $sql = "update faecher set fach_titel = '$fach_titel', gewichtung = $gewichtung where BINARY fach_id = '$fach_id'";
            $result = $conn->query($sql);
            break;
    }
}

if (isset($_POST["loeschen"])) {
    switch ($_POST["type"]) {
        case "Prüfung":
            $note_id = $_POST["id"];

            $sql = "delete from noten where BINARY note_id = '$note_id'";
            $result = $conn->query($sql);

            header("Location: https://alpha.kswe.ginf.ch/planer/pruefungen/");
            exit();
        case "Fach":
            $fach_id = $_POST["id"];

            $sql = "delete from noten where BINARY fach_id = '$fach_id'";
            $result = $conn->query($sql);

            $sql = "delete from faecher where BINARY fach_id = '$fach_id'";
            $result = $conn->query($sql);

            header("Location: https://alpha.kswe.ginf.ch/planer/faecher/");
            exit();
    }
}

if (!isset($_COOKIE["fach_id"]) and !isset($_POST["fach"])) {
    header("Location: https://alpha.kswe.ginf.ch");
    exit();
}


if (isset($_COOKIE["fach_id"])) {
    $fach_id = $_COOKIE["fach_id"];
} else {
    $fach_id = $_POST["fach"];
}

setcookie("fach_id", $fach_id, 0, "/", "alpha.kswe.ginf.ch");

if (isset($_POST["erstellen"])) {

    $note_id = create_note_id($conn);
    $note_titel = $_POST["pruefung"];
    $note_titel = str_replace("'", "''", $note_titel);
    $note = $_POST["note"];
    $gewichtung = $_POST["gewichtung"];
    $datum = $_POST["datum"];
    $fach_id = $_COOKIE["fach_id"];
    $jahr_id = $_COOKIE["jahr_id"];
    $user_id = $_COOKIE["user_id"];

    $sql = "insert into noten values 
    ('$note_id', '$note_titel', $note, $gewichtung, '$datum', '$fach_id', '$jahr_id', '$user_id')";
    $res = mysqli_query($conn, $sql);
}


    ?>


    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <title><?php
            $sql = "select fach_titel from faecher where BINARY fach_id = '$fach_id'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $fach_titel = $row["fach_titel"];
            echo $fach_titel;
            ?> Prüfungen</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/aussehen.css">
        <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/icons/zeugnis.png">
        <script src="https://alpha.kswe.ginf.ch/planer/commands.js"></script>


        <?php style_echo(); ?>


    </head>
    <body style="margin: 0">


<div style="margin: 5px;">
    <div>
        <p class="text-top"
            <?php
            if (isMobile()) {
                echo 'style="margin-bottom: 0px";';
            }
            ?>><strong> <?php echo $fach_titel; ?> </strong></p>

        <img class="pen" src="/planer/icons/pencil.png" onclick="aendern('fach', '<?php echo $fach_id; ?>')">

        <?php
        // schnitt ausrechnen

        $sql_schnitt = "select gewichtung, note from noten where BINARY fach_id = '$fach_id'";
        $result_schnitt = $conn->query($sql_schnitt);

        $schnitt = 0;
        $gewichtung = 0;

        // Schnitt ausrechnen
        while ($row_schnitt = $result_schnitt->fetch_assoc()) {
            if ($row_schnitt["note"] != 0) {
                $schnitt += $row_schnitt["note"] * $row_schnitt["gewichtung"];
                $gewichtung += $row_schnitt["gewichtung"];
            }
        }

        // Gesamtschnitt ausrechnen und runden
        $schnitt = $schnitt / $gewichtung;
        $schnitt = round($schnitt, 2);

        if ($schnitt >= 5.75) {
            $color = "#3881ff";
        } elseif ($schnitt >= 5.25) {
            $color = "#22a7e0";
        } elseif ($schnitt >= 4.75) {
            $color = "#29d932";
        } elseif ($schnitt >= 4.25) {
            $color = "#f5b50c";
        } elseif ($schnitt >= 3.75) {
            $color = "#f50000";
        } else {
            $color = "#000000";
        }

        if (is_nan($schnitt)) {
            $schnitt = "";
        }

        echo '<script>console.log("' . $schnitt . '")</script>';


        ?>

        <form style="float: right;" method="post" action="/planer/erstellen.php">
            <input type="hidden" name="type" id="type" value="Prüfung">
            <button class="neu-button" type="submit" name="button" id="button">Neue Prüfung</button>
        </form>

        <div class="schnitt_<?php
        if (isMobile()) {
            echo "mobile";
        } else {
            echo "pc";
        } ?>
" style="-webkit-text-fill-color: <?php echo $color; ?>"><strong><?php echo $schnitt; ?></strong></div>
    </div>



    <?php

    $sql = "select * from noten where BINARY fach_id = '$fach_id' order by datum asc";
    $result = $conn->query($sql);

    if ($result->num_rows != 0) {

        while ($row = $result->fetch_assoc()) {

            $schnitt = $row["note"];

            if (!is_nan($schnitt)) {
                if ($schnitt >= 5.75) {
                    $div_num = 6;
                    $color = "#3881ff";
                } elseif ($schnitt >= 5.25) {
                    $div_num = 5.5;
                    $color = "#22a7e0";
                } elseif ($schnitt >= 4.75) {
                    $div_num = 5;
                    $color = "#29d932";
                } elseif ($schnitt >= 4.25) {
                    $div_num = 4.5;
                    $color = "#f5b50c";
                } elseif ($schnitt >= 3.75) {
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

            $note = $row["note"];
            if ($note == 0) {
                $note = "-";
            }

            $box = '
<div ' . $style . ' class="big-div" 
onmouseover="hover(this, ' . $div_num . ')"
onmouseleave="leave(this, ' . $div_num . ')" 
onclick="aendern(' . "'pruefung'" . ", '" . $row["note_id"] . "'" . ')">
    <span class="big-span">';

            $box = $box . $row["note_titel"];
            $box = $box . '
</span>
<span class="noten-span" style="float: right">' . $note . '</span> <br>
    <div class="small-div">
    <img style="height: 10px" src="/planer/icons/calendar.png"> ';

            $sql_list = "select datum from noten where BINARY note_id = '" . $row["note_id"] . "'";
            $result_list = $conn->query($sql_list);
            $row_list = $result_list->fetch_assoc();

            $datum = $row_list["datum"];

            $datum = explode("-", $datum);
            $datum = $datum[2] . "." . $datum[1] . "." . $datum[0];

            $box = $box . $datum;

            if ($row["gewichtung"] != 0) {
                $box = $box . '<span class="small-span"> <img style="height: 10px" src="/planer/icons/weight.png"></span> ' . $row["gewichtung"];
            }
            $box = $box . '
    </div>
    </div>
    <br>';

            echo $box;
        }
    } else {
        echo "<p style='float: right;margin-top: 0px; padding-top: 0px'>
Noch keine Prüfungen eingetragen
<span style='font-size: 50px'>🠕</span></p>";
    }

    ?>


    <form style="width: 50%; margin: 0px; float: left;" action="/planer/faecher/" method="post">
        <button style="background: red; color: white;" type="submit" name="zu_faecher" id="zu_faecher"
                value="zu_faecher"><strong> < Fächer </strong></button>
    </form>
    <?php

    $sql_schnitt = "select gewichtung, note from noten where BINARY fach_id = '$fach_id'";
    $result_schnitt = $conn->query($sql_schnitt);

    $schnitt = 0;
    $gewichtung = 0;

    // Schnitt ausrechnen
    while ($row_schnitt = $result_schnitt->fetch_assoc()) {
        if ($row_schnitt["note"] != 0) {
            $schnitt += $row_schnitt["note"] * $row_schnitt["gewichtung"];
            $gewichtung += $row_schnitt["gewichtung"];
        }
    }

    // Gesamtschnitt ausrechnen und runden
    $schnitt = $schnitt / $gewichtung;
    $schnitt = round($schnitt, 3);


    ?>

    <!-- PopUp-Box -->

    <div class="box">
        <a class="button" href="#popup1">Wunschnote</a>
    </div>

    <div id="popup1" class="overlay">
        <div class="popup">
            <h2 style="text-align: center">Wunschnote berechnen</h2>
            <a class="close" href="#">&times;</a>
            <div class="content">
                <label style="width: 33%;" for="wunschnote">Wunschnote:</label>
                <input style="alignment: right; width: 65%" type="number" id="wunschnote"> <br>
                <label style="padding-right: 1%; width: 34%;" for="gewichtung">Gewichtung: </label>
                <input style="alignment: right; width: 65%;" type="number" id="gewichtung" value="1"> <br>
                <button onclick="wunschnote_berechnen(<?php echo $schnitt; ?>, <?php echo $gewichtung; ?>)"
                        style="width: 92%">Berechnen
                </button>
                <input style="width: 92%" type="number" name="berechnet" id="berechnet" readonly>
            </div>
        </div>
    </div>

</div>

    <ul>
        <li><a href="/planer/dashboard">Dashboard</a></li>
        <li><a href="/planer/schuljahre">
                <?php
                $sql_username = "select users.username from users join faecher on users.user_id = faecher.user_id where BINARY faecher.fach_id = '$fach_id'";
                $result_username = $conn->query($sql_username);
                $row_username = $result_username->fetch_assoc();
                $fach_username = $row_username["username"];
                echo $fach_username;
                ?>
            </a></li>
        <li><a href="/planer/faecher">
                <?php
                $sql_jahr = "select schuljahr.schuljahr from schuljahr join faecher on schuljahr.jahr_id = faecher.jahr_id where BINARY faecher.fach_id = '$fach_id'";
                $result_jahr = $conn->query($sql_jahr);
                $row_jahr = $result_jahr->fetch_assoc();
                $fach_jahr = $row_jahr["schuljahr"];
                echo $fach_jahr;
                ?>
            </a></li>
        <li><a href="/planer/pruefungen" class="list-active"><?php echo $fach_titel;?></a></li>
    </ul>




    </body>
    </html>
    <?php
    mysqli_close($conn);
    ?>