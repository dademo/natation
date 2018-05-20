/*********************
Permet d'envoyer tout le contenu d'une liste au lieu d'envoyer seulement l'élément sélectionné

Utile lorsqu'on modifie l'ordre des éléments d'une liste

Requiert JQuery
*********************/

$('select.list-organize').each(function(index) {
var $target = this;

var $form = $(this).closest('form');

if($form.size() != 0) {
  // L'élément est dans un formulaire et celui-ci a été trouvé
  $($form).on('submit', function(e){
    console.log(e);
    //e.preventDefault();
  });
  console.log($($form));
}

});
