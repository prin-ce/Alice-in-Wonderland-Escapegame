function extraire() {

    if( ++i < message.length ){ 
        
        if( message[i] == '\n' ){

            document.getElementById("msg").innerHTML += '<br/>';
        } else {

            document.getElementById("msg").innerHTML += message[i];
        }
    } else {

        clearTimeout(interval);
        location.replace("../enigmes/EnigmeTaille")
    }
}

var i = -1;

var message = "Le Pays des Merveilles s'ouvre a vous. Impatiente de le decouvrir,\n vous ne faites pas attention ou vous mettez les pieds et !!!\n Votre pied s'enfonce dans un terrier    ";

var interval = setInterval(extraire, 70);


             
