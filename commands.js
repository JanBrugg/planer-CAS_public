function color_pick(type, number) {
    if (type == "hover") {
        switch (number) {
            case 6:
                var color = "#005eff";
                break
            case 5.5:
                var color = "#00b3ff";
                break
            case 5:
                var color = "#00ff0d";
                break
            case 4.5:
                var color = "#d4a320";
                break
            case 4:
                var color = "#f50000";
                break
            case 1:
                var color = "#000000";
                break
            case -1:
                var color = "#7a7a7a";
                break
        }
    } else if (type == "leave") {
        switch (number) {
            case 6:
                var color = "#3881ff";
                break
            case 5.5:
                var color = "#22a7e0";
                break
            case 5:
                var color = "#29d932";
                break
            case 4.5:
                var color = "#f5b50c";
                break
            case 4:
                var color = "#c41818";
                break
            case 1:
                var color = "#000000";
                break
            case -1:
                var color = "#FFFFFF";
                break
        }
    }
    return color;
}

function hover(elem, number, rainbow) {

    let color = color_pick("hover", number);

    if (rainbow == 2) {
        elem.className = "big-div-on-mouse-rainbow-today"
    } else if (rainbow == 1) {
        elem.className = "big-div-on-mouse-rainbow";
    } else {
        elem.className = "big-div-on-mouse";
    }
    elem.style.border = "3px solid " + color;
    elem.style.boxShadow = "0 5px 10px " + color;
    elem.style.backgroundColor = "" + color;
}

function leave(elem, number, rainbow) {

    let color = color_pick("leave", number)

    if (rainbow == 2) {
        elem.className = "big-div-rainbow-today";
    } else if (rainbow == 1) {
        elem.className = "big-div-rainbow";
    } else {
        elem.className = "big-div";
    }

    elem.style.border = "3px solid " + color;
    elem.style.boxShadow = "0 3px 6px " + color;
    elem.style.backgroundColor = "" + color;

}

/* color-values: ->

6 {
#3881ff

#005eff
}

5.5 {
#22a7e0

#00b3ff
}

5 {
#29d932

#00ff0d
}

4.5 {
#f5b50c

#d4a320
}

4 {
#c41818

#f50000
}

1 {
#000000

#000000
}
 */

function select_schuljahr(jahr_id) {
    console.log(jahr_id);
    var form = '<form action="/planer/faecher/" method="post" id="schuljahrForm">' +
        '<input type="hidden" name="schuljahr" id="schuljahr" value="' + jahr_id + '"> </form>';
    document.write(form);
    document.getElementById("schuljahrForm").submit();
}

function select_fach(fach_id) {
    console.log(fach_id)
    var form = '<form action="/planer/pruefungen/" method="post" id="fachForm">' +
        '<input type="hidden" name="fach" id="fach" value="' + fach_id + '"> </form>';
    document.write(form);
    document.getElementById("fachForm").submit();
}

function wunschnote_berechnen(schnitt, gewicht) {
    var wunschnote = document.getElementById("wunschnote").value;
    console.log(wunschnote);
    var wunschgewichtung = document.getElementById("gewichtung").value;
    console.log(wunschgewichtung);

    // bekomme gesamt Gewichtung und Schnitt
    // Wunschnote * (Gesamtgewichtung + Wunschgewicht)
    // > Wunschnote - (Schnitt * Gesamtgewicht)
    // Wunschnote / Wunschgewicht

    wunschnote = parseFloat(wunschnote) * parseFloat((parseFloat(gewicht) + parseFloat(wunschgewichtung)));
    console.log(wunschnote);
    wunschnote = parseFloat(wunschnote) - parseFloat((parseFloat(schnitt) * parseFloat(gewicht)));
    console.log(wunschnote);
    wunschnote = parseFloat((parseFloat(wunschnote) / parseFloat(wunschgewichtung)));

    wunschnote = wunschnote.toFixed(2);

    console.log(wunschnote)
    document.getElementById("berechnet").value = wunschnote;

}

function aendern(item, id, where) {

    var form = '<form method="post" id="aendernForm" action="/planer/aendern.php">' +
        '<input type="hidden" name="id" id="id" value="' + id + '">' +
        '<input type="hidden" name="type" id="type" value="';

    switch (item) {
        case "pruefung":
            console.log("Prüfung: " + id);
            form = form + 'Prüfung">';
            if (where == "dashboard") {
                form = form + '<input type="hidden" name="where" id="where" value="dashboard">';
            }
            break
        case "fach":
            console.log("fach: " + id);
            form = form + 'Fach">';
            break
        case "schuljahr":
            console.log("schuljahr: " + id);
            form = form + 'Schuljahr">';
            break
        case "user":
            console.log("user: " + id);
            form = form + 'User">';
            break
        default:
            console.log("smth went wrong");
            break
    }

    form = form + '</form>';

    document.write(form);
    document.getElementById("aendernForm").submit();

}

function colorchange(elem) {
    if (document.getElementById("angemeldet_bleiben").value == "nein") {
        document.getElementById("angemeldet_id").innerHTML += " ✓"
        document.getElementById("angemeldet_bleiben").value = "ja";
        console.log("Status: " + document.getElementById("angemeldet_bleiben").value)
    } else {
        document.getElementById("angemeldet_id").innerHTML = "Angemeldet bleiben"
        document.getElementById("angemeldet_bleiben").value = "nein";
        console.log(document.getElementById("angemeldet_bleiben").value)
    }
}

function password_checker() {
    let passwort = document.getElementById("passwort").value;
    let passwort_bestätigung = document.getElementById("passwort_bestätigen").value;

    if (passwort != passwort_bestätigung) {
        document.getElementById("passwort_ungleich").innerHTML = "Passwörter ungleich!";
        if (window.location.href == "https://alpha.kswe.ginf.ch/planer/aendern.php") {
            document.getElementById("aendern").disabled = true;
        } else {
            document.getElementById("account_erstellen").disabled = true;
        }
    } else {
        document.getElementById("passwort_ungleich").innerHTML = "";
        if (window.location.href == "https://alpha.kswe.ginf.ch/planer/aendern.php") {
            document.getElementById("aendern").disabled = false;
        } else {
            document.getElementById("account_erstellen").disabled = false;
        }
    }
}

function loeschen(id) {
    var form = '<form action="/planer/index.php" method="post" id="myForm">' +
        '<input type="hidden" name="loeschen" id="loeschen" value="loeschen">' +
        '<input type="hidden" name="id" id="id" value="' + id + '" "> </form>';

    document.write(form);

    document.getElementById("myForm").submit();
}

function hausaufgaben_bearbeiten(id) {
    console.log(id);

    var form = '<form method="post" action="#bearbeiten" id="myForm">' +
        '<input type="hidden" name="id" id="id" value="' + id + '"></form>';

    document.write(form);
    document.getElementById("myForm").submit();
}

function hausaufgabe_loeschen(id) {
    var form = '<form method="post" action="#" id="myForm">' +
        '<input type="hidden" name="id" id="id" value="' + id + '">' +
        '<input type="hidden" name="type" id="type" value="loeschen"></form>';

    document.write(form);
    document.getElementById("myForm").submit();
}