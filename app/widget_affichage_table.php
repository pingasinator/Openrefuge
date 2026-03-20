<?php
require_once("../obj/utils.inc.php");

if(!isset($f))
{
    $f = new utils(null,"test","test");
}

$om_widget = $f->get_inst__om_dbform(array(
    "obj" => "om_widget",
    "idx" => 0,
));

//récupère les arguments du widget et en fait un tableau associatif 
if(isset($content)){
    $arguments = $om_widget->get_arguments($content);
}

// affiche une table avec les paramètres du tableau associatif
if(isset($arguments)){
    $om_widget->widget_display_table($arguments);
}

