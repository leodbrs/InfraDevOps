<?php
if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>DVD Rental</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="shortcut icon" href="/assets/img/favicon.png" type="image/png">
    <link rel='stylesheet' type='text/css' media='screen' href='/styles/main.css'>
</head>

<body>
    <?php include($_SERVER['DOCUMENT_ROOT'] . "/includes/header.php"); ?>
    <center>
        <section id='corps'>
            <br><br><br><br>
            <h1><a href="mailto:contact@dvdrental.com" class="contact">contact@dvdrental.fr</a></h1>
            <br>
            <h3>06 54 88 95 44 - 9 Rue de l'Arc en Ciel, 74940 Annecy</h3>
        </section>
    </center>
</body>