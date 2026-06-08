# Solutions et énigmes alternatives — Alice au Pays des Merveilles

## Solutions

Les énigmes sont présentées dans l'ordre de jeu actuel.

---

### Énigme 1 — Taille

**Question** : Quel attribut CSS pourrait changer la hauteur d'un pied à 20 cm ?

**Solution** : Cocher uniquement `height: 20cm;`

**Pourquoi** : `width` contrôle la largeur, pas la hauteur. `px` est une unité d'écran ; `cm` est une unité physique. La bonne propriété est `height` et la bonne unité est `cm`.

---

### Énigme 2 — Miam

**Question** : Comment faire pour ingérer les deux (`$Drink` et `$Eat`) à la suite, en PHP ?

**Solution** : Cocher uniquement `"$Drink . $Eat"`

**Pourquoi** : En PHP, l'opérateur de concaténation de chaînes est le point (`.`). `+` est réservé aux opérations numériques, `=` est une assignation, `*` une multiplication, `,` n'est pas un opérateur de chaîne.

---

### Énigme 3 — Tableau

**Question** : Comment sélectionner la 2ème personne du tableau `cartes` ?

**Solution** : `cartes[1]` ou `$cartes[1]`

**Pourquoi** : Les tableaux PHP sont indexés à partir de 0. La 1ère case est à l'index 0, la 2ème est donc à l'index 1. La syntaxe est `nomTableau[index]`.

---

### Énigme 4 — Carte

**Question** : Avec quelle fonction PHP Alice pourrait-elle choisir une des 52 cartes aléatoirement ?

**Solution** : `rand(1,52)` (les espaces autour de la virgule sont acceptés)

**Pourquoi** : La fonction `rand(min, max)` renvoie un entier aléatoire entre les bornes incluses. Pour un jeu de 52 cartes numérotées de 1 à 52, les paramètres sont `1` et `52`. Répondre simplement `rand()` déclenche un indice "Presque — précisez les paramètres".

---

### Énigme 5 — Snake

**Question** : Guidez le serpent vers la porte qui affiche la bonne réponse à chaque question de programmation.

**Solution** : Répondre correctement aux 4 questions de trivia en dirigeant le serpent vers la porte correspondante. La position de la bonne réponse change à chaque niveau.

**Questions et réponses** :

| Question | Bonne réponse |
|----------|---------------|
| En CSS, quel attribut n'est pas hérité naturellement ? | `padding` |
| Comment se connecter à une base de données en PHP ? | `PDO` |
| En jQuery, comment sélectionner toutes les balises `div` ? | `$("div")` |
| Quelle méthode permet de créer un constructeur en JS ? | `constructor(param)` |

**Contrôles** : touches directionnelles du clavier (↑ ↓ ← →).

---

### Énigme 6 — Fillette

**Question** : Cheshire garde la sortie. Quelles règles CSS rendraient `#Fillette` invisible ?

**Solution** : Cocher `#Fillette { visibility: hidden; }` ET `#Fillette { opacity: 0 }`

**Pourquoi** :
- `#Fillette` est un sélecteur d'**id** (le `#` cible un identifiant unique). `.Fillette` est un sélecteur de **classe** — incorrect ici.
- `visibility: hidden` et `opacity: 0` rendent tous les deux un élément invisible. Les deux sont valides pour masquer `#Fillette`.

---

### Énigme 7 — Lapin (climax)

**Question** : On dirait que le lapin veut nous dire quelque chose... Trouvez comment le faire parler.

**Solution** : Ouvrir la console du navigateur (F12 → onglet Console), lire le message affiché, puis entrer le mot `TEMPS` dans le champ de réponse. La casse n'est pas importante (`temps`, `Temps`, `TEMPS` sont tous acceptés).

**Pourquoi** : Le fichier JavaScript de la page contient `console.log("...le TEMPS est important.")`. "Faire parler l'ordinateur" signifie consulter la console développeur. La réponse est le mot mis en majuscules dans le message : **TEMPS** — référence au Lapin Blanc qui dit "Je n'ai pas le temps".

