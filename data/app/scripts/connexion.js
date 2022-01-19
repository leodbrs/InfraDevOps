function afficherPassword() {
            var x = document.getElementById("motdepasse");
            if (x.type === "password") {
                x.type = "text";
            }
            else {
                x.type = "password"
            }
        }