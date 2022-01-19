<?php
function creationPanier()
{
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = array();
        $_SESSION['panier']['nomfilm'] = array();
        $_SESSION['panier']['qte'] = array();
    }
    return true;
}

function ajouterFilm($nomfilm, $qte)
{
    if (isset($_SESSION['panier'])) {
        $positionFilm = array_search($nomfilm, $_SESSION['panier']['nomfilm']);

        if ($positionFilm !== false) {
            $_SESSION['panier']['qte'][$positionFilm] += $qte;
        } else {
            array_push($_SESSION['panier']['nomfilm'], $nomfilm);
            array_push($_SESSION['panier']['qte'], $qte);
        }
    } else {
        creationPanier();
        echo "problème survenu lors de l'ajout d'un film au panier";
    }
}

function supprimerFilm($nomfilm)
{
    if (isset($_SESSION['panier'])) {
        $tmp = array();
        $tmp['nomfilm'] = array();
        $tmp['qte'] = array();

        for ($i = 0; $i < count($_SESSION['panier']['nomfilm']); $i++) {
            if ($_SESSION['panier']['nomfilm'][$i] !== $nomfilm) {
                array_push($tmp['nomfilm'], $_SESSION['panier']['nomfilm'][$i]);
                array_push($tmp['qte'], $_SESSION['panier']['qte'][$i]);
            }
        }
        $_SESSION['panier'] = $tmp;
        unset($tmp);
    } else {
        creationPanier();
        echo "un problème est survenu lors de la suppression du film";
    }
}


function modifierQTE($nomfilm, $qte)
{
    if (isset($_SESSION['panier'])) {
        //Si la quantité est positive on modifie sinon on supprime l'article
        if ($qte > 0) {
            //Recherche du produit dans le panier
            // echo $qte;
            $positionProduit = array_search($nomfilm,  $_SESSION['panier']['nomfilm']);
            // print_r($positionProduit);

            $_SESSION['panier']['qte'][$positionProduit] = $qte;
            echo "information enregistré";
        } else {
            supprimerFilm($nomfilm);
        }
    } else {
        echo "fonction fonctionne pas";
    }
}
