<?php

$conn = mysqli_connect($hostname, $username, $password, $database)

if (isset($_POST["loeschen"])) {
    $user_id = $_POST["id"];

    $sql = "delete from noten where user_id = '$user_id'";
    $result = $conn->query($sql);

    $sql = "delete from faecher where user_id = '$user_id'";
    $result = $conn->query($sql);

    $sql = "delete from schuljahr where user_id = '$user_id'";
    $result = $conn->query($sql);

    $sql = "delete from users where user_id = '$user_id'";
    $result = $conn->query($sql);


    unset($_COOKIE["fach_id"]);
    setcookie("fach_id", "", 1, "/", "alpha.kswe.ginf.ch");

    unset($_COOKIE["jahr_id"]);
    setcookie("jahr_id", "", 1, "/", "alpha.kswe.ginf.ch");

    unset($_COOKIE["user_id"]);
    setcookie("user_id", "", 1, "/", "alpha.kswe.ginf.ch");


    header("Location: http://alpha.kswe.ginf.ch/");
    exit();
}

header("Location: https://alpha.kswe.ginf.ch/planer/dashboard");
exit();

?>





<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Title</title>

    <link rel="stylesheet" href="/planer/aussehen.css">
    <link rel="icon" type="image/png" href="https://alpha.kswe.ginf.ch/planer/zeugnis.png">
</head>
<body>



</body>
</html>