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


    //////////////////////////////////////////////////////// DEFINITION DES FONCTION PHP SERVANT AUX VERIF DES INFOS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function retour($message)
    { // COMMENT
        global $menuInscription;
        echo $menuInscription;
        if ($message != null) {
            echo "<center><h3>" . $message . "</h3></centre>";
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
    /////////////////////////////////////////////////////// VERIFICATION INFORMATIONS RENTREE ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // récupération des informations entré :
    if (isset($_POST['idclient'], $_POST['nomclient'], $_POST['prenomclient'], $_POST['genre'], $_POST['utilisateur'], $_POST['email'], $_POST['password'], $_POST['comfirme_password'], $_POST['actuel_password'])) {

        $local_nomClient = $_POST['nomclient'];
        $local_prenomClient = $_POST['prenomclient'];
        $local_nomUtilisateur = $_POST['utilisateur'];
        $local_adresseMail = $_POST['email'];
        $local_genre = $_POST['genre'];
        $local_password = $_POST['password'];
        $local_confirmpassword = $_POST['comfirme_password'];
        $local_actuelpassword = $_POST['actuel_password'];
        $local_idclient = $_POST['idclient'];

        // récupération des inforamtions du client actuellement dans la base de donnée 
        $sql = 'SELECT * FROM client WHERE idclient=:client;';
        $query = $conn1->prepare($sql);
        $query->bindValue(':client', $local_idclient, PDO::PARAM_INT);
        $query->execute();
        $infoclient = $query->fetchall();

        // si les informations informations rentré n'ont pas été modifié
        if (($local_nomClient == $infoclient[0]['nomclient']) && ($local_prenomClient == $infoclient[0]['prenomclient']) && ($local_nomUtilisateur == $infoclient[0]['nomutilisateur']) && ($local_genre == $infoclient[0]['genre']) && ($local_adresseMail == $infoclient[0]['email']) && ($local_password == null)) {
            retour("aucunes informations modifiées");
        } else {
            // si le champ nomclient est vide 
            if ($local_nomClient == null) {
                retour("Le champs nom est vide.");
                // si le nom est trop long ou avec des caractères incorrects 
            } elseif (preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]{1,50}$/", $local_nomClient) == 0) {
                retour("Le nom est trop long ou incorrect.");
                // si le champ prenomclient est vide 
            } elseif ($local_prenomClient == null) {
                retour("Le champs prénom est vide.");
                // si le prenom est trop long ou avec des caractères incorrects 
            } elseif (preg_match("/^[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]{1,50}$/", $local_prenomClient) == 0) {
                retour("Le prénom est trop long.");
                // si le champ addresse mail est vide 
            } elseif ($local_adresseMail == null) {
                retour("Le champs d'adresse mail est vide.");
            } elseif (strlen($local_adresseMail) > 100) { // aucun regex exploitable trouvé //
                retour("L'email est trop long.");
            } elseif ($local_genre == null) {
                retour("Aucun genre n'a été selectionné.");
            } elseif (preg_match('/^(Homme)$|^(Femme)$|^(Autre)$/', $local_genre) == 0) {
                retour("Le genre n'est pas correct.");
            }
            // si le champ utilisateur est vide 
            elseif ($local_nomUtilisateur == null) {
                retour("Le champs nom d'utilisateur est vide.");
                // si le nom d'utilisateur respect les caractères autorisés 
            } elseif (preg_match('/^[a-z0-9-_.]{1,50}$/', $local_nomUtilisateur) == 0) {
                retour("Le nom d'utilisateur n'est pas correct.");
            }
            // recherche si le nom d'utilisateur est pris grâce à la fonction
            elseif ((rechercheNomUtilisateur() == 1) && ($local_nomUtilisateur != $infoclient[0]['nomutilisateur'])) {
                retour("ce nom d'utilisateur est déjà pris");
            }
            // si le champ mot de passe actuelle est vide 
            elseif ($local_actuelpassword == null) { // Vérification que le champs de mot de passe n'est pas vide.
                retour("l'ancien mot de passe n'est pas renseigné (obligatoire pour valider toute modification)");
            }
            // verification du mot de passe avec celui dans la base de donnée
            elseif (md5($local_actuelpassword) != ($infoclient[0]['motdepasse'])) {
                retour("mot de passe actuel incorrect.");
            }
            // si le mot de passe est juste on enregistre dans un premier temps les informations vérifié
            // le client n'est pas obligé de changer son mot de passe pour enregistrer les premières informations modifié
            elseif (($local_password == null) && (md5($local_actuelpassword) == ($infoclient[0]['motdepasse']))) {
                $query = $conn1->prepare("UPDATE client SET nomclient=:nomclient, prenomclient=:prenomclient, email=:email, genre=:genre, nomutilisateur=:utilisateur WHERE  idclient=:idclient;");
                $query->bindValue(':nomclient', $local_nomClient, PDO::PARAM_STR);
                $query->bindValue(':prenomclient', $local_prenomClient, PDO::PARAM_STR);
                $query->bindValue(':email', $local_adresseMail, PDO::PARAM_STR);
                $query->bindValue(':genre', $local_genre, PDO::PARAM_STR);
                $query->bindValue(':utilisateur', $local_nomUtilisateur, PDO::PARAM_STR);
                $query->bindValue(':idclient', $local_idclient, PDO::PARAM_STR);
                $query->execute();
                $_SESSION['nom'] = $local_nomClient;
                $_SESSION['prenom'] = $local_prenomClient;
                $_SESSION['nomutilisateur'] = $local_nomUtilisateur;
                retour("les informations modifié ont été enregistré avec succès.");
            }
            // vérifie si le nouveau mot de passe et le mot de passe de confirmation sont les mêmes
            elseif ($local_password != $local_confirmpassword) {
                retour("Les mots de passe ne correspondent pas, veuillez réessayer.");
            }
            // vérifie que le nouveau mot de passe respect les conditions necessaire pour être efficace
            elseif (strlen($local_password) < 8) {
                retour("Le mot de passe doit contenir au moins 8 caractères.");
            } elseif (preg_match("/[0-9]/", $local_password) == 0) {
                retour("Le mot de passe doit contenir au moins un chiffre");
            } elseif (preg_match("/[A-Z]/", $local_password) == 0) {
                retour("Le mot de passe doit contenir au moins une lettre majuscule");
            } elseif (preg_match("/[!@#$%^&*-]/", $local_password) == 0) {
                retour("Le mot de passe doit contenir au moins un caractère spécial");
            }
            // vérifie que le champ mot de passe actuel est renseigné
            elseif ($local_actuelpassword == null) {
                retour("mot de passe actuel non renseigné.");
            }
            // vérifie avec la base de donné si le mot de passe est le bon
            elseif (md5($local_actuelpassword) != ($infoclient[0]['motdepasse'])) {
                retour("mot de passe actuel incorrect");
            }
            // Envoi des informations à la base de données. (Le mot de passe est chiffré en MD5 durant la requête)
            else {
                $query = $conn1->prepare("UPDATE client SET nomclient=:nomclient, prenomclient=:prenomclient, email=:email, genre=:genre, nomutilisateur=:utilisateur, motdepasse=:motdepasse WHERE  idclient=:idclient;");
                $query->bindValue(':nomclient', $local_nomClient, PDO::PARAM_STR);
                $query->bindValue(':prenomclient', $local_prenomClient, PDO::PARAM_STR);
                $query->bindValue(':email', $local_adresseMail, PDO::PARAM_STR);
                $query->bindValue(':genre', $local_genre, PDO::PARAM_STR);
                $query->bindValue(':utilisateur', $local_nomUtilisateur, PDO::PARAM_STR);
                $query->bindValue(':idclient', $local_idclient, PDO::PARAM_STR);
                $query->bindvalue(':motdepasse', md5($local_password), PDO::PARAM_STR);
                $query->execute();
                $_SESSION['nom'] = $local_nomClient;
                $_SESSION['prenom'] = $local_prenomClient;
                $_SESSION['nomutilisateur'] = $local_nomUtilisateur;
                retour("les nouvelles informations ont bien été enregistré");
            }
        }
    }
    // boutton retour sur les informations clients
    ?>
    <div class='film'>
        <a href="/client.php"><button>retour</button></a>
    </div>

</body>