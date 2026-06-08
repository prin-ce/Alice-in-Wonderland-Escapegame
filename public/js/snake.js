
//constante pour deplacer les elements dans une des enigmes
const GAUCHE = 37 ;
const HAUT = 38 ;
const DROITE = 39 ;
const BAS = 40 ;

/*partie classe*/

/**creation du serpent
 * avec creation de la classe serpent
 * @param {int}x - pour la coordonnee horizontal
 * @param{int}y- pour la coordonnee vertical
 */
class Serpent {

  //attribut
  #directions ; //attribut prive
  tete ;
  queue ;

  /**constructeur de la classe
  * @param {int} x
  * @param {int} y
  */
  constructor(x,y) {
    this.x = x ;
    this.y = y ;
    this.tete = [x,y] ;
    this.queue = [
      [x,y],
      [x,y],
      [x,y],
      [x,y],
      [x,y],
      [x,y]
    ];
    this.#directions = 0 ;
  }

//getter et setter
  getDirection() {
    return this.#directions ;
  }
  //permet de changer la direction du serpent
  setDirection(z) {
    switch(z){

      case GAUCHE :
        if(this.#directions != DROITE){
          this.#directions = z;
        }
        break;
      case DROITE :
        if(this.#directions != GAUCHE){
          this.#directions = z;
        }
        break;
      case HAUT :
        if(this.#directions != BAS){
          this.#directions = z;
        }
        break;
      case BAS :
        if(this.#directions != HAUT){
          this.#directions = z;
        }
        break;
      default:
       this.#directions = 0 ;

    }
  }
}
// fin de la classe serpent


/**creation d'une classe porte
* @param{int}x- premiere cellules de la porte
* @param{int}y-deuxieme cellules de la porte
* @param{int}z- troisieme cellules de la porte
* @param{int}a-derniere cellules de la porte
* @param{int}lig- pour la ligne su laquelle les portes se trouve
* @param{boolean}juste-verifie si la reponse est juste sinon renvoie false
}
*/
class Porte {
	//attribut
	tabCor = [] ;//permet de recupere les coordonnee des portes
	juste; //boolean qui renvoie vrai quand la porte est la bonne reponse, false sinon

/**constructeur de la classe porte
* @param{int}x- premiere cellules de la porte
* @param{int}y-deuxieme cellules de la porte
* @param{int}z- troisieme cellules de la porte
* @param{int}a-derniere cellules de la porte
* @param{int}lig- pour la ligne su laquelle les portes se trouve
* @param{boolean}juste-verifie si la reponse est juste sinon renvoie false
*/
	constructor(x,y,z,a,lig,verifie) {
		this.tabCor=[
			[x,lig],
			[y,lig],
			[z,lig],
			[a,lig]
		];
		this.juste = verifie;
	}

}
//fin de la classe porte

/**
*/
class Enigme {
	//attribut
	question;
	reponse_vrai;
	reponses_fausses=[];

	constructor(question, reponse_vrai, reponse_fausse1, reponse_fausse2) {
		this.question = question;
		this.reponse_vrai = reponse_vrai;
		this.reponses_fausses = [reponse_fausse1, reponse_fausse2];
	}
}


/*variables*/
//variable pour les portes

var couleurPorte = "red";
var porte1 ;
var porte2 ;
var porte3 ;


//variable pour le tableau de jeu
var tab = [] ;
var colonnes = 40 ;
var lignes = 30 ;
var $plateau ;

var intervalId ; //pour actualise la grille

var serpent ;
var couleurTete = "magenta" ;
var couleurReste = "purple" ;

var level= 0;

var enigmes = [
	new Enigme("En CSS quel attribut est naturellement pas hérité ? ","padding","color", "font-family"),
	new Enigme("Comment peut on se connecter a une base de donnée en PHP ?", "PDO", "MySQL", "MongoDB"),
	new Enigme("En JQUERY, comment on sélectionne TOUTES les balises Div ?", "$(\"div\")","$(\".div\")","$(\"#div\")"),
	new Enigme("Quelle méthode permettra de creer un constructeur en JS","constructor(param)","constructeur(param)","Enigme(param)")
]; //permet de recupere les differentes enigmes


/*Traitement*/

$(document).ready(function() {
  creationTabDeJeu();
	init();
});

// Change the direction of the serpent of the user using the keybord
$(document).keydown(function(e){
	var code = e.keyCode ;
	if (code == GAUCHE || code == HAUT || code == DROITE || code == BAS){
		serpent.setDirection(code) ;
	}
});

$(document).keyup(function(e){

		serpent.setDirection(0) ;
});





/*partie fonction*/

/**init permetde creer le plateu de jeu ainsi que le serpent
*ainsi que les portes pour les enigmes
* @param {Snake} snake- creer le serpent au coordonnée
*/
function init(){
	serpent = new Serpent(20,25);
	porte1 = new Porte(7,8,9,10,4,false);
	porte2 = new Porte(18,19,20,21,4,false);
	porte3 = new Porte(29,30,31,32,4,false);

	// Mélanger la position de la bonne réponse à chaque niveau
	var slots = [
		{ span: "#reponse1", porte: porte1 },
		{ span: "#reponse2", porte: porte2 },
		{ span: "#reponse3", porte: porte3 }
	];
	for (var i = slots.length - 1; i > 0; i--) {
		var j = Math.floor(Math.random() * (i + 1));
		var tmp = slots[i]; slots[i] = slots[j]; slots[j] = tmp;
	}
	slots[0].porte.juste = true;
	$(slots[0].span).text(enigmes[level].reponse_vrai);
	$(slots[1].span).text(enigmes[level].reponses_fausses[0]);
	$(slots[2].span).text(enigmes[level].reponses_fausses[1]);

	$("#probleme").text(enigmes[level].question);

	// Update the board every 80 ms
	intervalId = setInterval(updatePlateau, 80);

}

