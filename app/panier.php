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
    <script src='/scripts/client.js'></script>
</head>

<body>
    <?php
    //récupération du header
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/connexionBDD.php");
    $conn1 = connexionBDD();
    //récupération des fonctions du panier
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/fonctionPanier.php");

    include($_SERVER['DOCUMENT_ROOT'] . "/includes/header.php");

    if (!isset($_SESSION['panier'])) {
        creationPanier();
    }
    ///////////////////////////////////////////// VALIDATION DU PANIER /////////////////////////////////////////////////////////////////////////
    // si la validation est demandé : 
    if (isset($_GET['validation'])) {
        $commande = $_SESSION['panier']['nomfilm'];
        foreach ($commande as $ligne) {
            $local_nomfilm = $ligne;
            // echo $local_nomfilm;
            // echo $local_nomfilm;

            // récupération de l'id du film
            $sql = 'SELECT idfilm FROM film  WHERE titrefilm= :nomfilm;';
            $query = $conn1->prepare($sql);
            $query->bindValue(':nomfilm', $local_nomfilm, PDO::PARAM_STR);
            $query->execute();
            $infofilm = $query->fetch();
            $idfilm = $infofilm['idfilm'];
            // echo $idfilm;

            // recupérations un exemplaire disponible du film
            $sql = 'SELECT idexemplaire FROM exemplaire WHERE reffilm =:film AND dispo=true LIMIT 1 OFFSET 0;';
            $query = $conn1->prepare($sql);
            $query->bindValue(':film', $idfilm, PDO::PARAM_INT);
            $query->execute();
            $infoexemplaire = $query->fetchAll();

            // si le film est disponible :
            if (isset($infoexemplaire[0]['idexemplaire'])) {
                $idexemplaire = $infoexemplaire[0]['idexemplaire'];
                // echo $idexemplaire;
                $date = date("Y-m-d");
                // echo $date;
                $idclient = $_SESSION['id'];
                // echo $idclient;

                // ajoute une location dans la table location de 30 jours à la date du jour avec l'id client et exemplaire choisie
                $sql = 'INSERT INTO location(nbjours, dateloc, refclient, refexemplaire) VALUES (30,:dateActuelle,:idrefclient,:idrefexemplaire); ';
                $query = $conn1->prepare($sql);
                $query->bindValue(':dateActuelle', $date, PDO::PARAM_STR);
                $query->bindValue(':idrefclient', $idclient, PDO::PARAM_INT);
                $query->bindValue(':idrefexemplaire', $idexemplaire, PDO::PARAM_INT);
                $query->execute();

                // passe l'exemplaire choisie en non disponible
                $sql = 'UPDATE exemplaire SET dispo = false WHERE idexemplaire = :idexemplaire;';
                $query = $conn1->prepare($sql);
                $query->bindValue(':idexemplaire', $idexemplaire, PDO::PARAM_INT);
                $query->execute();

                modifierQTE($local_nomfilm, 0);

                echo "<h4>le film : " . $local_nomfilm . " a bien été enregistré</4><br>";

                // si le film n'est pas disponible
            } else {
                echo "<h4>le film : " . $local_nomfilm . " n'est pas disponible</4><br>";
            }
        }
    }
    ////////////////////////////////////////////////////// FIN VALIDATION //////////////////////////////////////////////////////////////////////////////////
    //si le film est supprimé :
    if (isset($_POST['nomfilm'])) {

        $local_nomfilm = $_POST['nomfilm'];

        modifierQTE($local_nomfilm, 0);
    }
    ?>
    <!------------------------------------------------------ AFFICHAGE DU PANIER  ------------------------------------------------------------------------------>
    <center>
        <h3>Votre panier</h3>
        <?php

        //récupération du nombre de film dans le panier
        $nbFilm = count($_SESSION['panier']['nomfilm']);
        //si il n'y a pas de film dans le panier 
        //si le nombre de film est inférieur ou égale à 0 le panier est vide
        if ($nbFilm <= 0) {
            echo "<span>Votre panier est vide</span>";
        }
        ?>
        <section id='corps'>
            <table style="width: 400px">

                <?php
                if (creationPanier()) {
                    //si le nombre de film est supérieur strictement à 0 : afficher le code permettant de voir le panier
                    if ($nbFilm > 0) {
                        // affiche les films 1 par 1
                        for ($i = 0; $i < $nbFilm; $i++) {
                ?>
                            <form method="POST" action="panier.php">
                                <tr>
                                    <td>nomfilm</td>
                                    <td>Quantité</td>
                                    <td>Action</td>
                                </tr>
                                <tr>
                                    <td><input type="text" size="40" name="nomfilm" value="<?= $_SESSION['panier']['nomfilm'][$i] ?>" readonly></td>
                                    <td><input type="text" size="4" name="qte" value="<?= $_SESSION['panier']['qte'][$i] ?>" readonly></td>
                                    <td><input type="submit" value="Annuler la selection"></td>
                                </tr>
                            </form>
                <?php
                        }
                    }
                }
                ?>
            </table>
            <div class="film"><a href='/catalogue.php'><button>retour au catalogue</button></a></div><br>
            <div class="film"><a href='/panier.php/?validation=1'><button>valider la commande</button></a></div>
        </section>
    </center>
</body>