<?php

$conn = mysqli_connect($hostname, $username, $password, $database) // mit DB verbinden

include "../functions.php";

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

unset($_COOKIE["fach_id"]);
setcookie("fach_id", "", 1, "/", "alpha.kswe.ginf.ch");


if (isset($_POST["erstellen"])){
    $fach_id = create_fach_id($conn);
    $fach_titel = $_POST["fach"];
    $fach_titel = str_replace("'", "''", $fach_titel);
    $gewichtung = $_POST["gewichtung"];
    $jahr_id = $_COOKIE["jahr_id"];
    $user_id = $_COOKIE["user_id"];

    $sql = "insert into faecher values('$fach_id', '$fach_titel', $gewichtung, '$jahr_id', '$user_id')";
    $res = mysqli_query($conn, $sql);
}

if (isset($_POST["aendern"])) {
    $schuljahr = $_POST["schuljahr"];
    $schuljahr = str_replace("'", "''", $schuljahr);
    $jahr_id = $_POST["id"];

    $sql = "update schuljahr set schuljahr = '$schuljahr' where BINARY jahr_id = '$jahr_id'";
    $result = $conn->query($sql);
    mysqli_close($conn);
    header("Location: https://alpha.kswe.ginf.ch/planer/faecher/");
    exit();
}

if (isset($_POST["loeschen"])) {
    $jahr_id = $_POST["id"];

    $sql = "delete from noten where BINARY jahr_id = '$jahr_id'";
    $result = $conn->query($sql);

    $sql = "delete from faecher where BINARY jahr_id = '$jahr_id'";
    $result = $conn->query($sql);

    $sql = "delete from schuljahr where BINARY jahr_id = '$jahr_id'";
    $result = $conn->query($sql);

    header("Location: https://alpha.kswe.ginf.ch/planer/schuljahre/");
    exit();
}

setcookie("jahr_id", $jahr_id, 0, "/", "alpha.kswe.ginf.ch");

setcookie("fach_id", "", 1, "/", "alpha.kswe.ginf.ch");

?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Fächer - <?php
        $sql = "select schuljahr from schuljahr where BINARY jahr_id = '$jahr_id'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $schuljahr = $row["schuljahr"];
        echo $schuljahr;
        ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/aussehen.css">
    <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/icons/zeugnis.png">
    <script src="https://alpha.kswe.ginf.ch/planer/commands.js"></script>

</head>
<body style="margin: 0">

<div style="margin: 5px;">

<div>
    <p class="text-top"
    <?php if (isMobile()) {echo 'style="margin-bottom: 0px"';} ?>
    > <strong> <?php echo $schuljahr;?> </strong> </p>

    <img class="pen" src="/planer/icons/pencil.png" onclick="aendern('schuljahr', '<?php echo $jahr_id;?>')">

    <?php

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

        $schnitt_fach = round($schnitt_fach*2) / 2;

        if (!is_nan($schnitt_fach)) {
            if ($schnitt_fach != 0) {
                $schnitt_jahr += $schnitt_fach * $row_fach_select["gewichtung"];
                $gewichtung_jahr += $row_fach_select["gewichtung"];
            }
        }
    }

    $gesamtsschnitt_jahr = $schnitt_jahr / $gewichtung_jahr;
    $gesamtsschnitt_jahr = round($gesamtsschnitt_jahr, 2);

    $schnitt = $gesamtsschnitt_jahr;
    if ($schnitt >= 5.75) {
        $color = "#3881ff";
    } elseif ($schnitt >= 5.25) {
        $color = "#22a7e0";
    } elseif ($schnitt >= 4.75) {
        $color = "#29d932";
    } elseif ($schnitt >= 4.25) {
        $color = "#f5b50c";
    } elseif ($schnitt >= 3.75) {
        $color = "#c41818";
    } else {
        $color = "#000000";
    }

    if (is_nan($schnitt)) {
        $gesamtsschnitt_jahr = "";
    }
    ?>
    <form style="float: right;" method="post" action="/planer/erstellen.php">
        <input type="hidden" name="type" id="type" value="Fach">
        <button class="neu-button" type="submit" name="button" id="button">Neues Fach</button>
    </form>
        <div class="schnitt_<?php
        if (isMobile()) {
            echo "mobile";
        } else {
            echo "pc";
        }?>
" style="-webkit-text-fill-color: <?php echo $color;?>"><strong><?php echo $gesamtsschnitt_jahr; ?></strong></div>
  </div>


<?php
$sql = "select * from faecher where BINARY jahr_id = '$jahr_id' order by fach_titel asc";
$result = $conn->query($sql);

if ($result->num_rows != 0) {

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
            if ($row_schnitt["note"] != 0) {
                $schnitt += $row_schnitt["note"] * $row_schnitt["gewichtung"];
                $gewichtung += $row_schnitt["gewichtung"];
            }
        }

        // Gesamtschnitt ausrechnen und runden
        $schnitt = $schnitt / $gewichtung;
        $schnitt = round($schnitt, 2);


        // Farbe zuordnen
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

        $schnitt = round($schnitt * 2) / 2;

        $box = '
<div ' . $style . ' class="big-div"
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
} else {
    echo "<p style='float: right;margin-top: 0px; padding-top: 0px'>
Noch keine Fächer eingetragen
<span style='font-size: 50px'>🠕</span></p>";
}

?>


<!-- <img style="height: 10px" src="/planer/icons/weight.png"> ' . $row["gewichtung"] . '
        <span class="small-span"></span> -->

<form action="/planer/schuljahre/" method="post">
    <button style="background: red; color: white;" type="submit" name="zu_schuljahr" id="zu_schuljahr" value="zu_schuljahr"><strong> < Schuljahre </strong></button>
</form>

</div>

<ul>
    <li><a href="/planer/dashboard">Dashboard</a></li>
    <li><a href="/planer/schuljahre">
            <?php
            $sql_username = "select users.username from users join schuljahr on users.user_id = schuljahr.user_id where BINARY schuljahr.jahr_id = '$jahr_id'";
            $result_username = $conn->query($sql_username);
            $row_username = $result_username->fetch_assoc();
            $fach_username = $row_username["username"];
            echo $fach_username;
            ?>
        </a></li>
    <li><a href="/planer/faecher" class="list-active">
            <?php
            echo $schuljahr;
            ?>
        </a></li>
</ul>

</body>
</html>
<?php
mysqli_close($conn);
?>