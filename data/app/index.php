<?php
if (!isset($_SESSION)) {
    session_start();
}
if (isset($_SESSION['nom'], $_SESSION['prenom'], $_SESSION['nomutilisateur'], $_SESSION['vendeur'])) {
} else {
    $_SESSION['vendeur'] = 0;
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
            <h1>Envie d'un film ? Choisissez, Reservez, Venez !</h1>
            <div>
                <h3>Faites votre choix :</h3>
                <span>Vous pouvez choisir un film en particulier dans le catalogue ou en découvrir via de nombreuses propositions.</span>

                <h3>Louer ou réserver :</h3>
                <span>Louez le film qui vous intéresse facilement pour être sûr d'avoir un exemplaire récupérable en magazin.</span><br>

                <h3>Récupérer votre film :</h3>
                <span>Une fois votre film loué vous n’avez plus qu’à venir le chercher dans notre magasin de distribution, le regarder chez vous et nous le ramener d'ici 30 jours.</span>
            </div>
            <br><br>
            <a href="/catalogue.php" class="film"><button>Visiter le catalogue</button></a>
        </section>
    </center>
</body>

</html>