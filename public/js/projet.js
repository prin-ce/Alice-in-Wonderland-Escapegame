class Question {
	//Constructeur 
  constructor(text, choix, reponse) {
    this.text = text;
    this.choix = choix;
    this.reponse = reponse;
  }

  //La fonction qui compare le choix entrer par le joueur et la reponse à l'enigme
  estVrai(choice) {
    return this.reponse === choice;
  }
}


//stocker un tableau des énigmes dans la variable questions
let questions = [
  new Question("Pour indiquer qu'un contenu est très important, on utilise l'élement...",["Strong", "Em", "Mark", "I"], "Strong"),
  new Question("Quelle balise utilisera-t-on de préférence pour le titre principal d'une page html?",["h1", "head","heading", "h6"], "h1"),
  new Question("comment sélectionner toutes les balises <p> en CSS?",["p{}", "#p{}", ".p{}", "<p>{}"], "p{}"),
  new Question("comment écrire un commentaire en HTML",["'<'!-- Comment--'>'", "#Comment#", "/*Comment*/", "//Comment"], "'<'!-- Comment--'>'"),
  new Question("Quelle méthode Javascript permet de vérifier si un élément figure dans un tableau?",["isNaN()", "includes()", "findIndex()", "isOdd()"], "includes()"),
  new Question("Quelle méthode Javascript permet de filtrer les éléments d'un tableau?",["indexOf()", "map()", "filter()", "VRAI", "reduce()"], "filter()")
];


console.log(questions);

class Enigme {
  constructor(questions) {
    this.score = 0;
    this.questions = questions;
    this.nombreQuestion  = 0;
  }
  //recupère l'indice du tableau
  tableau() {
    return this.questions[this.nombreQuestion ];
  }
  //rajoute un point au score si la reponse est vrai et passe à l'énigme suivant 
  deviner(tentative) {
    if (this.tableau().estVrai(tentative)) {
      this.score++;
    }
    this.nombreQuestion ++;
  }
  //retourne vrai si y'a encore des enigme faux sinon 
  estVide() {
    return this.nombreQuestion  >= this.questions.length;
  }
}

const affichage = {
	//recupére l'Id et écrire un texte à sa place  
  html: function(id, text) {
    let element = document.getElementById(id);
    element.innerHTML = text;
  },
  // afficher le score du joueur 
  finJeu: function() {
    finJeuHTML = `
      <h1 id="fin">Fin du Jeu !</h1>
      <h3 id="fin"> Votre score de cette dernière partie est de: ${enigme.score} / ${enigme.questions.length}</h3>
      <form id="fin" action="../browse">
        <button class="btn" type="submit">Terminer le jeu</button>
      </form>`;
    this.html("quiz", finJeuHTML);
  },
  question: function() {
    this.html("question", enigme.tableau().text);
  },
  choix: function() {
    let choix = enigme.tableau().choix;

    guess = (id, guess) => {
      document.getElementById(id).onclick = function() {
        enigme.deviner(guess);
        Jeu();
      }
    }
    for(let i = 0; i < choix.length; i++) {
      this.html("choice" + i, choix[i]);
      guess("guess" + i, choix[i]);
    }
  },
  progress: function() {
    let currentQuestionNumber = enigme.nombreQuestion  + 1;
    this.html("progress", "Question " + currentQuestionNumber + " sur " + enigme.questions.length);
  },
};

let timeElm = document.getElementById('timeElm');
        let timer = function(x) {
        if(x === 0) {
          affichage.finJeu();
            return;
        }
        timeElm.innerHTML = x;
        return setTimeout(() => {timer(--x)}, 1000)
        }
        timer(30);

Jeu = () => {
  if (enigme.estVide()) {
    affichage.finJeu();
  } else {
    affichage.question();
    affichage.choix();
    affichage.progress();
  } 
}


let enigme = new Enigme(questions);
Jeu();

console.log(enigme);