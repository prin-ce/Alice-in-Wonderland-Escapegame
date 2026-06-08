function extraire() {

    if( ++i < message.length ){ 
        
        if( message[i] == '\n' ){

            document.getElementById("msg").innerHTML += '<br/>';
        } else {

            document.getElementById("msg").innerHTML += message[i];
        }
    } else {

        clearTimeout(interval);
        location.replace("../enigmes/snake")
    }
}

var i = -1;

var message = "Vous etes tombee dans un piege, La Reine a ete mise\n au courant de votre presence et cherche a vous eliminer.\n Vous etes jetee dans un monde etrange, trouvez la sortie   ";

var interval = setInterval(extraire, 60);


             
