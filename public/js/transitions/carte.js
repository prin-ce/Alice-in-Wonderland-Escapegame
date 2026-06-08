function extraire() {

    if( ++i < message.length ){ 
        
        if( message[i] == '\n' ){

            document.getElementById("msg").innerHTML += '<br/>';
        } else {

            document.getElementById("msg").innerHTML += message[i];
        }
    } else {

        clearTimeout(interval);
        location.replace("../enigmes/EnigmeCarte")
    }
}

var i = -1;

var message = "Vous avez maintenant la bonne taille.\n Un chemin se dresse devant vous,\n au bout duquel vous tombez sur un mur de cartes    ";

var interval = setInterval(extraire, 70);


             
