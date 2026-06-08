function extraire() {

    if( ++i < message.length ){  // On incremente i et on compare a la taille du message.
        
        /**  Si i ne depasse pas le nombre de caracteres dans le message
         ** Note : le premier caractere de la chaine commence a l'index 0
         */
         music.loop = true;
         music.play();

        if( message[i] == '\n' ){
            // Si saut de ligne on remplace par l'equivalent HTML : "<br/>".
            document.getElementById("msg").innerHTML += '<br/>';
        } else {
            // Sinon on ajoute simplement le caractere a l'emplacement courant.
            document.getElementById("msg").innerHTML += message[i];
        }
    } else {
        // Sinon on arrete le timer car le texte a fini de s'afficher.
        // et on redirige vers la page du jeu
        clearTimeout(interval);
        location.replace("../enigmes/EnigmeMiam")
    }
}

var i = -1; // On incremente i en début de fonction, il vaudra donc 0 a la premiere execution.

var message = "En suivant le lapin blanc, vous tombez dans un gouffre\n A votre reveil, deux objets se trouvent devant vous    "; // Message a afficher, on utilise le caractere \n pour le retour a la ligne.

var interval = setInterval(extraire, 70); // On declanche le timer et on le garde dans une variable pour l'arreter plus tard.

var audio = new Audio('../audio/sonclavier.mp3');
var music = new Audio('../audio/son2.mp3');

             
