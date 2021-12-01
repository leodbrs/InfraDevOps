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

<body class='scrollinvisible'>
    <?php
    require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/connexionBDD.php");
    $conn1 = connexionBDD();

    include($_SERVER['DOCUMENT_ROOT'] . "/includes/header.php");
    ?>
    <!-------------------------------------------- MENU SELECTION D'UN GENRE -------------------------------------------------------------------------------->
    <nav id='menu' style='height: 120%;'>
        <ul class='deroulant'>
            <li>
                <label for='menu1'>Sélectionner un genre</label>
                <input id='menu1' type='checkbox' name='menu' />
                <ul class='sousmenu'>
                    <li><a href="/catalogue.php?page=1">annuler la sélection</a></li>
                    <?php
                    // récupération de tous les genre présent dans la bd
                    $sql = "SELECT * FROM genre;";
                    $query = $conn1->prepare($sql);
                    $query->execute();
                    $listeGenre = $query->fetchall();
                    //affichage des menus
                    //fait passer l'id du genre dans l'url
                    foreach ($listeGenre as $ligne) :
                    ?>
                        <li><a href="/catalogue.php?page=1&idgenre=<?= $ligne['idgenre'] ?>"><?= $ligne['nomgenre'] ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
        </ul>
    </nav>
    <section id="corps_paddingleft">
        <!------------------------------------------------------------------------------ FIN D'AFFICHAGE DU MENU DE GENRE --------------------------------------------------------->


        <?php


        // nombre de films total
        // si un genre a été sélectionné :
        if (isset($_GET['idgenre']) && !empty($_GET['idgenre'])) {
            $localgenre = $_GET['idgenre'];
            $sql = "SELECT COUNT(*) AS reffilm FROM appartenir WHERE refgenre =:genre;";
            $query = $conn1->prepare($sql);
            $query->bindValue(':genre', $localgenre, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch();
            $totalFilm = (int) $result['reffilm'];
            // echo "<pre>";
            // print_r($result);
            // echo "</pre>";
            // si aucun genre n'a été selectionné :
        } else {
            $sql = "SELECT COUNT(*) AS titrefilm FROM film;";
            $query = $conn1->prepare($sql);
            $query->execute();
            $result = $query->fetch();
            $totalFilm = (int) $result['titrefilm'];
        }
        ////////////////////////////////////////////////////////////////////////// PAGINATION ET SELECTION DES FILMS //////////////////////////////////////////////////////////////////////////////////////

        // determine sur quelle page on se trouve
        if (isset($_GET['page']) && !empty($_GET['page'])) {
            $currentPage = (int) strip_tags($_GET['page']);
        } else {
            $currentPage = 1;
        }

        // nombre de film par page + nombre de pages
        $parPage = 6;
        $pages = ceil($totalFilm / $parPage);

        // calcule du 1er film de la page
        $premier = ($currentPage * $parPage) - $parPage;

        // selection des film dans la bd
        // si un genre a été demandé :
        if (isset($_GET['idgenre']) && !empty($_GET['idgenre'])) {
            $sql = 'SELECT * FROM film INNER JOIN appartenir a on film.idfilm = a.reffilm WHERE refgenre= :genre LIMIT :parPage OFFSET :premier;';
            $query = $conn1->prepare($sql);
            $query->bindValue(':genre', $localgenre, PDO::PARAM_INT);
            $query->bindValue(':premier', $premier, PDO::PARAM_INT);
            $query->bindValue(':parPage', $parPage, PDO::PARAM_INT);
            $query->execute();
            $listFilm = $query->fetchall();

            // si aucun genre n'a été rentré 
        } else {
            $sql = 'SELECT * FROM film LIMIT :parPage OFFSET :premier;';
            $query = $conn1->prepare($sql);
            $query->bindValue(':premier', $premier, PDO::PARAM_INT);
            $query->bindValue(':parPage', $parPage, PDO::PARAM_INT);
            $query->execute();
            $listFilm = $query->fetchall();
        }

        ?>
        <?php
        ///////////////////////////////////////////////////////////// AFFICHAGE DU CATALOGUE SI UN GENRE EST SELECTIONNE ////////////////////////////////////////////////////////////////////////////////////////

        if (isset($_GET['idgenre']) && !empty($_GET['idgenre'])) {
            $localgenre = $_GET['idgenre'];
            $sql = "SELECT COUNT(*) AS reffilm FROM appartenir WHERE refgenre =:genre;";
            $query = $conn1->prepare($sql);
            $query->bindValue(':genre', $localgenre, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch();
            $film = (int) $result['reffilm'];

            // affichage si aucun film n'appartient au genre rentré :
            if ($film == 0) {
        ?>
                <center>
                    <h3>aucun film de ce genre n'est disponible</h3>
                    <div>
                        <a class="film" href="/catalogue.php?page=1"><button>retour</button></a>
                    </div>
                </center>


            <?php
            } else {
            ?>
                <div class="row">
                    <?php
                    foreach ($listFilm as $ligne) : ?>
                        <div class="card">
                            <?php echo '<img class="card img" src="/upload/' . $ligne['image'] . '" alt="Image non chargée" style="width:100%">' ?>
                            <h2><?php echo $ligne['titrefilm'] ?></h2>
                            <p><a href="/includes/film.php/?id_film=<?= $ligne['idfilm'] ?>">
                                    <button>en savoir plus</button>
                                </a></p>
                        </div>
                    <?php endforeach; ?>
                </div>
                <center>
                    <nav class="nav_catalogue">
                        <ul>
                            <?php if ($currentPage <= 1) : ?>

                            <?php else : ?>
                                <li>
                                    <a href="/catalogue.php?page=<?= $currentPage - 1 ?><?php if (isset($_GET['idgenre']) && !empty($_GET['idgenre'])) {
                                                                                            echo "&idgenre=" . $localgenre;
                                                                                        } ?>">précédente</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($page = 1; $page <= $pages; $page++) : ?>
                                <li>
                                    <a href="/catalogue.php?page=<?= $page ?><?php if (isset($_GET['idgenre']) && !empty($_GET['idgenre'])) {
                                                                                    echo "&idgenre=" . $localgenre;
                                                                                } ?>"><?= $page ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($currentPage == $pages) : ?>

                            <?php else : ?>
                                <li>
                                    <a href="/catalogue.php?page=<?= $currentPage + 1 ?><?php if (isset($_GET['idgenre']) && !empty($_GET['idgenre'])) {
                                                                                            echo "&idgenre=" . $localgenre;
                                                                                        } ?>">suivante</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </center>
            <?php
                ///////////////////////////////////////////////////////////////////// FIN DE L'AFFICHAGE DU CATALOGUE SI UN GENRE EST SELECTIONNE /////////////////////////////////////////////////////////////////////////////////////////
            }
        }
        ///////////////////////////////////////////////////////////////////// AFFICHAGE CATALOGUE SI AUCUN N'EST SELECTIONNE //////////////////////////////////////////////////////////////////////////////////////////////////////
        else { ?>

            <div class="row">
                <?php
                foreach ($listFilm as $ligne) : ?>
                    <div class="card">
                        <?php echo '<img class="card img" src="/upload/' . $ligne['image'] . '" alt="Image non chargée" style="width:100%">' ?>
                        <h2><?php echo $ligne['titrefilm'] ?></h2>
                        <p><a href="/includes/film.php/?id_film=<?= $ligne['idfilm'] ?>">
                                <button>en savoir plus</button>
                            </a></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <center>
                <nav class="nav_catalogue">
                    <ul>
                        <?php if ($currentPage <= 1) : ?>

                        <?php else : ?>
                            <li>
                                <a href="/catalogue.php?page=<?= $currentPage - 1 ?><?php if (isset($_GET['idgenre']) && !empty($_GET['idgenre'])) {
                                                                                        echo "&idgenre=" . $localgenre;
                                                                                    } ?>">précédente</a>
                            </li>
                        <?php endif; ?>

                        <?php for ($page = 1; $page <= $pages; $page++) : ?>
                            <li>
                                <a href="/catalogue.php?page=<?= $page ?><?php if (isset($_GET['idgenre']) && !empty($_GET['idgenre'])) {
                                                                                echo "&idgenre=" . $localgenre;
                                                                            } ?>"><?= $page ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($currentPage == $pages) : ?>

                        <?php else : ?>
                            <li>
                                <a href="/catalogue.php?page=<?= $currentPage + 1 ?><?php if (isset($_GET['idgenre']) && !empty($_GET['idgenre'])) {
                                                                                        echo "&idgenre=" . $localgenre;
                                                                                    } ?>">suivante</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </center>
        <?php
            ///////////////////////////////////////////////////////////////////////////////// FIN DE L'AFFICHAGE /////////////////////////////////////////////////////////////////////////////////////////////////
        }
        ?>
    </section>