/**
*permet de creer les cellules
*@param{cellules} cellules- cree les differentes cellules
*de taille 20x20
*le nombre de cellules est de 1200
*/
function creaCelluleJeu() {
    cellules = document.getElementsByClassName('cellules');
    for (var i = 0; i < 1200; i++) {
        cellules[i].style.backgroundColor = "black";
    }
}

/*reprend le fonctionnement de la creation de la grille du td3
*fait grace a la correction avec jquery
*fait par un etudiant
*/
function creationTabDeJeu(){

  for (var i = 0; i < colonnes; i++){
  		tab[i] = [];
  	}

  	$plateau = $("#plateau");

  	for (var j = 0; j < lignes; j++){
  		for (var i = 0; i < colonnes; i++){
  			// Create a div for the current cell
  			var $cellules = $("<div>", {class: "cellules"});
  			$plateau.append($cellules);

  			// Link the cell to the grid array
  			tab[i][j] = $cellules;
  		}
  	}
}

/*Permet d'actualisé le tableau de jeux*/
function updatePlateau(){
	// deplace le serpent
	bouge(serpent);
  draw();

}

/** permet de faire la zone pour le jeu et de faire le serpent
* @param {serpent}serpent- appelle la fonction qui dessine le Serpent
*
*/
function draw(){

  creaCelluleJeu();

  dessinerSerpent(serpent);
	dessinerPorte();
}

//dessiner les portes
function dessinerPorte(){

	for(var i = 0; i < porte1.tabCor.length; i++ ){
		tab[porte1.tabCor[i][0]][porte1.tabCor[i][1]].css("backgroundColor", couleurPorte);
		tab[porte2.tabCor[i][0]][porte2.tabCor[i][1]].css("backgroundColor", couleurPorte);
		tab[porte3.tabCor[i][0]][porte3.tabCor[i][1]].css("backgroundColor", couleurPorte);
	}
}

//dessiner le serpent
/**
 * Draw a serpent
 * @param {Serpent} serpent - The serpent to draw
 * @param {string} teteColor - met de la couleur a la tete du serpent
 * @param {string} queueColor - met de la couleur a la queue du serpent
 */
function dessinerSerpent(serpent){
	// Tail
	for (var k = 0; k < serpent.queue.length; k++){
		tab[serpent.queue[k][0]][serpent.queue[k][1]].css("background-color", couleurReste);
	}
  // Head
	tab[serpent.tete[0]][serpent.tete[1]].css("background-color", couleurTete);
}

//permet de mouvoir le serpent
/**
 * A function to make a serpent moves
 * @param {Serpent} serpent - the serpent
 */
function bouge(serpent){

  if(!outOfBoard(serpent)){
    //recuperation du code fait dans le td 3 etant tire de mon code personnelle

      switch(serpent.getDirection()){
    		case DROITE:
    			serpent.x++;
    			break;
    		case GAUCHE:
    			serpent.x--;
    			break;
    		case HAUT:
    			serpent.y--;
    			break;
    		case BAS:
    			serpent.y++;
    			break;
        case 0:
          break;
    		default:
    			console.log("Oups une erreur s'est produite lors du changement de direction");

    	}
        /*mouvement pour la queue*/
        serpent.queue[0] = serpent.tete;
        serpent.tete = [serpent.x, serpent.y];

        for (var i = serpent.queue.length - 1; i > 0; i--) {
            serpent.queue[i] = serpent.queue[i-1];
        }
      }
    estSurPorte();
    }

/**
 * Check if a serpent is out of the board
 * @param {serpent} serpent - the serpent
 * @return {boolean} true if we the serpent is out of the board, false else
 */
function outOfBoard(serpent){

  switch (serpent.getDirection()) {
    case DROITE:
      return serpent.tete[0]+1> colonnes-1 ;
      break;
    case GAUCHE:
      return serpent.tete[0]-1 < 0;
      break;
    case HAUT:
      return serpent.tete[1]-1 < 0 ;
      break;
    case BAS:
      return serpent.tete[1]+1 >lignes-1 ;
      break;
    default:
      return 0 ;
  }

}

function estSurPorte(){
	var portes = [porte1, porte2, porte3];
	for (var i = 0; i < portes.length; i++) {
		var p = portes[i];
		var xMin = p.tabCor[0][0];
		var xMax = p.tabCor[3][0];
		if (serpent.y == 4 && serpent.x >= xMin && serpent.x <= xMax) {
			if (p.juste) {
				suivante();
			} else {
				clearInterval(intervalId);
				init();
			}
			return;
		}
	}
}

//permet de passer a l'enigme suivante
function suivante(){
	if(level < 3){
		level++;
    clearInterval(intervalId);
    init();
	}
	else {
		document.getElementById("snake-win-form").submit();
	}

}
