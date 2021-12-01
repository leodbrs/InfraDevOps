function afficherPassword() {
    var x = document.getElementById("motdepasse");
    var y = document.getElementById("motdepasseconfirm");
    if (x.type === "password") {
        x.type = "text";
        y.type = "text";
    } else {
        x.type = "password";
        y.type = "password"
    }
}

function compteCar(idvilleOnload) {
    var objCodePostal = document.getElementById("code_postal").value;
    if (objCodePostal.length == 5) {
        rechercheCodePostal(idvilleOnload);
    }
}

function rechercheCodePostal(idvilleOnload) {
    var objRequete = new XMLHttpRequest();
    var code_postal = document.getElementById("code_postal").value;

    objRequete.open('GET', 'includes/rechercheVille.php?code_postal=' + code_postal, true);
    objRequete.onreadystatechange = function recupereReponse() {
        if (objRequete.readyState == 4 && objRequete.status == 200) {
            var listeVilleJSON = objRequete.responseText;
            var select = document.getElementById('ville');
            var listeVille = JSON.parse(listeVilleJSON)
            while (select.length > 1) {
                select.remove(select.length - 1);
            }
            for (ligne in listeVille) {
                var option = document.createElement('option');
                option.appendChild(document.createTextNode(listeVille[ligne]));
                option.value = ligne;
                if (ligne == idvilleOnload) {
                    option.selected = true;
                }
                select.appendChild(option);
            }
        }
    };
    objRequete.send(null);
}