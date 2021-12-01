<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/includes/connexionBDD.php");
$conn1 = connexionBDD();
$listeVille = array();

$local_codePostal = $_GET['code_postal'];

$query = $conn1->prepare("SELECT idville, nomville from ville WHERE codepostal=:codepostal");
$query->bindValue(":codepostal", $local_codePostal, PDO::PARAM_STR);
$query->execute();
$reponse = $query->fetchAll();

foreach ($reponse as $ligne) {
    $listeVille += [$ligne["idville"] => $ligne["nomville"]];
}
// echo "<pre>";
// print_r($listeVille);
echo json_encode($listeVille);
