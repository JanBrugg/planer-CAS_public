<?php

if (isset($_POST["abmelden"])) {
    unset($_COOKIE["user_id"]);
    setcookie("user_id", "", 1, "/", "alpha.kswe.ginf.ch");

    unset($_COOKIE["jahr_id"]);
    setcookie("jahr_id", "", 1, "/", "alpha.kswe.ginf.ch");

    unset($_COOKIE["fach_id"]);
    setcookie("fach_id", "", 1, "/", "alpha.kswe.ginf.ch");
}

if (isset($_COOKIE["user_id"])){
    echo '
    <form action="schuljahre/" method="post" id="myForm">
    <input type="hidden" name="place" id="palce" value="place">
    </form>
    <script type="text/javascript">document.getElementById("myForm").submit();</script>
    ';
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Anmeldung</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/aussehen.css">
    <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/icons/zeugnis.png">
    <script src="https://alpha.kswe.ginf.ch/planer/commands.js"></script>

    <style>

        .stay_login_on {
            border: 1px solid;
            padding: 10px;
            box-shadow: 1px 3px;
        }

        .stay_login_off {
            border: 0px solid;
            padding: 10px;
            box-shadow: 0px 0px;
        }

    </style>
    
</head>
<body>

<?php
if ($_GET["grund"] == "username_gibs_nicht"){
    echo "Username gibts nicht.";
} elseif ($_GET["grund"] == "passwort_falsch"){
    echo "Passwort Falsch.";
}
?>

    <form method="post" action="dashboard/">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required> <br>
        <label for="passwort">Passwort:</label>
        <input type="password" id="passwort" name="passwort" required> <br> <br>


        <button type="submit" name="anmelden" id="anmelden">Anmelden</button>
        <input type="hidden" id="angemeldet_bleiben" name="angemeldet_bleiben" value="nein">
        <label style="background: white;" onclick="colorchange(this)" onmouseover="hover(this, -1)" onmouseleave="leave(this, -1)" class="stay_login_off" for="angemeldet_bleiben" id="angemeldet_id">Angemeldet bleiben</label>
    </form>


<p>Noch keinen Account? <a href="account_erstellen.php">Erstelle jetzt einen!</a> </p>

</body>
</html>