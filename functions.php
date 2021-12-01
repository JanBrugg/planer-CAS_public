<?php

function isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function create_user_id($conn)
{
    // Liste mit 64 Zeichen -> base64
    $characters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_');
    $user_id = "";
    for ($x = 0; $x < 16; $x++) { // 16 mal -> 79’228’162’514’264’337’593’543’950’336 Mögliche IDs
        // 79 Quadrilliarde IDs
        $pos = rand(0, 63);
        $user_id = $user_id . $characters[$pos];
    }
    // überprüfen ob es die User_id schon gibt

    $sql = "select user_id from users where BINARY user_id = '$user_id'";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) { // also es gibt nicht mit der gleichen user_id
        return $user_id;
    } else {
        create_user_id($conn);
    }
}

function create_jahr_id($conn)
{
    // Liste mit 64 Zeichen -> base64
    $characters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_');
    $jahr_id = "";
    for ($x = 0; $x < 16; $x++) { // 16 mal -> 79’228’162’514’264’337’593’543’950’336 Mögliche IDs
        // 79 Quadrilliarde IDs
        $pos = rand(0, 63);
        $jahr_id = $jahr_id . $characters[$pos];
    }
    // überprüfen ob es die jahr_id schon gibt

    $sql = "select jahr_id from schuljahr where BINARY jahr_id = '$jahr_id'";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) { // also es gibt nicht mit der gleichen jahr_id
        return $jahr_id;
    } else {
        create_jahr_id($conn);
    }
}

function create_fach_id($conn) {
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

function create_note_id($conn)
{
    // Liste mit 64 Zeichen -> base64
    $characters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_');
    $note_id = "";
    for ($x = 0; $x < 16; $x++) { // 16 mal -> 79’228’162’514’264’337’593’543’950’336 Mögliche IDs
        // 79 Quadrilliarde IDs
        $pos = rand(0, 63);
        $note_id = $note_id . $characters[$pos];
    }
    // überprüfen ob es die note_id schon gibt

    $sql = "select note_id from noten where BINARY note_id = '$note_id'";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) { // also es gibt nicht mit der gleichen note_id
        return $note_id;
    } else {
        create_note_id($conn);
    }
}

function create_hausaufgabe_id($conn)
{
    // Liste mit 64 Zeichen -> base64
    $characters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_');
    $hausaufgabe_id = "";
    for ($x = 0; $x < 16; $x++) {
        $pos = rand(0, 63);
        $hausaufgabe_id = $hausaufgabe_id . $characters[$pos];
    }
    // überprüfen ob es die note_id schon gibt

    $sql = "select hausaufgabe_id from hausaufgaben where BINARY hausaufgabe_id = '$hausaufgabe_id'";
    $result = $conn->query($sql);
    if ($result->num_rows == 0) { // also es gibt nicht mit der gleichen note_id
        return $hausaufgabe_id;
    } else {
        create_note_id($conn);
    }
}

function wrong_change($grund, $id){
    $form = '<form method="post" action="/planer/aendern.php" id="grundForm">';
    $form .= '<input type="hidden" name="type" id="type" value="User">';
    $form .= '<input type="hidden" name="id" id="id" value="' . $id . '">';
    $form .= '<input type="hidden" name="grund" id="grund" value="' . $grund . '">';
    $form .= '</form>';

    echo $form;
    echo '<script> document.getElementById("grundForm").submit()</script>';
}


function display_hausaufgabe($result, $today, $conn, $style) {
    while ($row = $result->fetch_assoc()) {
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
}

function kalender_eintrag($titel, $fach, $type) {
    if ($type == "prüfung") {
        $color = "#ff7a7a";
    } elseif ($type == "hausaufgabe") {
        $color = "#c5c7ff";
    } else {
        $color = "#000000";
    }

    echo '<span style="
            display: inline-block;
            width: 100%;
            left: 0;
            background: ' . $color . ';
            color: #3e4e63;
            padding-top: 2px;
            padding-bottom: 2px;
            padding-left: 2px;
            font-size: 12.5px;
            position: absolute;
            border-radius: 4px;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;" title="' . $titel . "; " . $fach . '"><strong>' . $titel . "</strong><br>" . $fach . '</span>';
    echo "<br><br>";
}


function style_echo()
{
    echo '        <style>
            .box {
                float: right;
                display: inline-block;
                background: rgba(255, 255, 255, 0.2);
                background-clip: padding-box;
                text-align: center;
                position: relative;
            }

            .button {
                font-size: 1em;
                color: #fff;
                text-decoration: none;
                cursor: pointer;
                transition: all 1ms ease-out;
            }

            .overlay {
                position: fixed;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(0, 0, 0, 0.7);
                transition: opacity 1ms;
                visibility: hidden;
                opacity: 0;
                z-index: 100;
            }

            .overlay:target {
                visibility: visible;
                opacity: 1;
            }

            .popup {
                margin: 35px auto;
                padding: 20px;
                background: #fff;
                border-radius: 5px;
                width: 90%;
                position: relative;
                transition: all 1ms ease-in-out;
                position: relative;
                z-index: 100;
            }

            .popup h2 {
                margin-top: 0;
                color: #333;
                font-family: Tahoma, Arial, sans-serif;
            }

            .popup .close {
                position: absolute;
                top: 20px;
                right: 30px;
                transition: all 1ms;
                font-size: 30px;
                font-weight: bold;
                text-decoration: none;
                color: #333;
            }

            .popup .close:hover {
                color: #06D85F;
            }

            .popup .content {
                max-height: 30%;
                overflow: auto;
            }


        </style>';
}