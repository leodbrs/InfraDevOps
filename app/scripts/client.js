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

function afficherPasswordActuel() {
    var x = document.getElementById("motdepasseactuel");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}