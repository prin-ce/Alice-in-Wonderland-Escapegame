function extraire() {

    if( ++i < message.length ){ 
        
        if( message[i] == '\n' ){

            document.getElementById("msg").innerHTML += '<br/>';
        } else {

            document.getElementById("msg").innerHTML += message[i];
        }
    } else {

        clearTimeout(interval);
        location.replace("../enigmes/EnigmeLapin")
    }
}

var i = -1;

var message = "La carte qui vous a ouvert le chemin prends l'apparence du lapin blanc.\n Ce dernier regarde sa montre, et essaie de s'en aller    ";

var interval = setInterval(extraire, 70);


             
