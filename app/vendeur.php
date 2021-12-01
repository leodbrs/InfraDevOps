<?php
if (!isset($_SESSION)) {
    session_start();
}
// Vérification de l'état VENDEUR dans la session
if ($_SESSION['vendeur'] == 0) { // Si non vendeur, alors redirection index.php
    header('Location: /');
} elseif ($_SESSION['vendeur'] == 1) {
    function listerClients()                                                                // ########################### fonction pour lister les clients ###########################
    {
        echo "<h3>Informations clients</h3>";
        global $conn1;
        $query = $conn1->prepare("SELECT * FROM client;");
        $query->execute();

        $listeClient = $query->fetchAll();

        echo "<table>
                    <tr>
                        <td>Nom</td>
                        <td>Prénom</td>
                        <td>Email</td>
                        <td>Date de naissance</td>
                        <td>Genre</td>
                        <td>Nom d'utilisateur</td>
                        <td>Ville</td>
                    </tr>";
        foreach ($listeClient as $ligne) { // recupère nom de la ville du client
            $query = $conn1->prepare("SELECT nomville FROM ville WHERE idville=:idville;");
            $query->bindValue(':idville', $ligne["refville"], PDO::PARAM_INT);
            $query->execute();

            $nomVille = $query->fetchAll();

            $ligne["ville"] = $nomVille[0]['nomville'];
            echo "<tr class='liste'><td> " . $ligne["nomclient"] . "</td><td> " . $ligne["prenomclient"] .
                "</td><td> " . $ligne["email"] . "</td><td> " . $ligne["datenaissance"] .
                "</td><td>" . $ligne["genre"] . "</td><td> " . $ligne["nomutilisateur"] .
                "</td><td> " . $ligne["ville"] . "</td>
                <td><button id='client_edit' value='" . $ligne['idclient'] . "' href='#' onclick='menuVendeur(this)'>Modifier</button></td>
                <td><button id='client_delete'  value='" . $ligne['idclient'] . "' href='#' onclick='menuVendeur(this)'>Supprimer</button></td></tr>\n";
        }
        echo "</table>";
    }
    function listerLocations()                                                                // ########################### fonction pour lister les locations ###########################
    {
        echo "<h3>Informations locations client</h3>";
        // Récupère info location
        global $conn1;
        $query = $conn1->prepare("SELECT * FROM location;");
        $query->execute();

        $listeLocations = $query->fetchAll();

        echo "<table>
                    <tr>
                        <td>Durée</td>
                        <td>Date de location</td>
                        <td>Client</td>
                        <td>Titre du film</td>
                        <td>Exemplaire n°</td>
                    </tr>";

        // Création des lignes du tableau en fonction du nombre de location
        foreach ($listeLocations as $ligne) { // recupère nom et prénom du client de la location
            // Récupère nom et prénom d'un client
            $query = $conn1->prepare("SELECT nomclient, prenomclient FROM client WHERE idclient=:idclient;");
            $query->bindValue(':idclient', $ligne["refclient"], PDO::PARAM_INT);
            $query->execute();

            $infoClient = $query->fetchAll();
            $ligne["client"] = $infoClient[0]['nomclient'] . " " . $infoClient[0]['prenomclient'];

            // Récupère reffilm d'un exemplaire
            $query = $conn1->prepare("SELECT reffilm FROM exemplaire WHERE idexemplaire = :refexemplaire");
            $query->bindValue(':refexemplaire', $ligne['refexemplaire'], PDO::PARAM_INT);
            $query->execute();
            $infoExemplaire = $query->fetchAll();
            $ligne["reffilm"] = $infoExemplaire[0]['reffilm'];

            // Récupère titre d'un film lié à un exemplaire
            $query = $conn1->prepare("SELECT titrefilm FROM film INNER JOIN exemplaire e on film.idfilm = e.reffilm WHERE reffilm = :reffilm");
            $query->bindValue(':reffilm', $ligne['reffilm'], PDO::PARAM_INT);
            $query->execute();
            $infoFilm = $query->fetchAll();
            $ligne["titrefilm"] = $infoFilm[0]['titrefilm'];

            echo "<tr class='liste'><td> " . $ligne["nbjours"] . " Jours</td><td> " . $ligne["dateloc"] .
                "</td><td> " . $ligne["client"] . "</td><td> " . $ligne["titrefilm"] .
                "</td><td> " . $ligne["refexemplaire"] .
                "</td>
                <td><button id='location_edit' value='" . $ligne['idloc'] . "' href='#' onclick='menuVendeur(this)'>Modifier</button></td>
                <td><button id='location_delete'  value='" . $ligne['idloc'] . "' href='#' onclick='menuVendeur(this)'>Supprimer</button></td></tr>\n";
        }
        echo "</table><h3 id='message'></h3>";
    }
    function listerFilms()                                                                      // ########################### fonction pour lister les films ###########################
    {
        echo "<h3>Lister et éditer les films</h3>";
        // Récupère info film
        global $conn1;
        $query = $conn1->prepare("SELECT * FROM film");
        $query->execute();
        $liste = $query->fetchAll();

        echo "<table>
                    <tr>
                        <td>Titre</td>
                        <td>fichier image</td>
                        <td>Nombre d'exemplaire</td>
                    </tr>";
        // Création dune ligne d'un tableau
        foreach ($liste as $ligne) { // recupère idexemplaire du film
            $query = $conn1->prepare("SELECT idexemplaire FROM exemplaire inner join film f on f.idfilm = exemplaire.reffilm WHERE idfilm = :idfilm");
            $query->bindValue(':idfilm', $ligne["idfilm"], PDO::PARAM_INT);
            $query->execute();
            $nbExemplaire = count($query->fetchAll());

            echo "<tr class='liste'><td> " . $ligne["titrefilm"] . "</td><td> " . $ligne["image"] . "</td><td> " . $nbExemplaire . "</td>
            <td><button id='film_edit' value='" . $ligne['idfilm'] . "' href='#' onclick='menuVendeur(this)'>Modifier</button></td>
            <td><button id='film_delete'  value='" . $ligne['idfilm'] . "' href='#' onclick='menuVendeur(this)'>Supprimer</button></td></tr>\n";
        }
        echo "</table>";
    }
    function listerGenres()                                                                      // ########################### fonction pour lister les genres ###########################
    {
        echo "<h3>Lister et éditer les genres</h3>";
        // Récupère info genre
        global $conn1;
        $query = $conn1->prepare("SELECT * FROM genre;");
        $query->execute();

        $liste = $query->fetchAll();
        echo "<table>
                    <tr>
                        <td>Genre</td>
                    </tr>";
        // Création dune ligne de tableau
        foreach ($liste as $ligne) {
            echo "<tr class='liste'><td> " . $ligne["nomgenre"] . "</td>
            <td><button id='genre_edit' value='" . $ligne['idgenre'] . "' href='#' onclick='menuVendeur(this)'>Modifier</button></td>
            <td><button id='genre_delete'  value='" . $ligne['idgenre'] . "' href='#' onclick='menuVendeur(this)'>Supprimer</button></td></tr>\n";
        }
        echo "</table>";
    }
    // Detection si un menu est appelé
    if (isset($_GET['menu'])) {
        require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/connexionBDD.php");
        $conn1 = connexionBDD();
        $menu = $_GET['menu'];
        if (isset($_GET['id'])) { // Si défini, récupération de l'ID de l'objet à traiter
            $id = $_GET['id'];
        }
        // Detection menu client_list
        if (strpos($menu, 'client_list') !== false) {                                           // ########################### Lister les clients ###########################
            listerClients();
            // Detection menu client_edit
        } elseif (strpos($menu, 'client_edit') !== false) {
            // Detection menu client_edit_confirm
            if (strpos($menu, 'client_edit_confirm') !== false) {                               // ########################### Execution de la modification du client ###########################

                if (isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['date_naissance'], $_POST['genre'], $_POST['nom_utilisateur'], $_POST['ville'])) { // ########################### Execution de la modification du client ###########################

                    $local_nom = $_POST['nom'];
                    $local_prenom = $_POST['prenom'];
                    $local_email = $_POST['email'];
                    $local_dateNaissance = $_POST['date_naissance'];
                    $local_genre = $_POST['genre'];
                    $local_nomutilisateur = $_POST['nom_utilisateur'];
                    $local_ville = $_POST['ville'];

                    // Definition de la requete pour modifier le client
                    $query = $conn1->prepare("UPDATE client SET nomclient =:nom, prenomclient=:prenom, email=:email, datenaissance=:datenaissance, genre=:genre, nomutilisateur=:username, refville=:ville WHERE idclient=:idclient;");
                    $query->bindValue(':nom', $local_nom, PDO::PARAM_STR);
                    $query->bindValue(':prenom', $local_prenom, PDO::PARAM_STR);
                    $query->bindValue(':email', $local_email, PDO::PARAM_STR);
                    $query->bindValue(':datenaissance', $local_dateNaissance, PDO::PARAM_STR);
                    $query->bindValue(':genre', $local_genre, PDO::PARAM_STR);
                    $query->bindValue(':username', $local_nomutilisateur, PDO::PARAM_STR);
                    $query->bindValue(':ville', $local_ville, PDO::PARAM_INT);
                    $query->bindValue(':idclient', $id, PDO::PARAM_INT);

                    if ($query->execute()) {
                        echo 1;
                    } else {
                        echo 2;
                    }
                }
            } else {                                                                                // ########################### Modifier un client ###########################
                // Récupère la date actuel
                $maxDate = date("Y-m-d");

                // Récupère info client
                $query = $conn1->prepare("SELECT nomclient, prenomclient, email, datenaissance, genre, nomutilisateur, refville from client where idclient=:idclient;");
                $query->bindValue(':idclient', $id, PDO::PARAM_INT);
                $query->execute();

                $infoClient = $query->fetchAll();

                $listeGenre = array("Homme", "Femme", "Autre");

                // Création menu déroulant des genres avec genre actuel selected
                $genre = "";
                foreach ($listeGenre as $ligne) {
                    $genre = $genre .  "<option value='" . $ligne . "'";
                    if ((isset($infoClient[0]['genre'])) && $infoClient[0]['genre'] == $ligne) {
                        $genre = $genre .  " selected";
                    }
                    $genre = $genre . ">" . $ligne . "</option>";
                }
                $genre = $genre .  "</select>";

                // Récupère code postal d'un ville
                $query = $conn1->prepare("SELECT codepostal from ville where idville=:refville;");
                $query->bindValue(':refville', $infoClient[0]['refville'], PDO::PARAM_INT);
                $query->execute();
                $codePostal = $query->fetchAll();

                // Récupère idville et nomville d'un code postal
                $query = $conn1->prepare("SELECT idville, nomville from ville where codepostal=:codepostal;");
                $query->bindValue(':codepostal', $codePostal[0]['codepostal'], PDO::PARAM_INT);
                $query->execute();

                $listeVille = $query->fetchAll();
                $ville = "";

                // Récupère Création menu déroulant des villes
                foreach ($listeVille as $ligne) {
                    $ville = $ville .  "<option value='" . $ligne['idville'] . "'";
                    if ((isset($infoClient[0]['refville'])) && $infoClient[0]['refville'] == $ligne['idville']) {
                        $ville = $ville .  " selected";
                    }
                    $ville = $ville . ">" . $ligne['nomville'] . "</option>";
                }
                $ville = $ville .  "</select>";

                // Affiche le menu
                echo "<form method='POST' enctype='multipart/form-data'>
                    <table>
                        <h3>Informations personnelles :</h3>
                        <tr>
                            <td>Nom, prénom :</td>
                            <td><input type='text' name='nom' id='nom' placeholder='Votre nom' size='24' value='" . $infoClient[0]['nomclient'] . "'></td>
                            <td><input type='text' name='prenom' id='prenom' placeholder='Votre prénom' size='24' value='" . $infoClient[0]['prenomclient'] . "'></td>
                        </tr>
                        <tr>
                            <td>Email :</td>
                            <td><input type='email' name='email' id='email' size='24' value='" . $infoClient[0]['email'] . "'></td>
                        </tr>
                        <tr>
                            <td>Date de naissance :</td>
                            <td><input type='date' name='date_naissance' id='date_naissance' min='1900-01-01' max='" . $maxDate . "' value='" . $infoClient[0]['datenaissance'] . "' style='width:195px'></td>
                            <td>Genre : <select name='genre' id='genre'>
                                    <option value=''>Choisir un genre...</option>" . $genre . "
                                </select></td>
                            </tr>
                        <tr>
                            <td>Code postal :</td>
                            <td><input type='text' name='code_postal' id='code_postal' placeholder='Votre code postal' size='24' minlength='5' maxlength='5' value='" . $codePostal[0]['codepostal'] . "' onkeyup='compteCar()'></td>
                            <td><select class='selectformat' name='ville' id='ville'>
                                    <option value=''>Choisir une ville...</option>" . $ville . "
                                </select></td>
                        </tr>
                        <tr>
                            <td>Nom d'utilisateur : </td>
                            <td><input type='text' name='username' id='username' placeholder='Votre nom d&apos;utilisateur' size='24' value='" . $infoClient[0]['nomutilisateur'] . "'></td>
                        </tr>
                        </table>
                </form>
                <button class='margin' id='client_list' onclick='menuVendeur(this)'>Annuler</button>
                <button class='margin' id='client_edit_confirm' value='" . $id . "' onclick='modifierClient()'>Modifier</button>
                <h3 id='message'></h3>";
            }
            // Detection menu client_delete
        } elseif (strpos($menu, 'client_delete') !== false) {
            // Detection menu client_delete_confirm
            if (strpos($menu, 'client_delete_confirm') !== false) {                               // ########################### Execution de la suppression du client ###########################
                // Prepare la requete de suppression du film dans la base
                $query = $conn1->prepare("DELETE FROM client WHERE idclient =:id;");
                $query->bindValue(':id', $id, PDO::PARAM_INT);

                // Signaler au vendeur si la suppresion à fonctionné
                if ($query->execute()) {
                    listerClients();
                    echo "<h3>Le client a bien été supprimé</h3>";
                } else {
                    listerClients();
                    echo "<h3>Le client n'a pas été supprimé</h3>";
                }
            } else {                                                                            // ########################### Supprimer un client ###########################
                // Recupere nom et prenom d'un client
                $query = $conn1->prepare("SELECT nomclient, prenomclient FROM client WHERE idclient =:id;");
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();
                $nomPrenom = $query->fetchAll();

                // Affiche menu
                echo "<h3>Êtes-vous sûr de vouloir supprimer le film \"" . $nomPrenom[0]['nomclient'] . " " . $nomPrenom[0]['prenomclient'] . "\" ?</h3>
                    <button class='margin' id='client_list' href='#' onclick='menuVendeur(this)'>Non</button>
                    <button class='margin' id='client_delete_confirm' value='" . $id . "' href='#' onclick='menuVendeur(this)'>Oui</button>";
            }
            // Detection menu location_list
        } elseif (strpos($menu, 'location_list') !== false) {
            listerLocations();
            // Detection menu location_edit
        } elseif (strpos($menu, 'location_edit') !== false) {                                    // ########################### Modifier une location ###########################
            // Detection menu location_edit_confirm
            if (strpos($menu, 'location_edit_confirm') !== false) {
                if (isset($_POST['nbjours'], $_POST['dateloc'], $_POST['client'], $_POST['film'], $_POST['oldexemplaire'])) { // ########################### Execution de la modification de la location ###########################

                    $local_nbjours = $_POST['nbjours'];
                    $local_dateloc = $_POST['dateloc'];
                    $local_client = $_POST['client'];
                    $local_film = $_POST['film'];
                    $local_oldexemplaire = $_POST['oldexemplaire'];

                    // Recupere le premier idexemplaire d'un film
                    $query = $conn1->prepare("SELECT idexemplaire FROM exemplaire WHERE reffilm =:reffilm AND dispo=true LIMIT 1 OFFSET 0;");
                    $query->bindValue(':reffilm', $local_film, PDO::PARAM_STR);
                    $query->execute();

                    $local_exemplaire = $query->fetchAll();
                    // Si un exemplaire est disponible pour le film
                    if (isset($local_exemplaire[0]['idexemplaire'])) {
                        // Modification de la location avec le nouvel exemplaire
                        $query = $conn1->prepare("UPDATE location SET nbjours =:nbjours, dateloc=:dateloc::date, refclient=:refclient, refexemplaire=:refexemplaire WHERE idloc=:idloc;");
                        $query->bindValue(':nbjours', $local_nbjours, PDO::PARAM_STR);
                        $query->bindValue(':dateloc', $local_dateloc, PDO::PARAM_STR);
                        $query->bindValue(':refclient', $local_client, PDO::PARAM_INT);
                        $query->bindValue(':refexemplaire', $local_exemplaire[0]['idexemplaire'], PDO::PARAM_INT);
                        $query->bindValue(':idloc', $id, PDO::PARAM_INT);

                        // Changement d'état du nouvel exemplaire à indisponible et l'ancien état à disponible
                        if ($query->execute()) {
                            $query = $conn1->prepare("UPDATE exemplaire SET dispo = true WHERE idexemplaire = :idexemplaire;");
                            $query->bindValue(':idexemplaire', $local_oldexemplaire, PDO::PARAM_STR);
                            $query->execute();

                            $query = $conn1->prepare("UPDATE exemplaire SET dispo = false WHERE idexemplaire = :idexemplaire;");
                            $query->bindValue(':idexemplaire', $local_exemplaire[0]['idexemplaire'], PDO::PARAM_STR);
                            $query->execute();
                            echo 1;
                        } else {
                            echo 2;
                        }
                    } else {
                        echo 3;
                    }
                }
                // Si aucun menu alors 
            } else {
                // Recupere date actuel
                $maxDate = date("Y-m-d");

                // Recupere info location
                $query = $conn1->prepare("SELECT * from location WHERE idloc=:idloc;");
                $query->bindValue(':idloc', $id, PDO::PARAM_INT);
                $query->execute();
                $infoLocation = $query->fetchAll();

                // Recupere info client
                $query = $conn1->prepare("SELECT idclient, nomclient, prenomclient from client");
                $query->execute();
                $infoClient = $query->fetchAll();

                // Recupere reffilm dans exemplaire
                $query = $conn1->prepare("SELECT reffilm FROM exemplaire WHERE idexemplaire = :refexemplaire");
                $query->bindValue(':refexemplaire', $infoLocation[0]['refexemplaire'], PDO::PARAM_INT);
                $query->execute();
                $infoExemplaire = $query->fetchAll();

                // Recupere idfilm et titrefilm dans film lié à un reffilm dans exemplaire
                $query = $conn1->prepare("SELECT DISTINCT idfilm, titrefilm FROM film inner join exemplaire e on film.idfilm = e.reffilm WHERE dispo = true");
                $query->execute();
                $infoFilm = $query->fetchAll();

                $client = "";
                // Creation menu déroulant client
                foreach ($infoClient as $ligne) {
                    $client = $client . "<option value='" . $ligne['idclient'] . "'";
                    if ($infoLocation[0]['refclient'] == $ligne['idclient']) {
                        $client = $client .  " selected";
                    }
                    $client = $client . ">" . $ligne['nomclient'] . " " . $ligne['prenomclient'] . "</option>";
                }
                $client = $client .  "</select>";

                $film = "";
                foreach ($infoFilm as $ligne) {
                    $film = $film . "<option value='" . $ligne['idfilm'] . "'" . ">" . $ligne['titrefilm'] . "</option>";
                }
                $film = $film .  "</select>";

                // Affiche menu
                echo "<form method='POST' enctype='multipart/form-data'>
                    <table>
                        <h3>Modifier location :</h3>
                        <tr>
                            <td>Durée de la location :</td>
                            <td><input type='text' name='nbjours' id='nbjours' size='37' value='" . $infoLocation[0]['nbjours'] . "'></td>
                        </tr>
                        <tr>
                            <td>Date de début de la location :</td>
                            <td><input type='date' name='dateloc' id='dateloc' min='1900-01-01' max='" . $maxDate . "' value='" . $infoLocation[0]['dateloc'] . "' style='width: 295px;'></td>
                        </tr>
                        <tr>
                            <td>Nom du client :</td>
                            <td><select class='selectformat' name='client' id='client'>
                                    <option value=''>Choisir un client...</option>" . $client . "
                                </select></td>
                        </tr>
                        <tr>
                            <td>Film disponible : </td>
                            <td><select class='selectformat' name='film' id='film'>
                                    <option value=''>Choisir un film...</option>" . $film . "
                                </select></td>
                        </tr>
                        </table>
                </form>
                <button class='margin' id='location_list' onclick='menuVendeur(this)'>Annuler</button>
                <button class='margin' id='location_edit_confirm' value='" . $id . "' onclick='modifierLocation()'>Modifier</button>
                        <h3 id='message'></h3>
                        <input type='hidden' name='oldexemplaire' id='oldexemplaire' value='" . $infoLocation[0]['refexemplaire'] . "'>";
            }
            // Detection menu location_delete
        } elseif ((strpos($menu, 'location_delete') !== false)) {                             // ########################### Execution de la suppression d'une location ###########################

            // Detection menu location_delete_confirm
            if (strpos($menu, 'location_delete_confirm') !== false) {
                // Preparation de la suppresion d'une location
                $query = $conn1->prepare("DELETE FROM location WHERE idloc = :id;");
                $query->bindValue(':id', $id, PDO::PARAM_INT);

                // Signal au vendeur si le suppresion à fonctionné
                if ($query->execute()) {
                    listerLocations();
                    echo "<h3>La location a bien été supprimé</h3>";
                } else {
                    listerLocations();
                    echo "<h3>La location n'a pas été supprimée</h3>";
                }
            } else {
                // Affichage menu
                echo "<h3>Êtes-vous sûr de vouloir supprimer la location ?</h3>
                <button class='margin' id='location_list' href='#' onclick='menuVendeur(this)'>Non</button>
                <button class='margin' id='location_delete_confirm' value='" . $id . "' href='#' onclick='menuVendeur(this)'>Oui</button>";
            }
            // Detection menu film_list
        } elseif (strpos($menu, 'film_list') !== false) {                                       // ########################### Lister les films ###########################
            listerFilms();
            // Detection film_edit
        } elseif (strpos($menu, 'film_edit') !== false) {
            if (strpos($menu, 'film_edit_confirm') !== false) {
                if (isset($_POST['titre'], $_POST['description'], $_POST['localfilmCover'])) { // ########################### Execution de la modification du film ###########################
                    $local_titre = pg_escape_string($_POST['titre']);
                    $local_description = pg_escape_string($_POST['description']);
                    $local_filmCover = pg_escape_string($_POST['localfilmCover']);
                    // Préparation de la requete de modification d'un film
                    $query = $conn1->prepare("UPDATE film SET titrefilm =:titrefilm, description =:description, image =:image WHERE idfilm =:idfilm;");
                    $query->bindValue(':titrefilm', $local_titre, PDO::PARAM_STR);
                    $query->bindValue(':description', $local_description, PDO::PARAM_STR);
                    $query->bindValue(':image', $local_filmCover, PDO::PARAM_STR);
                    $query->bindValue(':idfilm', $id, PDO::PARAM_INT);

                    // Renvoi une valeur que le JavaScript interprete comme erreur(0) ou success(1)
                    if ($query->execute()) {
                        echo 1;
                    } else {
                        echo 2;
                    }
                }
                // Si le menu n'est pas celui de confirmation alors on affiche le menu pour modifier un film
            } else {                                                                        // ########################### Modifier un film ###########################
                // Recupere titre, description et image d'un film
                $query = $conn1->prepare("SELECT titrefilm, description, image FROM film WHERE idfilm=:idfilm;");
                $query->bindValue(":idfilm", $id, PDO::PARAM_INT);
                $query->execute();

                $liste = $query->fetchAll();
                $extension = array("apng", "bmp", "gif", "ico", "cur", "jpg", "jpeg", "jfif", "pjpeg", "pjp", "png", "svg", "tif", "tiff", "webp");
                $listeImage = array();
                // Recupere titre, description et image d'un film
                $directory = new DirectoryIterator($_SERVER['DOCUMENT_ROOT'] . "/upload");

                // Recupere nom fichier des images
                foreach ($directory as $fileinfo) {
                    if ($fileinfo->isFile()) {
                        $imageExtension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                        if (in_array($imageExtension, $extension)) {
                            $listeImage[] = $fileinfo->getFilename();
                        }
                    }
                }
                // Trie les images par ordre alphabétique
                natcasesort($listeImage);
                echo "<h3>Edition de film</h3>
                <form method='POST' enctype='multipart/form-data'>
                    <table>
                        <tr>
                            <td>Titre : </td>
                            <td><textarea name='titre' id='titre' cols='70' rows='1'>" . $liste[0]['titrefilm'] . "</textarea></td>
                        </tr>
                        <tr>
                        <td>Description : </td>
                        <td><textarea name='description' id='description' cols='70' rows='15'>" . $liste[0]['description'] . "</textarea></td>
                        <td><img id='coverFilm' src='/upload/" . $liste[0]['image'] . "' alt='Image non chargée' style='height: 244px; width: 172px;'></td>
                        </tr>
                        <tr>
                            <td>Cover du film : </td>
                            <td><select name='localfilmCover' id='localfilmCover' onchange='changeImage(this.value)'>";
                // Création du menu déroulant d'image
                foreach ($listeImage as $ligne) {
                    echo "<option value='" . $ligne . "'";
                    if ($liste[0]['image'] == $ligne) {
                        echo " selected";
                    }
                    echo ">" . $ligne;
                    echo "</option>";
                }
                echo "</select></td>
                        </tr>
                    </table>
                </form>
                <button class='margin' id='film_list' onclick='menuVendeur(this)'>Annuler</button>
                <button class='margin' id='film_edit_confirm' value='" . $id . "' onclick='modifierFilm()'>Modifier</button>
                <h3 id='message'></h3>";
            }
            // Detection menu film_delete
        } elseif (strpos($menu, 'film_delete') !== false) {
            // Detection menu film_delete_confirm
            if (strpos($menu, 'film_delete_confirm') !== false) {                               // ########################### Execution de la suppression du film ###########################
                // Preparation des trois requetes de suppresion du au fait qu'un film est lié à d'autre table
                $query = $conn1->prepare("DELETE FROM appartenir WHERE reffilm = :id;");
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query2 = $conn1->prepare("DELETE FROM exemplaire WHERE reffilm = :id;");
                $query2->bindValue(':id', $id, PDO::PARAM_INT);
                $query3 = $conn1->prepare("DELETE FROM film WHERE idfilm = :id;");
                $query3->bindValue(':id', $id, PDO::PARAM_INT);

                // Affiche au vendeur si les trois actions on bien eu lieu
                if ($query->execute() and $query2->execute() and $query3->execute()) {
                    listerFilms();
                    echo "<h3>Le film a bien été supprimé</h3>";
                } else {
                    listerFilms();
                    echo "<h3>Le film n'a pas été supprimé</h3>";
                }
            } else {                                                                            // ########################### Supprimer un film ###########################
                // Recupere titre dun film dans film
                $query = $conn1->prepare("SELECT titrefilm FROM film WHERE idfilm =:idfilm");
                $query->bindValue(':idfilm', $id, PDO::PARAM_INT);
                $query->execute();

                $titrefilm = $query->fetchAll();
                // Afficher menu demande confirmation
                echo "<h3>Êtes-vous sûr de vouloir supprimer le film \"" . $titrefilm[0]['titrefilm'] . "\" ?</h3>
                    <button class='margin' id='film_list' href='#' onclick='menuVendeur(this)'>Non</button>
                    <button class='margin' id='film_delete_confirm' value='" . $id . "' href='#' onclick='menuVendeur(this)'>Oui</button>";
            }
            // Detection menu film_add
        } elseif (strpos($menu, 'film_add') !== false) {                                        // ########################### Execution de l'ajout du film ###########################
            if ((isset($_FILES['film_cover']['name'])) or (isset($_POST['localfilm_cover']))) {
                // Si le vendeur souhaite poster une image
                if (isset($_FILES['film_cover']['name'])) {
                    $imageName = $_FILES['film_cover']['name'];
                    $targetImage = $_SERVER['DOCUMENT_ROOT'] . "RT/1projet18/site/upload/" . basename($_FILES["film_cover"]["name"]);
                    $imageExtension = strtolower(pathinfo($targetImage, PATHINFO_EXTENSION));
                    $extension = array("apng", "bmp", "gif", "ico", "cur", "jpg", "jpeg", "jfif", "pjpeg", "pjp", "png", "svg", "tif", "tiff", "webp");

                    // Si limage respecte les extensions autorisé
                    if (in_array(strtolower($imageExtension), $extension)) {
                        if (!file_exists($targetImage)) {
                            move_uploaded_file($_FILES['film_cover']['tmp_name'], $targetImage);
                        }
                        if (isset($_POST['titre'], $_POST['description'])) {
                            $local_titre = pg_escape_string($_POST['titre']);
                            $local_description = pg_escape_string($_POST['description']);
                            // Insertion dans la base du film
                            $query = $conn1->prepare("INSERT INTO film (titrefilm, description, image) VALUES (:titrefilm, :description, :image) RETURNING idfilm;");
                            $query->bindValue(':titrefilm', $local_titre, PDO::PARAM_STR);
                            $query->bindValue(':description', $local_description, PDO::PARAM_STR);
                            $query->bindValue(':image', $imageName, PDO::PARAM_STR);
                            $query->execute();

                            $idFilm = $query->fetchAll()[0]['idfilm'];

                            // Si le nom du film est bien trouvé alors le signal au vendeur
                            $query = $conn1->prepare("SELECT titrefilm FROM film WHERE idfilm=:idfilm");
                            $query->bindValue(':idfilm', $idFilm, PDO::PARAM_INT);
                            $query->execute();

                            if (count(($query->fetchAll())) == 1) {
                                echo 1;
                            } else {
                                echo 2;
                            }
                        }
                    }
                } else {
                    // Si le vendeur utilise une image deja en ligne alors
                    if (isset($_POST['titre'], $_POST['description'], $_POST['localfilm_cover'])) {
                        $local_titre = pg_escape_string($_POST['titre']);
                        $local_description = pg_escape_string($_POST['description']);
                        $local_filmCover = pg_escape_string($_POST['localfilm_cover']);
                        // Insertion dans la base du film
                        $query = $conn1->prepare("INSERT INTO film (titrefilm, description, image) VALUES (:titrefilm, :description, :image) RETURNING idfilm;");
                        $query->bindValue(':titrefilm', $local_titre, PDO::PARAM_STR);
                        $query->bindValue(':description', $local_description, PDO::PARAM_STR);
                        $query->bindValue(':image', $local_filmCover, PDO::PARAM_STR);
                        $query->execute();

                        $idFilm = $query->fetchAll()[0]['idfilm'];

                        // Si le nom du film est bien trouvé alors le signal au vendeur
                        $query = $conn1->prepare("SELECT titrefilm FROM film WHERE idfilm=:idfilm");
                        $query->bindValue(':idfilm', $idFilm, PDO::PARAM_INT);
                        $query->execute();

                        if (count(($query->fetchAll())) == 1) {
                            echo 1;
                        } else {
                            echo 2;
                        }
                    }
                }
            } else {                                                                            // ########################### Ajouter un film ###########################
                // Affichage du menu de modification
                $extension = array("apng", "bmp", "gif", "ico", "cur", "jpg", "jpeg", "jfif", "pjpeg", "pjp", "png", "svg", "tif", "tiff", "webp");
                $listeImage = array();
                $directory = new DirectoryIterator($_SERVER['DOCUMENT_ROOT'] . "/upload");

                // Recupere la liste des images
                foreach ($directory as $fileinfo) {
                    if ($fileinfo->isFile()) {
                        $imageExtension = strtolower(pathinfo($fileinfo->getFilename(), PATHINFO_EXTENSION));
                        if (in_array($imageExtension, $extension)) {
                            $listeImage[] = $fileinfo->getFilename();
                        }
                    }
                }
                // Affichage du menu
                echo "<h3>Ajouter un film</h3>
                    <form method=' POST' enctype='multipart/form-data'>
                        <table>
                            <tr>
                                <td>Titre : </td>
                                <td><textarea name='titre' id='titre' cols='70' rows='1'></textarea></td>
                            </tr>
                            <tr>
                                <td>Description : </td>
                                <td><textarea name='description' id='description' cols='70' rows='15'></textarea></td>
                                <td><img id='coverFilm' src='/assets/img/blanc.png' alt='' style='height: 244px; width: 172px;'></td>
                            </tr>
                            <tr>
                                <td>Cover du film : </td>
                                <td><input type='file' id='filmCover' name='filmCover' onchange='changeImage()'/><br> ou <br>
                                <select name='localfilmCover' id='localfilmCover' onchange='changeImage(this.value)'><option value='' id='defaultSelected'>Choisir...</option>";
                // Creation du menu deroulant des images
                foreach ($listeImage as $ligne) {
                    echo "<option value='" . $ligne . "'>" . $ligne . "</option>";
                }
                echo "</select></td>
                            </tr>
                            <tr>
                                <td><input type='button' value='Ajouter le film' onclick='ajouterFilm()'></td>
                            </tr>
                        </table>
                    </form>
                    <h3 id='message'></h3>";
            }
            // Detection menu film_genres
        } elseif (strpos($menu, 'film_genres') !== false) {
            // Detection menu film_genres_list
            if (strpos($menu, 'film_genres_list') !== false) {                                   // ########################### Lister des genres lié à un film pour vendeur.js ###########################
                $listeGenres = array();
                $filmGenres = array();
                $listeFilmGenres = array();

                // Recupere idgenre nomgenre
                $query = $conn1->prepare("SELECT idgenre, nomgenre FROM genre");
                $query->execute();

                // Listes des genres lié à leur id
                foreach ($query as $ligne) {
                    $listeGenres += [$ligne["idgenre"] => $ligne["nomgenre"]];
                }

                $listeFilmGenres += ["listeGenres" => $listeGenres];

                // Recupere genres d'un film
                $query = $conn1->prepare("SELECT idgenre, nomgenre FROM genre INNER JOIN appartenir a on genre.idgenre = a.refgenre WHERE reffilm = :id");
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();

                $query = $query->fetchAll();

                // Listes des genres d'un film lié à leur id
                foreach ($query as $ligne) {
                    $filmGenres += [$ligne["idgenre"] => $ligne["nomgenre"]];
                }
                $listeFilmGenres += ["filmGenres" => $filmGenres];

                // Renvoie au Js en Json
                echo json_encode($listeFilmGenres);
                // Detection menu film_genres_confirm
            } elseif (strpos($menu, 'film_genres_confirm') !== false) {                     // ########################### Execution de l'ajout des genres lié à un film ###########################
                $genre = array();

                // Recupere idgenre nomgenre des genres lié à un film
                $query = $conn1->prepare("SELECT idgenre, nomgenre FROM genre INNER JOIN appartenir a on genre.idgenre = a.refgenre WHERE reffilm = :id");
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();
                $filmGenres = $query->fetchAll();

                // Liste les genre lié à leur id
                foreach ($filmGenres as $ligne) {
                    array_push($genre, $ligne['idgenre']);
                }

                // Recupere les idgenre et nomgenre de genre
                $query = $conn1->prepare("SELECT idgenre, nomgenre FROM genre;");
                $query->execute();
                $listeGenres = $query->fetchAll();



                // Ajoute le lien ou le supprime entre le film et les genres
                foreach ($listeGenres as $ligne) {
                    if (in_array($ligne['idgenre'], $genre) and $_POST[$ligne['idgenre']] == "false") {
                        $query = $conn1->prepare("DELETE FROM appartenir WHERE reffilm =:reffilm AND refgenre =:refgenre");
                        $query->bindValue(':reffilm', $id, PDO::PARAM_INT);
                        $query->bindValue(':refgenre', $ligne['idgenre'], PDO::PARAM_INT);
                        if ($query->execute()) {
                        } else {
                            echo 2;
                        }
                    } elseif (!in_array($ligne['idgenre'], $genre) and $_POST[$ligne['idgenre']] == "true") {
                        $query = $conn1->prepare("INSERT INTO appartenir (reffilm, refgenre) VALUES (:reffilm, :refgenre);");
                        $query->bindValue(':reffilm', $id, PDO::PARAM_INT);
                        $query->bindValue(':refgenre', $ligne['idgenre'], PDO::PARAM_INT);
                        if ($query->execute()) {
                        } else {
                            echo 2;
                        }
                    }
                }
                echo 1;
                // Menu de liaison de genres à un film
            } else {                                                                         // ########################### Menu liste des genres lié à un film ###########################
                // Recupere idfilm et titre dans film
                $query = $conn1->prepare("SELECT idfilm, titrefilm FROM film;");
                $query->execute();

                $query = $query->fetchAll();

                $listeFilm = "";
                // Creation du menu deroulant des films
                foreach ($query as $ligne) {
                    $listeFilm = $listeFilm . "<option value='" . $ligne['idfilm'] . "'>" . $ligne['titrefilm'] . "</option>";
                }
                // Affiche le menu
                echo "<h3>Lier des genres à un film</h3>
                    <table>
                        <tr>
                            <td>Selectioné un film : </td>
                            <td><select name='film' id='film' onchange='listeFilmGenres()'><option value='' id='defaultSelected'>Choisir...</option>" . $listeFilm . "</td>
                            <td><button class='margin' id='film_genres_confirm' value='' onclick='modifierFilmGenres()' disabled>Valider</button></td>
                        </tr>
                    </table>
                    <table id='tableGenres'>
                    </table>
                    <h3 id='message'></h3>";
            }
            // Detection du menu genre_list
        } elseif (strpos($menu, 'genre_list') !== false) {                                         // ########################### Lister les genres ###########################
            listerGenres();
            // Detection du menu genre_edit
        } elseif (strpos($menu, 'genre_edit') !== false) {
            // Detection du menu genre_edit_confim
            if (strpos($menu, 'genre_edit_confirm') !== false) {
                if (isset($_POST['genre'])) {                                                     // ########################### Execution de la modification du genre ###########################
                    $local_genre = pg_escape_string($_POST['genre']);
                    // Prepare la requete de modification du genre
                    $query = $conn1->prepare("UPDATE genre SET nomgenre =:nomgenre WHERE idgenre =:id;");
                    $query->bindValue(":nomgenre", $local_genre, PDO::PARAM_STR);
                    $query->bindValue(":id", $id, PDO::PARAM_INT);
                    // Signal au vendeur si la modification à bien eu lieu
                    if ($query->execute()) {
                        echo 1;
                    } else {
                        echo 2;
                    }
                }
            } else {                                                                             // ########################### Modifier un genre ###########################
                // Recupere nomgenre d'un genre
                $query = $conn1->prepare("SELECT nomgenre FROM genre WHERE idgenre=:idgenre;");
                $query->bindValue(":idgenre", $id, PDO::PARAM_INT);
                $query->execute();

                $liste = $query->fetchAll();
                // Affiche le menu
                echo "<h3>Edition de genre</h3>
                <form method='POST' enctype='multipart/form-data'>
                    <table>
                        <tr>
                            <td>Genre : </td>
                            <td><input type='text' name='genre' id='genre' value='" . $liste[0]['nomgenre'] . "']'></td>
                        </tr>
                    </table>
                </form>
                <button class='margin' id='genre_list' onclick='menuVendeur(this)'>Annuler</button>
                <button class='margin' id='genre_edit_confirm' value='" . $id . "' onclick='modifierGenre()'>Modifier</button>
                <h3 id='message'></h3>";
            }
            // Detection du menu genre_delete
        } elseif (strpos($menu, 'genre_delete') !== false) {
            // Detection du menu genre_delete_confirm
            if (strpos($menu, "genre_delete_confirm") !== false) {                               // ########################### Execution de la suppression du genre ###########################
                // Prepare les deux requetes du au fait quun genre est lié au dautre table
                $query = $conn1->prepare("DELETE FROM appartenir WHERE refgenre = :id;");
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query2 = $conn1->prepare("DELETE FROM genre WHERE idgenre = :id;");
                $query2->bindValue(':id', $id, PDO::PARAM_INT);

                // Signal au vendeur si les requetes on eu lieu
                if ($query->execute() and $query2->execute()) {
                    listerGenres();
                    echo "<h3>Le genre a bien été supprimé</h3>";
                } else {
                    listerGenres();
                    echo "<h3>Le genre n'a pas été supprimé</h3>";
                }
            } else {                                                                            // ########################### Supprimer un genre ###########################
                // Recupere nomgenre dun genre
                $query = $conn1->prepare("SELECT nomgenre FROM genre WHERE idgenre=:idgenre;");
                $query->bindValue(":idgenre", $id, PDO::PARAM_INT);
                $query->execute();

                $nomgenre = $query->fetchAll();
                // Affiche le menu
                echo "<h3>Êtes-vous sûr de vouloir supprimer le genre \"" . $nomgenre[0]['nomgenre'] . "\" ?</h3>
                    <button class='margin' id='genre_list' href='#' onclick='menuVendeur(this)'>Non</button>
                    <button class='margin' id='genre_delete_confirm' value='" . $id . "' href='#' onclick='menuVendeur(this)'>Oui</button>";
            }
            // Detection du menu genre_add
        } elseif (strpos($menu, 'genre_add') !== false) {                                       // ########################### Execution de l'ajout du genre ###########################
            if (isset($_POST['genre'])) {
                $local_genre = pg_escape_string($_POST['genre']);

                // Ajoute dans la base le genre
                $query = $conn1->prepare("INSERT INTO genre (nomgenre) VALUES (:nomgenre) RETURNING idgenre;");
                $query->bindValue(":nomgenre", $local_genre, PDO::PARAM_STR);
                $query->execute();

                $idGenre = $query->fetchAll()[0]['idgenre'];

                // Verifie si le nom existe et le signal au vendeur
                $query = $conn1->prepare("SELECT nomgenre FROM genre WHERE idgenre=:idgenre");
                $query->bindValue(":idgenre", $idGenre, PDO::PARAM_INT);
                $query->execute();

                if (count($query->fetchAll()) == 1) {
                    echo 1;
                } else {
                    echo 2;
                }
            } else {                                                                            // ########################### Ajouter un genre ###########################
                // Affiche menu 
                echo "<h3>Ajouter un genre</h3>
                                <form method=' POST' enctype='multipart/form-data'>
                                    <table>
                                        <tr>
                                            <td>Genre : </td>
                                            <td><input type='text' name='genre' id='genre'></td>
                                        </tr>
                                        <tr>
                                            <td><input type='button' value='Ajouter le genre' onclick='ajouterGenre()'></td>
                                        </tr>
                                    </table>
                                </form>
                                <h3 id='message'></h3>";
            }
            // Detection menu genre_films
        } elseif (strpos($menu, 'genre_films') !== false) {
            // Detection menu genre_films_list
            if (strpos($menu, 'genre_films_list') !== false) {                                    // ########################### Lister des films lié à un genre pour vendeur.js ###########################
                $listeFilms = array();
                $genreFilms = array();
                $listeGenreFilms = array();

                // Recupere idfilm et titrefilm dans film
                $query = $conn1->prepare("SELECT idfilm, titrefilm FROM film");
                $query->execute();

                // Liste les films en les liant à leur id
                foreach ($query as $ligne) {
                    $listeFilms += [$ligne["idfilm"] => $ligne["titrefilm"]];
                }

                $listeGenreFilms += ["listeFilms" => $listeFilms];

                // Recupere idfilm et titrefilm dans appartenir lié à un genre
                $query = $conn1->prepare("SELECT idfilm, titrefilm FROM film INNER JOIN appartenir a on film.idfilm = a.reffilm WHERE refgenre = :id");
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();

                $query = $query->fetchAll();

                // Liste les films en les liant à leur id
                foreach ($query as $ligne) {
                    $genreFilms += [$ligne["idfilm"] => $ligne["titrefilm"]];
                }
                $listeGenreFilms += ["genreFilms" => $genreFilms];

                // Encode la liste en json avant de renvoyé au Js
                echo json_encode($listeGenreFilms);
                // Detection menu genre_films_confirm
            } elseif (strpos($menu, 'genre_films_confirm') !== false) {                     // ########################### Execution de l'ajout des films lié à un genre ###########################
                $film = array();

                // recupere idfilm et titrefilm dun genre
                $query = $conn1->prepare("SELECT idfilm, titrefilm FROM film INNER JOIN appartenir a on film.idfilm = a.reffilm WHERE refgenre = :id");
                $query->bindValue(':id', $id, PDO::PARAM_INT);
                $query->execute();
                $genreFilms = $query->fetchAll();

                // Ajoute a une liste le film avec son id lié
                foreach ($genreFilms as $ligne) {
                    array_push($film, $ligne['idfilm']);
                }

                // Selection id film et titrefilm dans film
                $query = $conn1->prepare("SELECT idfilm, titrefilm FROM film;");
                $query->execute();
                $listeFilms = $query->fetchAll();



                // Ajoute ou supprime un lien entre un film et des genres
                foreach ($listeFilms as $ligne) {
                    if (in_array($ligne['idfilm'], $film) and $_POST[$ligne['idfilm']] == "false") {
                        $query = $conn1->prepare("DELETE FROM appartenir WHERE reffilm =:reffilm AND refgenre =:refgenre");
                        $query->bindValue(':reffilm', $ligne['idfilm'], PDO::PARAM_INT);
                        $query->bindValue(':refgenre', $id, PDO::PARAM_INT);
                        if ($query->execute()) {
                        } else {
                            echo 2;
                        }
                    } elseif (!in_array($ligne['idfilm'], $film) and $_POST[$ligne['idfilm']] == "true") {
                        $query = $conn1->prepare("INSERT INTO appartenir (reffilm, refgenre) VALUES (:reffilm, :refgenre);");
                        $query->bindValue(':reffilm', $ligne['idfilm'], PDO::PARAM_INT);
                        $query->bindValue(':refgenre', $id, PDO::PARAM_INT);
                        if ($query->execute()) {
                        } else {
                            echo 2;
                        }
                    }
                }
                echo 1;
            } else {                                                                         // ########################### Menu liste des films lié à un genre ###########################
                // recupere idgenre et nomgenre dans genre
                $query = $conn1->prepare("SELECT idgenre, nomgenre FROM genre;");
                $query->execute();

                $query = $query->fetchAll();

                $listeGenre = "";
                // Creation menu deroulant genre
                foreach ($query as $ligne) {
                    $listeGenre = $listeGenre . "<option value='" . $ligne['idgenre'] . "'>" . $ligne['nomgenre'] . "</option>";
                }
                echo "<h3>Lier des films à</h3>
                    <table>
                        <tr>
                            <td>Selectioné un genre : </td>
                            <td><select name='genre' id='genre' onchange='listeGenreFilms()'><option value='' id='defaultSelected'>Choisir...</option>" . $listeGenre . "</td>
                            <td><button class='margin' id='genre_films_confirm' value='' onclick='modifierGenreFilms()' disabled>Valider</button></td>
                        </tr>
                    </table>
                    <table id='tableFilms'>
                    </table>
                    <h3 id='message'></h3>";
            }
            // Detection menu exemplaire
        } elseif (strpos($menu, 'exemplaire') !== false) {
            // Detection menu exemplaire_list
            if (strpos($menu, 'exemplaire_list') !== false) {
                $listeExemplaires = array();
                $filmGenres = array();

                // Recupere info dans genre dun film
                $query = $conn1->prepare("SELECT * FROM exemplaire INNER JOIN film f on f.idfilm = exemplaire.reffilm WHERE idfilm = :idfilm");
                $query->bindValue(':idfilm', $id, PDO::PARAM_INT);
                $query->execute();

                // Ajoute dans une list un exemplaire avec son etat lié
                foreach ($query as $ligne) {
                    $listeExemplaires += [$ligne["idexemplaire"] => $ligne["dispo"]];
                }
                // Si la liste nest pas vide alors on lencode en json et on lenvoie au js
                if (count($listeExemplaires)) {
                    echo json_encode($listeExemplaires);
                }
                // Detection menu exemplaire_add
            } elseif (strpos($menu, 'exemplaire_add') !== false) {
                // Preparation de lajout dun exemplaire
                $query = $conn1->prepare("INSERT INTO exemplaire (reffilm, dispo) VALUES (:idfilm, true) RETURNING idexemplaire;");
                $query->bindValue(':idfilm', $id, PDO::PARAM_INT);
                // Si lajoue a eu lieu on lencode le retoure de lid de ce nouvel exemplaire en json et on js le recupere 
                if ($query->execute()) {
                    $query = $query->fetchAll();
                    $reponse = array();
                    array_push($reponse, 1);
                    array_push($reponse, $query[0]['idexemplaire']);
                    echo json_encode($reponse);
                } else {
                    echo 2;
                }
                // Detection menu exemplaire_delete
            } elseif (strpos($menu, 'exemplaire_delete') !== false) {
                // Preparation de suppresion
                $query = $conn1->prepare("DELETE FROM exemplaire WHERE idexemplaire=:idexemplaire;");
                $query->bindValue(':idexemplaire', $id, PDO::PARAM_INT);
                // Signaler letat de la requete au vendeur
                if ($query->execute()) {
                    echo 1;
                } else {
                    echo 2;
                }
                // Detection menu vendeur exemplaire_changeetat
            } elseif (strpos($menu, 'exemplaire_changeetat') !== false) {
                // Preparation requete modification exemplaire
                $query = $conn1->prepare("UPDATE exemplaire SET dispo =:dispo WHERE idexemplaire = :idexemplaire;");
                $query->bindValue(':dispo', $_GET['etat'], PDO::PARAM_BOOL);
                $query->bindValue(':idexemplaire', $id, PDO::PARAM_INT);
                // Signal au vendeur si la modification a fonctoionné ou pas
                if ($query->execute()) {
                    echo 1;
                } else {
                    echo 2;
                }
            } else {
                // Liste les films
                $query = $conn1->prepare("SELECT idfilm, titrefilm FROM film;");
                $query->execute();

                $query = $query->fetchAll();

                $listeFilm = "";
                // Creation menu deroulant film
                foreach ($query as $ligne) {
                    $listeFilm = $listeFilm . "<option value='" . $ligne['idfilm'] . "'>" . $ligne['titrefilm'] . "</option>";
                }
                // Affiche menu
                echo "<h3>Lister et modifier les exemplaires d'un film</h3>
                    <table>
                        <tr>
                            <td>Selectioné un film : </td>
                            <td><select name='film' id='film' onchange='listFilmExemplaires()'><option id='defaultSelected' value=''>Choisir...</option>" . $listeFilm . "</td>
                            <td><button class='margin' id='exemplaire_add' value='' onclick='ajouterFilmExemplaires()' disabled>Ajouter un exemplaire</button></td>
                        </tr>
                    </table>
                    <table id='tableExemplaires'>
                    </table>
                    <h3 id='message'></h3>";
            }
        }
    } else {                                                                                    // ########################### Menu navigation de gauche ###########################
        // Affiche menu de gauche
        echo "<!DOCTYPE html>
                <html>

                <head>
                    <meta charset='utf-8'>
                    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
                    <title>DVD Rental</title>
                    <meta name='viewport' content='width=device-width, initial-scale=1'>
                    <link rel='stylesheet' type='text/css' media='screen' href='/styles/main.css'>
                    <link rel='shortcut icon' href='/assets/img/favicon.png' type='image/png'>
                    <script src='scripts/jquery-3.6.0.min.js'></script>
                    <script src='scripts/vendeur.js'></script>
                </head>

                <body class='scrollinvisible'>";
        include($_SERVER['DOCUMENT_ROOT'] . "/includes/header.php");
        echo "      <nav id='menu'>
                        <ul class='deroulant'>
                            <li>
                                <label for='menu1'>Gestions des clients</label>
                                <input id='menu1' type='checkbox' name='menu'/>
                                <ul class='sousmenu'>
                                    <li><a id='client_list' href='#' onclick='menuVendeur(this)'>Lister et modifier</a></li>
                                </ul>
                            </li>
                            <li>
                                <label for='menu2'>Gestions des locations</label>
                                <input id='menu2' type='checkbox' name='menu'/>
                                <ul class='sousmenu'>
                                    <li><a id='location_list' href='#' onclick='menuVendeur(this)'>Lister et modifier</a></li>
                                </ul>
                            </li>
                            <li>
                                <label for='menu3'>Gestions des films</label>
                                <input id='menu3' type='checkbox' name='menu'/>
                                <ul class='sousmenu'>
                                    <li><a id='film_list' href='#' onclick='menuVendeur(this)'>Lister et modifier</a></li>
                                    <li><a id='film_add' href='#' onclick='menuVendeur(this)'>Ajouter</a></li>
                                    <li><a id='film_genres' href='#' onclick='menuVendeur(this)'>Attribuer des genres</a></li>
                                </ul>
                            </li>
                            <li>
                                <label for='menu4'>Gestions des genres</label>
                                <input id='menu4' type='checkbox' name='menu'/>
                                <ul class='sousmenu'>
                                    <li><a id='genre_list' href='#' onclick='menuVendeur(this)'>Lister et modifier</a></li>
                                    <li><a id='genre_add' href='#' onclick='menuVendeur(this)'>Ajouter</a></li>
                                    <li><a id='genre_films' href='#' onclick='menuVendeur(this)'>Attribuer des films</a></li>
                                    </ul>
                            </li>
                            <li>
                                <label for='menu5'>Gestions des exemplaires</label>
                                <input id='menu5' type='checkbox' name='menu'/>
                                <ul class='sousmenu'>
                                    <li><a id='exemplaire' href='#' onclick='menuVendeur(this)'>Lister et modifier</a></li>
                                    </ul>
                            </li>
                        </ul>
                    </nav>
                    <center>
                        <section id='corps_paddingleft' class='scrolling'>
                            <h3>Interface de gestion vendeur</h3>
                            <span>Choisissez un menu sur la gauche</span>
                        </section>
                    </center>
                </body>

            </html>";
    }
}
