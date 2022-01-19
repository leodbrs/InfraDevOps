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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel='stylesheet' type='text/css' media='screen' href='/styles/main.css'>
</head>

<body>
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/connexionBDD.php");
    $conn1 = connexionBDD();

    include($_SERVER['DOCUMENT_ROOT'] . "/includes/header.php");
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/fonctionPanier.php");
    ?>

    <?php
    // if (!isset($_SESSION['panier'])){
    //     creationPanier();
    // }

    // récupération de l'id du film et récupération des information correspondant
    $idfilm = $_GET['id_film'];
    $sql = 'SELECT * FROM film WHERE idfilm=' . $idfilm . '';
    $query = $conn1->prepare($sql);
    $query->execute();
    $listefilm = $query->fetchall();


    // si le panier est créé --> ajoute le film au panier avec une quantité de 1 
    if (isset($_GET['panier'])) {
        $nomfilm = $listefilm[0]['titrefilm'];
        // echo $nomfilm ;
        ajouterFilm($nomfilm, 1);
    }
    ?>
    <!-- affichage des informations -->
    <section id='corps'>
        <table class='film'>
            <tr>
                <td>
                    <h3><?= $listefilm[0]['titrefilm']  ?></h3>
                </td>
            </tr>
            <tr>
                <td><img src='/upload/<?= $listefilm[0]['image'] ?>' alt='Image non chargée' style='width:400px'></td>
                <td style='width: 400px;'><span style='width: 11px;'>
                        <h4>Synopsis :</h4><?= $listefilm[0]['description'] ?><br><br><a href='/catalogue.php'><button>retour</button></a></td>
            </tr>
            <tr>
                <?php
                if (isset($_SESSION['nom'], $_SESSION['prenom'], $_SESSION['nomutilisateur'], $_SESSION['vendeur'])) {

                    if (!isset($_SESSION['panier'])) {
                        creationPanier();
                    }
                ?> <td>
                        <?php
                        $query = $conn1->prepare("SELECT idexemplaire FROM exemplaire WHERE reffilm =:reffilm AND dispo=true LIMIT 1 OFFSET 0;");
                        $query->bindValue(':reffilm', $idfilm, PDO::PARAM_INT);
                        $query->execute();

                        $local_exemplaire = $query->fetchAll();
                        // Si un exemplaire est disponible pour le film
                        if (isset($local_exemplaire[0]['idexemplaire'])) {
                            echo "<h3>Disponible</h3>";
                        } else {
                            echo "<h3>Non disponible</h3>";
                        }
                        ?>
                        <p><a href="/includes/film.php/?id_film=<?= $listefilm[0]['idfilm'] ?>&panier=1"><button>louer</button></p>
                    </td>

                <?php } else { ?>

                    <td>
                        <p><a href="/connexion.php"><button>louer</button></p>
                    </td>
                <?php
                }
                ?>

            </tr>
            <table>
    </section>
</body>