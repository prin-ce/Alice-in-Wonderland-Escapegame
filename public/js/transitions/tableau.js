function extraire() {

    if( ++i < message.length ){ 
        
        if( message[i] == '\n' ){

            document.getElementById("msg").innerHTML += '<br/>';
        } else {

            document.getElementById("msg").innerHTML += message[i];
        }
    } else {

        clearTimeout(interval);
        location.replace("../enigmes/EnigmeTableau")
    }
}

var i = -1;

var message = "Vous avez perdu la trace du lapin (encore),\n mais vous vous retrouvez maintenant face a\n un groupe de cartes, et qui parlent     ";

var interval = setInterval(extraire, 60);


             
