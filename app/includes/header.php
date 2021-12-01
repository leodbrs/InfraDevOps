<?php
if (isset($_SESSION['nom'], $_SESSION['prenom'], $_SESSION['nomutilisateur'], $_SESSION['vendeur'])) {
    $prenom = $_SESSION['prenom'];
    if ($_SESSION['vendeur'] == 1) {
        echo <<<EOT
        <header>
            <a href="/">
                <img src="/assets/img/logo.png" alt="">
                <span>DVD rental</span>
            </a>
        </header>
        <nav class="navigation">
            <ul class="leftnav">
                <li><a href="/">Accueil</a></li>
                <li><a href="/catalogue.php">Catalogue</a></li>
                <li><a href="/contact.php">Contact</a></li>
            </ul>
            <ul class="rightnav compte">
                <li><a href="/vendeur.php">Page vendeur ({$prenom})</a></li>
                <li><a href="/includes/deconnexion.php">Se déconnecter</a></li>
            </ul>
        </nav>
EOT;
    }
    elseif ($_SESSION['vendeur'] == 0) {
        echo <<<EOT
        <header>
            <a href="/">
                <img src="/assets/img/logo.png" alt="">
                <span>DVD rental</span>
            </a>
        </header>
        <nav class="navigation">
            <ul class="leftnav">
                <li><a href="/">Accueil</a></li>
                <li><a href="/catalogue.php">Catalogue</a></li>
                <li><a href="/contact.php">Contact</a></li>
            </ul>
            <ul class="rightnav compte">
                <li><a href="/panier.php">Panier</a></li>
                <li><a href="/client.php">Compte ({$prenom})</a></li>
                <li><a href="/includes/deconnexion.php">Se déconnecter</a></li>
            </ul>
        </nav>
EOT;
    }
}
else {
    echo <<<EOT
    <header>
        <a href="/">
            <img src="/assets/img/logo.png" alt="">
            <span>DVD rental</span>
        </a>
    </header>
    <nav class="navigation">
        <ul class="leftnav">
            <li><a href="/">Accueil</a></li>
            <li><a href="/catalogue.php">Catalogue</a></li>
            <li><a href="/contact.php">Contact</a></li>
        </ul>
        <ul class="rightnav">
            <li><a href="/connexion.php">Connexion</a></li>
            <li><a href="/inscription.php">Inscription</a></li>
        </ul>
    </nav>
EOT;
}
