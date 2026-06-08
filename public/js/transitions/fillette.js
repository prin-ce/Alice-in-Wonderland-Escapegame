function extraire() {

    if( ++i < message.length ){ 
        
        if( message[i] == '\n' ){

            document.getElementById("msg").innerHTML += '<br/>';
        } else {

            document.getElementById("msg").innerHTML += message[i];
        }
    } else {

        clearTimeout(interval);
        location.replace("../enigmes/EnigmeFillette")
    }
}

var i = -1;

var message = "Vous avez resolu le probleme du snake,\n  le chemin de la sortie vous est desormais accessible.\n Mais attention, le chat de la Reine garde l'acces    ";

var interval = setInterval(extraire, 70);


             
