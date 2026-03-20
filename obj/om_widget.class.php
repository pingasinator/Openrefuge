<?php
//$Id$ 
//gen openMairie le 30/12/2025 11:56

require_once PATH_OPENMAIRIE."obj/om_widget.class.php";

class om_widget extends om_widget_core {

    /**
     * affiche un lien dans un widget
     */
    function widget_display_link($content)
    {
        echo "<a href=\"" . (isset($content['link']) ? $content['link'] : "#") ."\"> ".
                $content['text'] . 
             "</a>";
    }

    /**
     * récupère les arguments et leurs valeurs dans une chaine de caractère et renvoi un tableau associatif
     * @param array $content
     * @return array<array|bool|string>|null
     */
    function get_arguments($content = null){

        if($content != null){
            $params = explode("\n",$content);
            $arguments = array();
            foreach($params as $value){
                $e = explode("=",$value,2);
                $arguments[$e[0]] = $e[1];
                if(strpos($e[1],",")){
                    $arguments[$e[0]] = explode(",",$e[1]);
                }
            
            }

            return $arguments;
        }
        
        return null;
    }


    /**
     * Affiche les tables dans le widget
     * @param array $content
     * @return void
     */
    function widget_display_table($content = null)
    {
        if($content != null){

            $sql = "select ";

            // ajoute les colonnes définies à la requête SQL
            if(isset($content['column']))
            {
                foreach($content['column'] as $column)
                {
                    $sql .= $column . ($column != $content['column'][count($content['column']) -1] ? "," : "");
                }
            }else{
                // génère la requete SQL avec toutes les colonnes  
                $sql .= "*";
            }
        
            // ajoute la table principale
            $sql .= " FROM ".DB_PREFIXE.$content['name'];

            // ajoute les jointures
            if(isset($content["join"])){
                if(is_array($content['join'])){
                    foreach($content['join'] as $join)
                    {
                        $sql .= " left join ". DB_PREFIXE. $join;
                    }
                }else if ($content['join'] != ""){
                     $sql .= " left join ". DB_PREFIXE. $content['join'];
                }
            }
        
            //triage par ID / clé primaire et exécution de la requête SQL
            $sql .= " ORDER BY ". (isset($content['p_key']) ? $content['p_key'] : $content['name'] ) . " DESC LIMIT " . ((isset($content["limit"]) && $content["limit"] != "") ? $content["limit"] : "10") .";";
            $res =& $this->f->db->query($sql);

            //echo $sql;
            //affichage du tableau
            echo '<table class="widget_table">';
            // Entête du tableau
            echo '<thead>';
            echo '<tr>';
                if(isset($content['column']))
                {
                    // affiche le nom d'affichage si il y en a un sinon affiche le nom par défaut
                    if(isset($content["replace"])){
                        foreach($content['replace'] as $column)
                        {
                            // défini la couleur de l'entête du tableau (rouge par défaut)
                            echo '<th style="background-color:'. (isset($content["color"]) && $content["color"] != "" ? $content["color"] : "red") .';">';
                            echo $column;
                            echo '</th>';
                        }
                    }else{
                        foreach($content['column'] as $column)
                        {
                            // défini la couleur de l'entête du tableau (rouge par défaut)
                            echo '<th style="background-color:'. (isset($content["color"]) && $content["color"] != "" ? $content["color"] : "red") .';">';
                            echo $column;
                            echo '</th>';
                        }
                    }
                }
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
                //affiche les Données dans le tableau
                while($row =& $res->fetchRow())
                {
                   echo '<tr>';
                    foreach($row as $data)
                    {
                        echo '<td>';
                        echo $data;
                        echo '</td>';
                    }        
                    echo '</tr>';
                }
            echo "</tbody>";
            echo "</table>";
        }
    }
}
