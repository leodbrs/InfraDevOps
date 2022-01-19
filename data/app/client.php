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

    include($_SERVER['DOCUMENT_ROOT'] . "/includes/header.php");
    ?>

    <?php
    function retour($message)
    { // COMMENT
        global $menuInscription;
        echo $menuInscription;
        if ($message != null) {
            echo "<h3>" . $message . "</h3>";
        }
    }

    function rechercheNomUtilisateur()
    {
        global $conn1;
        global $local_nomUtilisateur;
        $query = $conn1->prepare("SELECT * FROM client WHERE nomutilisateur=:nomutilisateur");
        $query->bindValue(':nomutilisateur', $local_nomUtilisateur, PDO::PARAM_STR);
        $query->execute();

        $result = $query->fetchAll();

        return count($result);
    }
    // récupération des variables de la session client
    if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {

        $idclient = $_SESSION['id'];
        $nomclient = $_SESSION['nom'];
        $prenomclient = $_SESSION['prenom'];
        $utilisateur = $_SESSION['nomutilisateur'];

        // récupération des autres informations du client
        $sql = 'SELECT * FROM client WHERE idclient=' . $idclient . '';
        $query = $conn1->prepare($sql);
        $query->execute();
        $infoclient = $query->fetchall();

        // echo "<pre>";
        // print_r($infoclient);
        // echo "</pre>";
    ?>

        <center>
            <section id="corps">
                <form id='corps' method="POST" action="/includes/verifclient.php">
                    <table>
                        <h3>Vos informations</h3>
                        <div class='invisible'><input type="text" name="idclient" id="idclient" value='<?= $idclient ?>'></div>
                        <tr>
                            <td>Nom, prénom :</td>
                            <td><input type="text" name="nomclient" id="nomclient" size="24" value='<?= $nomclient ?>'></td>
                            <td><input type="text" name="prenomclient" id="prenomclient" size="24" value='<?= $prenomclient ?>'></td>
                        </tr>
                        <tr>
                            <td>Genre :</td>
                            <td>
                                <?php
                                echo "<select name='genre' id='genre' onchange='changeImage(this.value)'>";
                                $genre = array("Homme", "Femme", "Autre");

                                foreach ($genre as $ligne) {
                                    echo "<option value='" . $ligne . "'";
                                    if ($infoclient[0]['genre'] == $ligne) {
                                        echo " selected";
                                    }
                                    echo ">" . $ligne;
                                    echo "</option>";
                                }
                                echo "</select>"
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>Nom d'utilisateur :</td>
                            <td><input type="text" name="utilisateur" id="utilisateur" size="24" value='<?= $utilisateur ?>'></td>
                        </tr>
                        <tr>
                            <td>Adresse email :</td>
                            <td><input type="text" name="email" id="email" size="24" value='<?= $infoclient[0]['email'] ?>'></td>
                        </tr>
                        <tr>
                            <td>Nouveau mot de passe :</td>
                            <td><input type="password" name="password" id="motdepasse" placeholder="Votre mot de passe" size="24"></td>
                            <td><input type="password" name="comfirme_password" id="motdepasseconfirm" placeholder="Confirmer le mot de passe" size="24"></td>
                            <td><input type="checkbox" onclick="afficherPassword()"> Afficher les mots de passe</input></td>
                        </tr>
                        <tr>
                            <td>Mot de passe actuel :</td>
                            <td><input type="password" name="actuel_password" id="motdepasseactuel" placeholder="Confirmer le mot de passe" size="24"></td>
                            <td><input type="checkbox" onclick="afficherPasswordActuel()"> Afficher les mots de passe</input></td>
                        </tr>
                    </table>
                    <input type="submit" value="modifier les informations">
                </form>
            </section>
        </center>
    <?php } ?>
</body>