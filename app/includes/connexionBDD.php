<?php
function connexionBDD() {
    include("infoConnexion.php");
    $dsn='pgsql:host='.$lehost.';dbname='.$dbname.';port='.$leport;

    // connexion à la bdd (connexion non persistante) avec le connecteur nommé $connex
    try { // essai de connexion
        $connex = new PDO($dsn, $user, $pass); // tentative de connexion
    } catch (PDOException $e) { // si la connexion échoue
        die(); // Arrêt du script - sortie.
    }
    return $connex;
}
function deconnexionBDD($connex) {
    $connex = null; // fermeture de connexion
}
?>