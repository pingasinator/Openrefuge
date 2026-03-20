\copy animal_espece (animal_espece,nom) from 'refuge_data/animal_espece.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy animal_race (animal_race,nom,animal_espece) from 'refuge_data/animal_race.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy animal_sexe (animal_sexe,libelle) from 'refuge_data/animal_sexe.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy civilite (civilite,libelle) from 'refuge_data/civilite.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy hebergement_type (hebergement_type,libelle) from 'refuge_data/hebergement_type.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy provenance (provenance,libelle) from 'refuge_data/provenance.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy soin_type (soin_type,libelle) from 'refuge_data/soin_type.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy unite_mesure (unite_mesure,libelle) from 'refuge_data/unite_mesure.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy ville (ville,nom,code_postal) from 'refuge_data/ville.csv' with (FORMAT csv, HEADER true, DELIMITER ',');



\copy om_widget (om_widget,libelle,lien,texte,type,script,arguments) from 'refuge_data/om_widget.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy om_dashboard (om_dashboard,om_profil,bloc,position,om_widget) from 'refuge_data/om_dashboard.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy om_requete (om_requete,code,libelle,description,requete,merge_fields,type,classe,methode) from 'refuge_data/om_requete.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy om_sousetat (om_sousetat,om_collectivite,id,libelle,actif,titre,titrehauteur,titrefont,titreattribut,titretaille,titrebordure,titrealign,titrefond,titrefondcouleur,titretextecouleur,intervalle_debut,intervalle_fin,entete_flag,entete_fond,entete_orientation,entete_hauteur,entetecolone_bordure,entetecolone_align,entete_fondcouleur,entete_textecouleur,tableau_largeur,tableau_bordure,tableau_fontaille,bordure_couleur,se_fond1,se_fond2,cellule_fond,cellule_hauteur,cellule_largeur,cellule_bordure_un,cellule_bordure,cellule_align,cellule_fond_total,cellule_fontaille_total,cellule_hauteur_total,cellule_fondcouleur_total,cellule_bordure_total,cellule_align_total,cellule_fond_moyenne,cellule_fontaille_moyenne,cellule_hauteur_moyenne,cellule_fondcouleur_moyenne,cellule_bordure_moyenne,cellule_align_moyenne,cellule_fond_nbr,cellule_fontaille_nbr,cellule_hauteur_nbr,cellule_fondcouleur_nbr,cellule_bordure_nbr,cellule_align_nbr,cellule_numerique,cellule_total,cellule_moyenne,cellule_compteur,om_sql) from 'refuge_data/om_sousetat.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy om_etat (om_etat,om_collectivite,id,libelle,actif,orientation,format,logo,logoleft,logotop,titre_om_htmletat,titreleft,titretop,titrelargeur,titrehauteur,titrebordure,corps_om_htmletatex,om_sql,se_font,se_couleurtexte,margeleft,margetop,margeright,margebottom,header_om_htmletat,header_offset,footer_om_htmletat,footer_offset) from 'refuge_data/om_etat.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
\copy om_droit(om_droit,libelle,om_profil) from 'refuge_data/om_droit.csv' with (FORMAT csv, HEADER true, DELIMITER ',');
