<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Account erstellen</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://alpha.kswe.ginf.ch/planer/aussehen.css">
    <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/icons/zeugnis.png">
    <script src="https://alpha.kswe.ginf.ch/planer/commands.js"></script>

</head>
<body>

<?php
if ($_GET["grund"] == "passwort_falsch") {
    echo "Passwort stimmt nicht überein";
} elseif ($_GET["grund"] == "email_gibs") {
    echo "Email gibts bereits";
} elseif ($_GET["grund"] == "username_gibs") {
    echo "Username gibts bereits";
}
?>

 <form method="post" action="schuljahre/index.php">
     <label for="username">Username:</label>
     <input type="text" id="username" name="username" required>
     <br>
     <label for="vorname">Vorname:</label>
     <input type="text" id="vorname" name="vorname" required>
     <br>
     <label for="nachname">Nachname:</label>
     <input type="text" id="nachname" name="nachname" required>
     <br>
     <label for="email">Email:</label>
     <input type="email" id="email" name="email" required>
     <br>
     <label for="passwort">Passwort:</label>
     <input type="password" id="passwort" name="passwort" required onfocusout="password_checker()">
     <br>
     <label for="passwort_bestätigen">Passwort bestätigen:</label>
     <input type="password" id="passwort_bestätigen" name="passwort_bestätigen" required onfocusout="password_checker()">
     <p style="color: #FF0000" id="passwort_ungleich"></p>
     <label>Durch erstellen eines Accounts stimmen Sie den <a href="nutzungsbedingungen" target="_blank">Nutzungsbedingungen</a> zu.</label>
     <br>

     <button type="submit" name="account_erstellen" id="account_erstellen">Erstellen und Akzeptieren</button>
 </form>

</body>
</html>