---

## Énigmes alternatives proposées

Pour chaque énigme, 3 à 5 alternatives sont présentées, classées de la plus simple à la plus ambitieuse à implémenter.

---

### Alternatives — Taille

**Alt 1 — L'Armoire**
Alice est bloquée devant une armoire trop haute. Le code CSS de l'armoire s'affiche partiellement. Le joueur modifie la propriété et l'unité pour que l'armoire fasse 20 cm. Feedback visuel : l'armoire se redimensionne en temps réel.

**Alt 2 — La Règle de la Reine**
La Reine mesure les têtes. Alice doit ajuster sa taille exactement. Deux sliders (propriété + valeur). La silhouette d'Alice change en temps réel. Valider quand `height = 20cm`.

**Alt 3 — Le Catalogue de Styles**
Un catalogue de "potions CSS" avec des étiquettes. Le joueur identifie celle qui rétrécit Alice en hauteur jusqu'à 20 cm. QCM visuel avec animation de chaque option.

**Alt 4 — Le Codex Royal**
Quatre règles CSS affichées dans des parchemins enluminés. Le joueur lit chaque règle et identifie son effet sur un personnage visuel avant de choisir.

---

### Alternatives — Miam

**Alt 1 — L'Étiquette**
Alice trouve deux étiquettes : "Bois-moi" et "Mange-moi". Elle doit écrire le message complet. Afficher `echo ____;` avec 4 boutons-opérateurs (`.`, `+`, `,`, `&`). Seul `.` fonctionne.

