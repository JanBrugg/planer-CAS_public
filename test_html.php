<?php

function start()
{

    $conn = mysqli_connect($hostname, $username, $password, $database) // mit DB verbinden

    function create_fach_id($conn)
    {
        // Liste mit 64 Zeichen -> base64
        $characters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_');
        $fach_id = "";
        for ($x = 0; $x < 16; $x++) { // 16 mal -> 79’228’162’514’264’337’593’543’950’336 Mögliche IDs
            // 79 Quadrilliarde IDs
            $pos = rand(0, 63);
            $fach_id = $fach_id . $characters[$pos];
        }
        // überprüfen ob es die fach_id schon gibt

        $sql = "select fach_id from faecher where BINARY fach_id = '$fach_id'";
        $result = $conn->query($sql);
        if ($result->num_rows == 0) { // also es gibt nicht mit der gleichen fach_id
            return $fach_id;
        } else {
            create_fach_id($conn);
        }
    }

    if (!isset($_COOKIE["jahr_id"])) {

// Wenn cookie nicht gesetzt ist und auch nicht von Schuljahr-Click kommt, zur Anmeldung
        if (!isset($_POST["schuljahr"]) and !isset($_COOKIE["user_id"])) {
            header("Location: https://alpha.kswe.ginf.ch/planer/anmeldung.php");
            exit();
        } elseif (!isset($_POST["schuljahr"])) { // Wenn es Cookie gibt aber schuljahr nicht, nur zu Schuljahre
            header("Location: https://alpha.kswe.ginf.ch/planer/schuljahre/");
            exit();
        }
    }

    if (isset($_COOKIE["jahr_id"])) {
        $jahr_id = $_COOKIE["jahr_id"];
    } else {
        $jahr_id = $_POST["schuljahr"];
    }

    if (isset($_POST["erstellen"])) {
        $fach_id = create_fach_id($conn);
        $fach_titel = $_POST["fach"];
        $gewichtung = $_POST["gewichtung"];
        $jahr_id = $_COOKIE["jahr_id"];
        $user_id = $_COOKIE["user_id"];

        $sql = "insert into faecher values('$fach_id', '$fach_titel', $gewichtung, '$jahr_id', '$user_id')";
        $res = mysqli_query($conn, $sql);
    }

    setcookie("jahr_id", $jahr_id, 0, "/", "alpha.kswe.ginf.ch");

    setcookie("fach_id", "", 1, "/", "alpha.kswe.ginf.ch");

    echo '


    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Fächer -';
    $sql = "select schuljahr from schuljahr where BINARY jahr_id = '$jahr_id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $schuljahr = $row["schuljahr"];
    echo $schuljahr;
    echo '</title>

        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/aussehen.css">
        <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/zeugnis.png">
        <script src="https://alpha.kswe.ginf.ch/planer/commands.js"></script>

    </head>
    <body>

    <div>
        <p class="text-top"> <strong>';
    echo $schuljahr;
    echo '</strong> </p>
        <form style="float: right;" method="post" action="/planer/erstellen.php">
            <input type="hidden" name="type" id="type" value="Fach">
            <button class="neu-button" type="submit" name="button" id="button">Neues Fach</button>
        </form>
    </div>';


    $sql = "select * from faecher where BINARY jahr_id = '$jahr_id' order by fach_titel asc";
    $result = $conn->query($sql);

    // Alle Fächer durchgehen
    while ($row = $result->fetch_assoc()) {
        // schnitt ausrechnen
        $fach_id = $row["fach_id"];

        $sql_schnitt = "select gewichtung, note from noten where BINARY fach_id = '$fach_id'";
        $result_schnitt = $conn->query($sql_schnitt);

        $schnitt = 0;
        $gewichtung = 0;

        // Schnitt ausrechnen
        while ($row_schnitt = $result_schnitt->fetch_assoc()) {
            $schnitt += $row_schnitt["note"] * $row_schnitt["gewichtung"];
            $gewichtung += $row_schnitt["gewichtung"];
        }

        // Gesamtschnitt ausrechnen und runden
        $schnitt = $schnitt / $gewichtung;
        $schnitt = round($schnitt, 3);

        // Farbe zuordnen
        if (!is_nan($schnitt)) {
            if ($schnitt >= 5.75) {
                $class = "big-div-6";
                $div_num = 6;
            } elseif ($schnitt >= 5.25) {
                $class = "big-div-5-5";
                $div_num = 5.5;
            } elseif ($schnitt >= 4.75) {
                $class = "big-div-5";
                $div_num = 5;
            } elseif ($schnitt >= 4.25) {
                $class = "big-div-4-5";
                $div_num = 4.5;
            } elseif ($schnitt >= 3.75) {
                $class = "big-div-4";
                $div_num = 4;
            } else {
                $class = "big-div-1";
                $div_num = 1;
            }
        } else {
            $class = "big-div-1";
            $div_num = 1;
        }


        $box = '
<div class="' . $class . '"
onmouseover="hover(this, ' . $div_num . ')"
onmouseleave="leave(this, ' . $div_num . ')"
onclick="select_fach(' . "'" . $row["fach_id"] . "'" . ')">
    <span class="big-span">';

        $box = $box . $row["fach_titel"];
        $box = $box . '
</span> <span class="noten-span" style="float: right">';


        if (!is_nan($schnitt)) {
            $box = $box . $schnitt;
        }

        $box = $box . '</span> <br>';

        $box = $box . '<div class="small-div">
     <img style="height: 10px" src="/planer/icons/list.png"> ';

        $sql_list = "select count(note_id) as anzahl from noten where BINARY fach_id = '" . $row["fach_id"] . "'";
        $result_list = $conn->query($sql_list);
        $row_list = $result_list->fetch_assoc();

        $liste_count = $row_list["anzahl"];

        $box = $box . $liste_count;

        if ($row["gewichtung"] != 0) {
            $box = $box . '<span class="small-span"> <img style="height: 10px" src="/planer/icons/weight.png"></span> ' . $row["gewichtung"];
        }
        $box = $box . '
    </div>
</div>
<br>';

        echo $box;
    }


    echo '
    <form action="/planer/schuljahre/" method="post">
        <button style="background: red; color: white;" type="submit" name="zu_schuljahr" id="zu_schuljahr" value="zu_schuljahr"><strong> < Schuljahre </strong></button>
    </form>

    </body>
    </html>';

    mysqli_close($conn);
}
?>