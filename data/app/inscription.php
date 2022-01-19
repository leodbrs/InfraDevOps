<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/connexionBDD.php");
$conn1 = connexionBDD();

$maxDate = date("Y-m-d");

$listeParametre = array("nom", "prenom", "email", "date_naissance", "genre", "code_postal", "ville", "username");
$parametre = array();

foreach ($listeParametre as $ligne) {
    if (isset($_POST[$ligne])) {
        $parametre[$ligne] = $_POST[$ligne];
    } else {
        $parametre[$ligne] = "";
    }
}

$genre = array("Homme", "Femme", "Autre");

foreach ($genre as $ligne) {
    $parametre['genre'] = $parametre['genre'] .  "<option value='" . $ligne . "'";
    if ((isset($_POST['genre'])) && $_POST['genre'] == $ligne) {
        $parametre['genre'] = $parametre['genre'] .  " selected";
    }
    $parametre['genre'] = $parametre['genre'] . ">" . $ligne . "</option>";
}
$parametre['genre'] = $parametre['genre'] .  "</select>";

$menuInscription = <<<EOT
<center>
    <form id='corps' method="POST" action="/inscription.php">
        <table>
            <h3>Informations personnelles :</h3>
            <tr>
                <td>Nom, prénom :</td>
                <td><input type="text" name="nom" placeholder="Votre nom" size="24" value="{$parametre['nom']}"></td>
                <td><input type="text" name="prenom" placeholder="Votre prénom" size="24" value="{$parametre['prenom']}"></td>
            </tr>
            <tr>
                <td>Email :</td>
                <td><input type="email" name="email" placeholder="Votre email" size="24" value="{$parametre['email']}"></td>
            </tr>
            <tr>
                <td>Date de naissance :</td>
                <td><input type="date" name="date_naissance"min="1900-01-01" max="{$maxDate}" value="{$parametre['date_naissance']}" style="width:195px"></td>
                <td>Genre : <select name="genre">
                        <option value="">Choisir un genre...</option>{$parametre['genre']}
                    </select></td>
            </tr>
            <tr>
                <td>Code postal :</td>
                <td><input type="text" name="code_postal" id="code_postal" placeholder="Votre code postal" size="24" minlength="5" maxlength="5" value="{$parametre['code_postal']}" onkeyup="compteCar()"></td>
                <td><select class="selectformat" name="ville" id="ville">
                        <option value="">Choisir une ville...</option>
                    </select></td>
            </tr>
        </table>
        <table>
            <h3>Connexion et sécurité :</h3>
            <tr>
                <td>Nom d'utilisateur : </td>
                <td><input type="text" name="username" placeholder="Votre nom d'utilisateur" size="24" value="{$parametre['username']}"></td>
            </tr>
            <tr>
            <tr>
                <td>Mot de passe : </td>
                <td><input type="password" name="password" id="motdepasse" placeholder="Votre mot de passe" size="24"></td>
                <td><input type="password" name="comfirme_password" id="motdepasseconfirm" placeholder="Confirmer le mot de passe" size="24"></td>
                <td><input type="checkbox" onclick="afficherPassword()"> Afficher les mots de passe</input></td>
            </tr>
            <tr>
                <td><input type="submit" value="S'inscrire"></td>
            </tr>
        </table>
    </form>
</center>
EOT;

