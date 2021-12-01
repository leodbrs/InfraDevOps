<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/connexionBDD.php");
$conn1 = connexionBDD();
function ConnecteVendeur($nomutilisateur, $motdepasse)
{
    global $conn1;

    $query = $conn1->prepare("SELECT * FROM Vendeur WHERE nomutilisateur =:nomutilisateur AND motdepasse =:motdepasse");
    $query->bindValue(':nomutilisateur', $nomutilisateur, PDO::PARAM_STR);
    $query->bindValue(':motdepasse', md5($motdepasse), PDO::PARAM_STR);
    $query->execute();

    return count($query->fetchAll());
}
function ConnecteClient($nomutilisateur, $motdepasse)
{
    global $conn1;

    $query = $conn1->prepare("SELECT * FROM Client WHERE nomutilisateur =:nomutilisateur AND motdepasse =:motdepasse");
    $query->bindValue(':nomutilisateur', $nomutilisateur, PDO::PARAM_STR);
    $query->bindValue(':motdepasse', md5($motdepasse), PDO::PARAM_STR);
    $query->execute();

    return count($query->fetchAll());
}
echo "
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>DVD Rental</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='shortcut icon' href='/assets/img/favicon.png' type='image/png'>
    <link rel='stylesheet' type='text/css' media='screen' href='/styles/main.css'>

    <script src='scripts/connexion.js'></script>
</head>

<body>";

if (isset($_POST['nom_utilisateur'])) {
    $parametre['nom_utilisateur'] = $_POST['nom_utilisateur'];
} else {
    $parametre['nom_utilisateur'] = "";
}

$menuConnexion = <<<EOT
<form method='POST' action="/connexion.php">
    <table>
        <tr>
            <td>Nom d'utilisateur : </td>
            <td><input type="text" name="nom_utilisateur" size="24" value="{$parametre['nom_utilisateur']}"></td>
            </br>
        </tr>
        <tr>
            <td>Mot de passe :</td>
            <td><input type="password" name="password" size="24" id="motdepasse"></td>
            <td><input type="checkbox" onclick="afficherPassword()"> Afficher les mots de passe</input></td>
        </tr>
        <tr>
        <td><input type="submit" value="Se connecter"></td>
        </tr>
    </table>
</form>
EOT;

include($_SERVER['DOCUMENT_ROOT'] . "/includes/header.php");
echo "<section id='corps'>
<center>";
if (isset($_SESSION['nom'], $_SESSION['prenom'], $_SESSION['nomutilisateur'])) {
    if ($_SESSION['vendeur'] == 1) {
        echo "<h3>Vous êtes connecté en tant que vendeur, " . $_SESSION['prenom'] . " !</h3>";
        echo "</center>";
    } elseif ($_SESSION['vendeur'] == 0) {
        echo "<h3>Vous êtes connecté, " . $_SESSION['prenom'] . " !</h3>";
        echo "</center>";
    }
} else {
    if (isset($_POST['nom_utilisateur'], $_POST['password'])) {
        $local_nomUtilisateur = $_POST['nom_utilisateur'];
        $local_password = $_POST['password'];

        if (ConnecteVendeur($local_nomUtilisateur, $local_password) == 1) {
            $query = $conn1->prepare("SELECT nomvendeur, prenomvendeur, idvendeur FROM vendeur WHERE nomutilisateur=:nomutilisateur");
            $query->bindValue(':nomutilisateur', $local_nomUtilisateur, PDO::PARAM_STR);
            $query->execute();
            $information = $query->fetchAll();

            $_SESSION['id'] = $information[0]['idvendeur'];
            $_SESSION['nom'] = $information[0]['nomvendeur'];
            $_SESSION['prenom'] = $information[0]['prenomvendeur'];
            $_SESSION['nomutilisateur'] = $local_nomUtilisateur;
            $_SESSION['vendeur'] = 1;
            header('Location: /connexion.php');
        } elseif (ConnecteClient($local_nomUtilisateur, $local_password) == 0) {
            echo $menuConnexion;
            echo "<h3>Nom d'utilisateur ou mot de passe incorrect</h3>";
            echo "</center>";
        } else {
            $query = $conn1->prepare("SELECT nomclient, prenomclient, idclient FROM client WHERE nomutilisateur=:nomutilisateur");
            $query->bindValue(':nomutilisateur', $local_nomUtilisateur, PDO::PARAM_STR);
            $query->execute();
            $information = $query->fetchAll();

            $_SESSION['id'] = $information[0]['idclient'];
            $_SESSION['nom'] = $information[0]['nomclient'];
            $_SESSION['prenom'] = $information[0]['prenomclient'];
            $_SESSION['nomutilisateur'] = $local_nomUtilisateur;
            $_SESSION['vendeur'] = 0;
            header('Location: /connexion.php');
        }
    } else {
        echo $menuConnexion;
        echo "</center>";
    }
}
?>
</section>
</body>

</html>