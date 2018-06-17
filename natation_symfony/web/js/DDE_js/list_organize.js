/*********************
Permet de réorganiser les éléments d'un select

On doit ajouter à un élément "SELECT" la classe "list-organize"

Lors du démarrage, le script recherchera tous les éléments "list-organize" et va créer à côté de ceux-ci deux boutons, l'un permettant de monter un élément, l'autre permettant de le descendre

Requiert JQuery
*********************/

$('select.list-organize').each(function(index) {
  var target = this;
  var oldSelect = this.cloneNode(true);
  //this.remove();

  function invert_data(orig, dest){
    $sel = $(oldSelect).find('option')[orig].cloneNode(true);
    $old = $(oldSelect).find('option')[dest].cloneNode(true);

    $(oldSelect).find('option')[orig].replaceWith($old);
    $(oldSelect).find('option')[dest].replaceWith($sel);
    $($(oldSelect).find('option')[dest]).prop('selected', true);
  }

  $elem = $('<div>');
  $elem.addClass('list-organize');
  $buttons = $('<div class="form-group list-organize-buttons">');
  $select = $('<div class="form-group list-organize-select">');
  $select.append(oldSelect);
  $btn_up = $('<button type="button" class="btn btn-default">').append($('<i class="fa fa-fw fa-angle-up">'));
  $btn_down = $('<button type="button" class="btn btn-default">').append($('<i class="fa fa-fw fa-angle-down">'));

  $btn_up.prop('disabled', true);
  $btn_down.prop('disabled', true);

  $btn_up.click(function(){
    var $selected = $(oldSelect).find(':selected');
    if($selected.length === 0){
      alert('Aucun élément sélectionné !');
    } else {
      var selectedIndex = $selected.index();
      /*
      $sel = $(oldSelect).find('option')[selectedIndex].cloneNode(true);
      $old = $(oldSelect).find('option')[selectedIndex - 1].cloneNode(true);

      $(oldSelect).find('option')[selectedIndex].replaceWith($old);
      $(oldSelect).find('option')[selectedIndex - 1].replaceWith($sel);
      $($(oldSelect).find('option')[selectedIndex - 1]).prop('selected', true);
      */

      invert_data(selectedIndex, selectedIndex - 1);

      /* MAJ des boutons */
      $btn_up.prop('disabled', (selectedIndex - 1 === 0));
      $btn_down.prop('disabled', (selectedIndex - 1 === $(oldSelect).find('option').size() - 1));
    }
  });

  $btn_down.click(function(){
    var $selected = $(oldSelect).find(':selected');
    if($selected.length === 0){
      alert('Aucun élément sélectionné !');
    } else {
      var selectedIndex = $selected.index();
      /*
      $sel = $(oldSelect).find('option')[selectedIndex].cloneNode(true);
      $old = $(oldSelect).find('option')[selectedIndex + 1].cloneNode(true);

      $(oldSelect).find('option')[selectedIndex].replaceWith($old);
      $(oldSelect).find('option')[selectedIndex + 1].replaceWith($sel);
      $($(oldSelect).find('option')[selectedIndex + 1]).prop('selected', true);
      */

      invert_data(selectedIndex, selectedIndex + 1);

      /* MAJ des boutons */
      $btn_up.prop('disabled', (selectedIndex + 1 === 0));
      $btn_down.prop('disabled', (selectedIndex + 1 === $(oldSelect).find('option').size() - 1));
    }
  });

  $(oldSelect).change(function(){
    $selected = $(this).find(':selected');
    $btn_up.prop('disabled', ($selected.index() === 0));
    $btn_down.prop('disabled', ($selected.index() === $(this).find('option').size() - 1));
  });

  $buttons.append($btn_up);
  $buttons.append('<br/>');
  $buttons.append($btn_down);

  $elem.append($buttons);
  $elem.append($select);

  //$('body').append($elem);
  $(this).replaceWith($elem);
});
