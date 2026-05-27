/**
 * Ce script javascript ...
 *
 * @package framework_openmairie
 * @version SVN : $Id: localisation.js 4348 2018-07-20 16:49:26Z softime $
 */

//
$(function() {
   localisation_bind_draggable();
});
//
function localisation_bind_draggable() {
    $("#draggable").draggable({ containment: "parent" });
    $("#draggable").dblclick(function() {
        infos = $(this).attr("class");
        infos = infos.split(" ");
        infos = infos[0].split(";");
        // Récupération de la position de l'élément
        position = $(this).position();
        x = parseInt(position.left);
        y = parseInt(position.top);
        //
        localisation_return(infos[0], infos[1], x, infos[2], y);
        return true;
    });
}
// Cette fonction permet de retourner les informations sur le fichier téléchargé
// du formulaire d'upload vers le formulaire d'origine
function localisation_return(form, champ_x, value_x, champ_y, value_y) {
    $("form[name|="+form+"] #"+champ_x, window.opener.document).attr('value', value_x);
    $("form[name|="+form+"] #"+champ_y, window.opener.document).attr('value', value_y);
    window.close();
}
