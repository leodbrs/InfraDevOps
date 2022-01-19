function menuVendeur(pageDemander) {
    var page = document.getElementById("corps_paddingleft");
    var objRequete = new XMLHttpRequest();
    if (pageDemander.value != undefined) {
        var query = '?menu=' + pageDemander.id + '&id=' + pageDemander.value;
    } else {
        var query = '?menu=' + pageDemander.id;
    }

    objRequete.open('GET', 'vendeur.php' + query, true);
    objRequete.onreadystatechange = function recupereReponse() {
        if (objRequete.readyState == 4 && objRequete.status == 200) {
            var reponse = objRequete.responseText;
            page.innerHTML = reponse;
        }
    };
    objRequete.send(null);
}

function changeImage(imageSelect) {
    var img = document.getElementById("coverFilm");
    var defaultSelected = document.getElementById("defaultSelected");
    var filmCover = document.getElementById("filmCover");
    if (imageSelect == undefined) {
        defaultSelected.selected = true;
        img.setAttribute("src", "/assets/img/blanc.png");
    } else {
        if (filmCover != null) {
            filmCover.value = "";
        }
        img.setAttribute("src", "/upload/" + imageSelect);
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

function modifierClient() {
    var h3message = document.getElementById("message");
    var formData = new FormData();

    var nom = $("#nom").val();
    var prenom = $("#prenom").val();
    var email = $("#email").val();
    var date_naissance = $("#date_naissance").val();
    var genre = $("#genre").val();
    var username = $("#username").val();
    var ville = $("#ville").val();
    var edit_confirm = $("#client_edit_confirm").val();

    if (nom.length > 0 && prenom.length > 0 && email.length > 0 && date_naissance.length > 0 && genre.length > 0 && username.length > 0 && ville.length > 0) {
        formData.append("nom", nom);
        formData.append("prenom", prenom);
        formData.append("email", email);
        formData.append("date_naissance", date_naissance);
        formData.append("genre", genre);
        formData.append("nom_utilisateur", username);
        formData.append("ville", ville);

        $.ajax({
            url: "vendeur.php?menu=client_edit_confirm&id=" + edit_confirm,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(reponse) {
                if (reponse == 1) {
                    h3message.innerHTML = "Le client \"" + nom + " " + prenom + "\" a été modifié.";
                } else {
                    h3message.innerHTML = "Une erreur s'est produite lors de la modification.";
                }
            },
        });
    } else {
        h3message.innerHTML = "Le formulaire d'ajout est incomplet.";
    }
}

function modifierLocation() {
    var h3message = document.getElementById("message");
    var formData = new FormData();

    var nbjours = $("#nbjours").val();
    var dateloc = $("#dateloc").val();
    var client = $("#client").val();
    var film = $("#film").val();
    var oldexemplaire = $("#oldexemplaire").val();
    var edit_confirm = $("#location_edit_confirm").val();

    if (nbjours.length > 0 && dateloc.length > 0 && client.length > 0 && film.length > 0) {
        formData.append("nbjours", nbjours);
        formData.append("dateloc", dateloc);
        formData.append("client", client);
        formData.append("film", film);
        formData.append("oldexemplaire", oldexemplaire);

        $.ajax({
            url: "vendeur.php?menu=location_edit_confirm&id=" + edit_confirm,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(reponse) {
                if (reponse == 1) {
                    h3message.innerHTML = "La location a été modifié.";
                } else if (reponse == 3) {
                    h3message.innerHTML = "Aucun exemplaire de ce film n'est disponible.";
                } else {
                    h3message.innerHTML = "Une erreur s'est produite lors de la modification.";
                }
            },
        });
    } else {
        h3message.innerHTML = "Le formulaire d'ajout est incomplet.";
    }
}

function modifierFilm() {
    var h3message = document.getElementById("message");
    var formData = new FormData();

    var titre = $("#titre").val();
    var description = $("#description").val();
    var localfilmCover = $("#localfilmCover").val();
    var edit_confirm = $("#film_edit_confirm").val();

    if (titre.length > 0 && description.length > 0 && localfilmCover.length > 0) {
        formData.append("titre", titre);
        formData.append("description", description);
        formData.append("localfilmCover", localfilmCover);

        $.ajax({
            url: "vendeur.php?menu=film_edit_confirm&id=" + edit_confirm,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(reponse) {
                if (reponse == 1) {
                    h3message.innerHTML = "Le film \"" + titre + "\" a été modifié.";
                } else {
                    h3message.innerHTML = "Une erreur s'est produite lors de la modification.";
                }
            },
        });
    } else {
        h3message.innerHTML = "Le formulaire d'ajout est incomplet.";
    }
}

function ajouterFilm() {
    var formData = new FormData();

    var h3message = document.getElementById("message");
    var titre = $("#titre").val();
    var description = $("#description").val();
    var filmCover = $("#filmCover")[0].files;
    var localfilmCover = $("#localfilmCover").val();

    if (titre.length > 0 && description.length > 0 && (filmCover.length > 0 || localfilmCover.length > 0)) {
        formData.append("titre", titre);
        formData.append("description", description);
        if (filmCover.length > 0) {
            formData.append("film_cover", filmCover[0]);
        } else {
            formData.append("localfilm_cover", localfilmCover);
        }
        $.ajax({
            url: "vendeur.php?menu=film_add",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(reponse) {
                if (reponse == 1) {
                    h3message.innerHTML = "Le film \"" + titre + "\" a été ajouté.";
                } else {
                    h3message.innerHTML = "Une erreur s'est produite lors de l'ajout.";
                }
            },
        });
    } else {
        h3message.innerHTML = "Le formulaire d'ajout est incomplet.";
    }
}

function listeFilmGenres() {

    var formData = new FormData();
    var table = document.getElementById("tableGenres");
    var submit = document.getElementById("film_genres_confirm");
    var idfilm = $("#film").val();
    submit.value = idfilm;

    if (idfilm == null || idfilm == "") {
        submit.disabled = true;
    } else {
        submit.disabled = false;
    }
    $.ajax({
        url: "vendeur.php?menu=film_genres_list&id=" + idfilm,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(reponse) {
            var reponse = JSON.parse(reponse);
            table.innerHTML = '';
            for (ligne in reponse['listeGenres']) {
                var tr = document.createElement('tr');
                var tdTexte = document.createElement('td');
                tdTexte.appendChild(document.createTextNode(reponse['listeGenres'][ligne]));

                var tdGenre = document.createElement('td');
                var checkbox = document.createElement('input');
                checkbox.type = "checkbox";
                checkbox.name = ligne;
                checkbox.id = reponse['listeGenres'][ligne];

                if (reponse['filmGenres'][ligne] == reponse['listeGenres'][ligne]) {
                    checkbox.checked = true;
                }

                tdGenre.appendChild(checkbox);
                tr.appendChild(tdTexte);
                tr.appendChild(tdGenre);
                $("p:first").addClass("intro");
                table.appendChild(tr);
            }
        },
    });
}

function modifierFilmGenres() {
    var formData = new FormData();
    var h3message = document.getElementById("message");
    var idfilm = $("#film").val();
    $.ajax({
        url: "vendeur.php?menu=film_genres_list&id=",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(reponse) {
            var reponse = JSON.parse(reponse);
            var formData = new FormData();
            for (ligne in reponse['listeGenres']) {
                genre = reponse['listeGenres'][ligne];
                genreid = ligne;
                var checkbox = document.getElementById(genre);
                if (checkbox.checked == true) {
                    formData.append(genreid, true);
                    console.log(genreid);
                } else {
                    formData.append(genreid, false);
                }
            }
            $.ajax({
                url: "vendeur.php?menu=film_genres_confirm&id=" + idfilm,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(reponse) {
                    if (reponse == 1) {
                        h3message.innerHTML = "Les genres ont été ajouté.";
                    } else {
                        h3message.innerHTML = "Une erreur s'est produite lors de l'ajout des genres.";
                    }
                },
            });
        },
    });
}

function modifierGenre() {
    var h3message = document.getElementById("message");
    var formData = new FormData();

    var genre = $("#genre").val();
    var edit_confirm = $("#genre_edit_confirm").val();

    if (genre.length) {
        formData.append("genre", genre);

        $.ajax({
            url: "vendeur.php?menu=genre_edit_confirm&id=" + edit_confirm,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(reponse) {
                if (reponse == 1) {
                    h3message.innerHTML = "Le genre \"" + genre + "\" a été modifié.";
                } else {
                    h3message.innerHTML = "Une erreur s'est produite lors de la modification.";
                }
            },
        });
    } else {
        h3message.innerHTML = "Le formulaire d'ajout est incomplet.";
    }
}

function ajouterGenre() {
    var formData = new FormData();

    var h3message = document.getElementById("message");
    var genre = $("#genre").val();

    if (genre.length > 0) {
        formData.append("genre", genre);
        $.ajax({
            url: "vendeur.php?menu=genre_add",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(reponse) {
                if (reponse == 1) {
                    h3message.innerHTML = "Le genre \"" + genre + "\" a été ajouté.";
                } else {
                    h3message.innerHTML = "Une erreur s'est produite lors de l'ajout.";
                }
            },
        });
    } else {
        h3message.innerHTML = "Le formulaire d'ajout est incomplet.";
    }
}

function listeGenreFilms() {

    var formData = new FormData();
    var table = document.getElementById("tableFilms");
    var submit = document.getElementById("genre_films_confirm");
    var idgenre = $("#genre").val();
    submit.value = idgenre;

    if (idgenre == null || idgenre == "") {
        submit.disabled = true;
    } else {
        submit.disabled = false;
    }
    $.ajax({
        url: "vendeur.php?menu=genre_films_list&id=" + idgenre,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(reponse) {
            var reponse = JSON.parse(reponse);
            table.innerHTML = '';
            for (ligne in reponse['listeFilms']) {
                var tr = document.createElement('tr');
                var tdTexte = document.createElement('td');
                tdTexte.appendChild(document.createTextNode(reponse['listeFilms'][ligne]));

                var tdFilm = document.createElement('td');
                var checkbox = document.createElement('input');
                checkbox.type = "checkbox";
                checkbox.name = ligne;
                checkbox.id = reponse['listeFilms'][ligne];

                if (reponse['genreFilms'][ligne] == reponse['listeFilms'][ligne]) {
                    checkbox.checked = true;
                }

                tdFilm.appendChild(checkbox);
                tr.appendChild(tdTexte);
                tr.appendChild(tdFilm);
                table.appendChild(tr);
            }
        },
    });

}

function modifierGenreFilms() {
    var formData = new FormData();
    var h3message = document.getElementById("message");
    var idgenre = $("#genre").val();
    $.ajax({
        url: "vendeur.php?menu=genre_films_list&id=",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(reponse) {
            var reponse = JSON.parse(reponse);
            var formData = new FormData();
            for (ligne in reponse['listeFilms']) {
                film = reponse['listeFilms'][ligne];
                filmid = ligne;
                var checkbox = document.getElementById(film);
                if (checkbox.checked == true) {
                    formData.append(filmid, true);
                    console.log(filmid);
                } else {
                    formData.append(filmid, false);
                }
            }
            $.ajax({
                url: "vendeur.php?menu=genre_films_confirm&id=" + idgenre,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(reponse) {
                    if (reponse == 1) {
                        h3message.innerHTML = "Les films ont été ajouté.";
                    } else {
                        h3message.innerHTML = "Une erreur s'est produite lors de l'ajout des films.";
                    }
                },
            });
        },
    });
}

function listFilmExemplaires() {

    var formData = new FormData();
    var table = document.getElementById("tableExemplaires");
    var submit = document.getElementById("exemplaire_add");
    var idfilm = $("#film").val();
    var h3message = document.getElementById("message");
    h3message.innerHTML = "";
    submit.value = idfilm;

    if (idfilm == null || idfilm == "") {
        submit.disabled = true;
    } else {
        submit.disabled = false;
    }
    $.ajax({
        url: "vendeur.php?menu=exemplaire_list&id=" + idfilm,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(reponse) {
            table.innerHTML = '';
            if (reponse.length > 0) {
                var reponse = JSON.parse(reponse);

                var tr = document.createElement('tr');
                var tdExemplaire = document.createElement('td');
                tdExemplaire.appendChild(document.createTextNode("Exemplaire"));
                tr.appendChild(tdExemplaire);
                var tdDisponible = document.createElement('td');
                tdDisponible.appendChild(document.createTextNode("Disponible"));
                tr.appendChild(tdDisponible);
                table.appendChild(tr);

                for (ligne in reponse) {
                    var tr = document.createElement('tr');
                    tr.setAttribute("id", ligne)
                    var tdTexte = document.createElement('td');
                    tdTexte.appendChild(document.createTextNode(ligne));
                    tr.appendChild(tdTexte);

                    var checkbox = document.createElement('input');
                    checkbox.type = "checkbox";
                    checkbox.name = ligne;
                    if (reponse[ligne] == true) {
                        checkbox.checked = true;
                    }
                    var tdFilm = document.createElement('td');
                    tdFilm.appendChild(checkbox);
                    tr.appendChild(tdFilm);

                    var bouton = document.createElement("button");
                    bouton.innerHTML = "Supprimer";
                    bouton.setAttribute("value", ligne);
                    bouton.setAttribute("id", "bouton" + ligne);

                    if (reponse[ligne] == false) {
                        bouton.disabled = true;
                    }
                    var tdBouton = document.createElement('td');
                    tdBouton.appendChild(bouton);
                    tr.appendChild(tdBouton);
                    table.appendChild(tr);

                    bouton.addEventListener("click", function(button) {
                        var formData = new FormData();
                        $.ajax({
                            url: "vendeur.php?menu=exemplaire_delete&id=" + button.target.value,
                            type: "POST",
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(reponse) {
                                if (reponse == 1) {
                                    h3message.innerHTML = "L'exemplaire a été supprimé.";
                                    var tdDelete = document.getElementById(button.target.value);
                                    if (table.children.length > 2) {
                                        table.removeChild(tdDelete);
                                    } else {
                                        table.innerHTML = "";
                                    }
                                } else {
                                    h3message.innerHTML = "L'exemplaire appartient à une location, la suppression de l'exemplaire n'a pas eu lieu.";
                                }
                            },
                        });
                    });
                    checkbox.addEventListener("click", function(checkbox) {
                        var formData = new FormData();
                        $.ajax({
                            url: "vendeur.php?menu=exemplaire_changeetat&id=" + checkbox.target.name + "&etat=" + checkbox.target.checked,
                            type: "POST",
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(reponse) {
                                if (reponse == 1) {
                                    h3message.innerHTML = "L'etat de l'exemplaire a été changer.";
                                    var bouton = document.getElementById("bouton" + checkbox.target.name);
                                    if (checkbox.target.checked == false) {
                                        bouton.disabled = true;
                                    } else {
                                        bouton.disabled = false;
                                    }
                                } else {
                                    h3message.innerHTML = "Une erreur s'est produite lors du changement d'état de l'exemplaire.";
                                }
                            },
                        });
                    });
                }
            }
        },
    });

}

function ajouterFilmExemplaires() {
    var formData = new FormData();
    var h3message = document.getElementById("message");
    var idfilm = $("#film").val();
    $.ajax({
        url: "vendeur.php?menu=exemplaire_add&id=" + idfilm,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(reponse) {
            var reponse = JSON.parse(reponse);

            if (reponse[0] == 1) {
                h3message.innerHTML = "Un exemplaire à été ajouté.";

                var table = document.getElementById("tableExemplaires");
                if (table.children.length == 0) {
                    var tr = document.createElement('tr');
                    var tdExemplaire = document.createElement('td');
                    tdExemplaire.appendChild(document.createTextNode("Exemplaire"));
                    tr.appendChild(tdExemplaire);
                    var tdDisponible = document.createElement('td');
                    tdDisponible.appendChild(document.createTextNode("Disponible"));
                    tr.appendChild(tdDisponible);
                    table.appendChild(tr);
                }

                var tr = document.createElement('tr');
                tr.setAttribute("id", reponse[1])

                var tdTexte = document.createElement('td');
                tdTexte.appendChild(document.createTextNode(reponse[1]));
                tr.appendChild(tdTexte);

                var checkbox = document.createElement('input');
                checkbox.type = "checkbox";
                checkbox.name = reponse[1];
                checkbox.checked = true;
                var tdFilm = document.createElement('td');
                tdFilm.appendChild(checkbox);
                tr.appendChild(tdFilm);

                var bouton = document.createElement("button");
                bouton.innerHTML = "Supprimer";
                bouton.setAttribute("value", reponse[1]);
                bouton.setAttribute("id", "bouton" + reponse[1]);

                var tdBouton = document.createElement('td');
                tdBouton.appendChild(bouton);
                tr.appendChild(tdBouton);
                table.appendChild(tr);
                bouton.addEventListener("click", function(button) {
                    var formData = new FormData();
                    $.ajax({
                        url: "vendeur.php?menu=exemplaire_delete&id=" + button.target.value,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(reponse) {
                            if (reponse == 1) {
                                h3message.innerHTML = "L'exemplaire a été supprimé.";
                                var tdDelete = document.getElementById(button.target.value);
                                if (table.children.length > 2) {
                                    table.removeChild(tdDelete);
                                } else {
                                    table.innerHTML = "";
                                }
                            } else {
                                h3message.innerHTML = "L'exemplaire appartient à une location, la suppression de l'exemplaire n'a pas eu lieu.";
                            }
                        },
                    });
                });
                checkbox.addEventListener("click", function(checkbox) {
                    var formData = new FormData();
                    $.ajax({
                        url: "vendeur.php?menu=exemplaire_changeetat&id=" + checkbox.target.name + "&etat=" + checkbox.target.checked,
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(reponse) {
                            if (reponse == 1) {
                                h3message.innerHTML = "L'etat de l'exemplaire a été changer.";
                                var bouton = document.getElementById("bouton" + checkbox.target.name);
                                if (checkbox.target.checked == false) {
                                    bouton.disabled = true;
                                } else {
                                    bouton.disabled = false;
                                }
                            } else {
                                h3message.innerHTML = "Une erreur s'est produite lors du changement d'état de l'exemplaire.";
                            }
                        },
                    });
                });

            } else {
                h3message.innerHTML = "Une erreur s'est produite lors de la l'ajout d'un exemplaire.";
            }
        },
    });
}