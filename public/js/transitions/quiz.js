function extraire() {

    if( ++i < message.length ){ 
        
        if( message[i] == '\n' ){

            document.getElementById("msg").innerHTML += '<br/>';
        } else {

            document.getElementById("msg").innerHTML += message[i];
        }
    } else {

        clearTimeout(interval);
        location.replace("../enigmes/final")
    }
}

var i = -1;

var message = "hahaha, vous pensiez avoir fini? Eh bien non!!\n  Vous avez active une carte piege conduisant dans une\n boucle spacio-temporelle. Terminez un dernier quizz   ";

var interval = setInterval(extraire, 60);


             