function retour($message)
{ // COMMENT
    global $menuInscription;
    echo $menuInscription;
    if ($message != null) {
        echo "<center><h3>" . $message . "</h3></center>";
    }
}
function rechercheVille()
{
    global $conn1;
    global $local_ville;
    $query = $conn1->prepare("SELECT nomville FROM ville WHERE idville=:ville;");
    $query->bindValue(':ville', $local_ville, PDO::PARAM_INT);
    $query->execute();

    $result = $query->fetchAll();

    return count($result);
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
    <script src='/scripts/inscription.js'></script>
</head>

<body onload="compteCar(<?= $parametre['ville'] ?>)">
    <?php
    include($_SERVER['DOCUMENT_ROOT'] . "/includes/header.php");
    if (isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['date_naissance'], $_POST['genre'], $_POST['code_postal'], $_POST['ville'], $_POST['username'], $_POST['password'], $_POST['comfirme_password'])) {

        // Récupération des paramètres.
        $local_nomClient = $_POST['nom'];
        $local_prenomClient = $_POST['prenom'];
        $local_adresseMail = $_POST['email'];
        $local_dateNaissance = $_POST['date_naissance'];
        $local_genre = $_POST['genre'];
        $local_codePostal = $_POST['code_postal'];
        $local_ville = $_POST['ville'];
        $local_nomUtilisateur = $_POST['username'];
        $local_password = $_POST['password'];
        $local_comfirmePassword = $_POST['comfirme_password'];

        // Vérification liée au nom.
        if ($local_nomClient == null) {
            retour("Le champs nom est vide.");
        } elseif (preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]{1,50}$/", $local_nomClient) == 0) {
            retour("Le nom est trop long ou incorrect.");
        }
        // Vérification liée au prénom.
        elseif ($local_prenomClient == null) {
            retour("Le champs prénom est vide.");
        } elseif (preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]{1,50}$/", $local_prenomClient) == 0) {
            retour("Le prénom est trop long ou incorrect.");
        }
        // Vérification liée à l'email.
        elseif ($local_adresseMail == null) {
            retour("Le champs d'adresse mail est vide.");
        } elseif (strlen($local_adresseMail) > 100) { // #################### PRENDRE UNE REGEX PLUS SIMPLE ####################
            retour("L'email est trop long ou incorrect.");
        }
        // Vérification liée à la date de naissance.
        elseif ($local_dateNaissance == null) {
            retour("Le champs date de naissance est vide.");
        } elseif (preg_match('/([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))/', $local_dateNaissance) == 0) {
            retour("La date n'est pas correcte.");
        }
        // Vérification liée au genre.
        elseif ($local_genre == null) {
            retour("Aucun genre n'a été selectionné.");
        } elseif (preg_match('/^(Homme)$|^(Femme)$|^(Autre)$/', $local_genre) == 0) {
            retour("Le genre n'est pas correct.");
        }
        // Vérification liée au code postal.
        elseif ($local_codePostal == null) {
            retour("Le champs code postal est vide.");
        } elseif (preg_match('/^[0-9]{5}$/', $local_codePostal) == 0) {
            retour("Le code postal n'est pas correcte.");
        }
        // Vérification liée à la ville.
        elseif (rechercheVille() == 0) {
            retour("La ville sélectionnée n'est pas correcte.");
        }
        // Vérification liée au nom d'utilisateur.
        elseif ($local_nomUtilisateur == null) {
            retour("Le champs nom d'utilisateur est vide.");
        } elseif (preg_match('/^[a-z0-9-_.]{1,50}$/', $local_nomUtilisateur) == 0) {
            retour("Le nom d'utilisateur n'est pas correct.");
        }
        // Vérifie si le nom d'utilisateur est disponible ou non.
        elseif (rechercheNomUtilisateur() == 1) {
            retour("Ce nom d'utilisateur est déjà pris");
        }
        // Vérification liée au mot de passe.
        elseif ($local_password == null) { // Vérification que le champs de mot de passe n'est pas vide.
            retour("Les champs mots de passe sont vides, veuillez réessayer.");
        } elseif (strlen($local_password) > 50) { // Vérification que le champs de mot de passe n'est pas vide.
            retour("Le mot de passe est trop long.");
        } elseif ($local_password != $local_comfirmePassword) { // Vérification que les chj'iame amps de mots de passe sont identiques.
            retour("Les mots de passe ne correspondent pas, veuillez réessayer.");
        }
        // Détection complexité du mot de passe.
        elseif (strlen($local_password) < 8) {
            retour("Le mot de passe doit contenir au moins 8 caractères.");
        } elseif (preg_match("/[0-9]/", $local_password) == 0) {
            retour("Le mot de passe doit contenir au moins un chiffre");
        } elseif (preg_match("/[A-Z]/", $local_password) == 0) {
            retour("Le mot de passe doit contenir au moins une lettre majuscule");
        } elseif (preg_match("/[!@#$%^&*-]/", $local_password) == 0) {
            retour("Le mot de passe doit contenir au moins un caractère spécial");
        } else { // Envoi des informations à la base de données. (Le mot de passe est chiffré en MD5 durant la requête)

            $query = $conn1->prepare("INSERT INTO client(nomclient, prenomclient, email, datenaissance, genre, nomutilisateur, motdepasse, refville) VALUES (:nomclient, :prenomclient, :email, :datenaissance, :genre, :nomutilisateur, :motdepasse, :refville) RETURNING * ;");
            $query->bindValue(':nomclient', $local_nomClient, PDO::PARAM_STR);
            $query->bindValue(':prenomclient', $local_prenomClient, PDO::PARAM_STR);
            $query->bindValue(':email', $local_adresseMail, PDO::PARAM_STR);
            $query->bindValue(':datenaissance', $local_dateNaissance, PDO::PARAM_STR);
            $query->bindValue(':genre', $local_genre, PDO::PARAM_STR);
            $query->bindValue(':nomutilisateur', $local_nomUtilisateur, PDO::PARAM_STR);
            $query->bindValue(':motdepasse', md5($local_password), PDO::PARAM_STR);
            $query->bindValue(':refville', $local_ville, PDO::PARAM_INT);

            if ($query->execute()) {
                echo "<center><h3>Bienvenue $local_prenomClient, vous êtes bien inscrit</h3></center>";
            } else {
                // header('Location: /inscription.php');
                echo "<center><h3>Une erreur s'est produite et vous n'avez pas été inscrits, veuillez réessayer.</h3></center>";
            }
        }
    } else { // Si un paramètre n'est pas passé dans l'URL ceci se produit.
        echo $menuInscription;
    }
    ?>
</body>