**Alt 2 — La Formule Magique**
Afficher un chaudron. Le joueur drag-and-drop les ingrédients (`$sel`, l'opérateur, `$sucre`) dans le bon ordre. Validation si la formule est syntaxiquement correcte.

**Alt 3 — Le Miroir**
Alice voit son reflet dire deux phrases. Pour les unir en une : compléter `$phrase1 ___ $phrase2;`. Saisie libre, validation PHP simulée en JS avec retour différencié selon l'opérateur saisi.

**Alt 4 — Le Parchemin de la Reine**
Un parchemin avec code PHP incomplet : `$message = $debut ?? $fin;`. Le joueur remplace `??` par le bon opérateur. 4 tampons royaux disponibles.

**Alt 5 — Le Télégramme**
Style enquête : un télégramme avec deux messages séparés, un formulaire PHP incomplet. Le joueur corrige le code. Retour immédiat via simulation d'exécution PHP en JS.

---

### Alternatives — Tableau

**Alt 1 — Le Rang des Soldats**
7 soldats-cartes alignés, numérotés 0 à 6. La Reine veut le 2ème soldat. Le joueur tape la syntaxe pour l'attraper. Visualisation directe de l'indexation 0-based.

**Alt 2 — La Liste des Invités**
Un carton d'invitation : `$invités = ["Chapelier", "Lièvre", "Alice"];`. Trois sous-questions progressives. Après 3 bonnes réponses, la question principale est débloquée.

**Alt 3 — La Bibliothèque du Lapin**
Des étagères numérotées 0, 1, 2... avec des livres. Le joueur récupère le livre à l'étagère 1 en écrivant la syntaxe PHP. Le format est illustré visuellement.

**Alt 4 — Le Vrai/Faux d'Indexation**
5 assertions à valider ou invalider (`$t[1]` est le 1er élément → Faux, etc.). Après 3 bonnes réponses consécutives, la question principale est posée.

---

### Alternatives — Carte

**Alt 1 — La Pioche Automatique**
Un robot pige une carte. Le joueur programme : `$robot->pige( ___( ___ , ___ ) );`. 3 blancs séparés = nom de fonction + 2 paramètres. Plus clair sur le format attendu.

**Alt 2 — Le Dé Charmé**
Alice lance un dé magique à N faces. Le joueur choisit N parmi 4, 6, 12, 52, puis la syntaxe PHP. Validation en 2 étapes.

**Alt 3 — La Boîte à Surprises**
Mini-éditeur de code avec complétion automatique. Le joueur tape `r` → suggestions : `rand()`, `random_int()`, `reset()`. Il sélectionne et complète les paramètres.

**Alt 4 — L'Enchanteur Aléatoire**
Puzzle de déduction : afficher plusieurs résultats (`Carte: 7`, `Carte: 32`, `Carte: 19`, `Carte: 1`). Le joueur identifie la fonction qui génère ces nombres entre 1 et 52, parmi 4 extraits de code.

**Alt 5 — La Formule du Chapelier**
Texte narratif : "Voici son code incomplet : `$tirage = _____`. Complétez." Avec indication explicite du format `fonction(départ, arrivée)`.

---

### Alternatives — Snake

**Alt 1 — Labyrinthe de Code**
Alice traverse un labyrinthe. À chaque carrefour, une question CSS/PHP. La bonne réponse ouvre le bon chemin. La réponse détermine directement la direction — mécaniquement cohérent.

**Alt 2 — Le Clavier de la Reine**
Type racer : la Reine énonce une propriété CSS, le joueur tape le code exact en moins de 10 secondes. 4 rounds de difficulté croissante.

**Alt 3 — Le Jardin de Roses**
Jeu de point & click : Alice peint des roses en rouge via CSS. Le joueur clique sur la bonne règle CSS parmi plusieurs pour changer la couleur. Narrativement parfait (scène iconique d'Alice).

**Alt 4 — La Course des Cartes**
4 cartes-soldats courent vers une ligne d'arrivée. Le joueur clique sur celle qui représente la bonne réponse avant qu'elles arrivent. 4 rounds.

**Alt 5 — Le Décodeur Royal**
Interface de cryptage : un message encodé de la Reine. Le joueur répond à 4 questions pour obtenir les 4 caractères du code de déchiffrement. Chaque bonne réponse révèle un caractère.

---

### Alternatives — Fillette

**Alt 1 — La Disparition Progressive**
Alice disparaît progressivement selon les propriétés CSS choisies. Le joueur sélectionne la bonne combinaison et voit Alice devenir invisible en temps réel.

**Alt 2 — Le Miroir Brisé**
4 miroirs affichent des versions d'Alice avec des règles CSS différentes appliquées. Le joueur identifie les deux miroirs où Alice est complètement invisible.

**Alt 3 — L'Atelier du Chapelier**
Interface éditeur CSS : `#Fillette { }` et `.Fillette { }` avec des champs à compléter. Deux décisions séquentielles (sélecteur puis propriété).

**Alt 4 — Le Grimoire des Sélecteurs**
Puzzle de correspondance : règles CSS à gauche, effets visuels à droite. Le joueur relie chaque règle à son effet. Deux paires permettent de "rendre Alice invisible".

---

### Alternatives — Lapin

**Alt 1 — Le Morse du Lapin**
Le lapin clignote en morse (via animation CSS). Le joueur décode le mot TEMPS en morse. Conserve le côté observation/découverte sans exiger les DevTools.

**Alt 2 — L'Horloger Fou**
Une horloge brisée affiche les lettres S, E, M, P, T en désordre. Le joueur réassemble les lettres pour former TEMPS.

**Alt 3 — Le Journal de Bord**
Une page avec du texte blanc sur fond blanc. Le joueur sélectionne le texte (Ctrl+A) ou change le contraste pour révéler TEMPS. Métaphore de l'inspection DOM.

**Alt 4 — Le Code Source**
Un bouton "Voir la source de la page" dans l'interface. Un clic ouvre une simulation de code source HTML avec un commentaire `<!-- Le mot de passe : TEMPS -->`. Introduit la notion de source HTML sans vraies DevTools.

**Alt 5 — La Clé du Réseau**
Le lapin envoie une requête HTTP (simulation). Un panneau de réseau fictif affiche les headers : `X-Secret-Word: TEMPS`. Introduction aux en-têtes HTTP dans un contexte narratif.
