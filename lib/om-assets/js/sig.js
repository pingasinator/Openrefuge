// affectation d'un mode d'action :
	// Géolocalisation,
	// Information,
	// Navigation,
	// Edition,
	// Formulaire,
	// Mesure surface,
	// Mesure distance
function map_set_mode_action(mode) {
	if (mode_action != mode && mode_action == 'Edition') map_clicEditClose();
	mode_action=mode;
	map_affiche_info("");
	map.removeControl(selectControl);
	selectControl_layers = [];
	var bMarkers = false;
	var bData = false;
	var bDataEdit = false;
	var bSelectControl = false
	measureControls['line'].deactivate();
	measureControls['polygon'].deactivate();
	if (mode_action == 'Géolocalisation'){
		map_clear_getfeatures();
		bGeolocate=true;
	} else {
		if (bGeolocate==true) {
			vector = map.getLayersByName("Geolocate")[0];
			vector.removeAllFeatures();
			bGeolocate=false;
		}
		if (mode_action=='Information'){
			bSelectControl = true;
			bMarkers = true;
			bData = true;
		} else if (mode_action=='Navigation'){
		} else if (mode_action=='Edition'){
			map_clear_getfeatures();
			bSelectControl=true;
		} else if (mode_action=='Formulaire'){
		} else if (mode_action=='Mesure surface'){
			map_clear_getfeatures();
			measureControls['polygon'].activate();
		} else if (mode_action=='Mesure distance'){
			map_clear_getfeatures();
			measureControls['line'].activate();
		}
	}
	if (bMarkers == true) selectControl_layers.push(markersLayer);
	if (bData == true) {
		for(var i=0; i<lst_idx_data_layers.length; i++) {
			selectControl_layers.push(lst_idx_data_layers[i]);
		}
	}
	if (bDataEdit == true) {
		for(var i=0; i<lst_idx_data_layers_edit.length; i++) {
			selectControl_layers.push(lst_idx_data_layers_edit[i]);
		}
	}
	if (selectControl_layers.length > 0) {
		map_add_SelectControl();
		if (bSelectControl) selectControl.activate(); else selectControl.deactivate();
	}
}

// function popupIt pour utilisation dans sig
function map_popupIt(objsf, link, width, height, callback, callbackParams) {
    var dialog = $('<div id=\"sousform-'+objsf+'\"></div>').insertAfter('#map-id');
    $('<input type=\"text\" name=\"recherchedyn\" id=\"recherchedyn\" value=\"\" class=\"champFormulaire\" style=\"display:none\" />').insertAfter('#sousform-'+objsf);
    $.ajax({
        type: "GET",
        url: link,
        cache: false,
        success: function(html){
            dialog.empty();
            dialog.append(html);
            om_initialize_content();
            $(dialog).dialog({
            close: function(ev, ui) {
                if (typeof(callback) === "function") {
                    callback(callbackParams);
                }
                $(this).remove();
            },
            resizable: true,
            modal: true,
            width: "65%", // *** jlb ancien = variable width qui ne fonctionne pas,
            height: height,
            position: 'center center'
          });
        },
        async : false
    });
    $('#sousform-'+objsf).on("click",'a.retour',function() {
        $(dialog).dialog('close').remove();
        return false;
    });
}

// implémentation simplifiée de l'utilisation de popupIt dans la fonction SIG
function map_popup(url) {
	map_popupIt(obj,url, 'auto','auto','','');
}

// vide les div du div map-getfeatures
function map_clear_getfeatures() {
	$('#map-getfeatures-markers').empty();
	$('#map-getfeatures-flux').empty();
	$('#map-getfeatures-dats').empty();
}

// ouverture du div map-getfeatures
function map_open_getfeatures() {
	$("#map-getfeatures").show();
	vis_getfeatures = true;
}

// ouverture du div map-getfeatures
function map_close_getfeatures() {
	$("#map-getfeatures").hide();
	vis_getfeatures=false;
}

// affichage dans la zone info
function map_affiche_info(message) {
	$("#map-infos").text(mode_action+message);
}

// affichage dans la zone titre
function map_affiche_titre() {
	$("#map-titre").empty();
	if (idx_sel == '-1') {
		$("#map-titre").append("<td><font id='map-titre-id' class='ui-corner-all'>"+obj+"&nbsp;</font></td>");
	} else {
		$("#map-titre").append("<td><font id='map-titre-id' class='ui-corner-all'>"+obj+": "+idx_sel+"</font></td>");
	}
}

// Traitement du select d'un élement de la couche des marqueurs
function map_on_select_feature_marker(event) {
	if (select_marker != null) selectControl.unselect(select_marker);
	$("#map-getfeatures-markers").empty();
	select_marker = event.feature;
	if (mode_action=='Information') {
		map_clic(event);
	}
}

// Traitement du unselect d'un élement de la couche des marqueurs
function map_on_unselect_feature_marker(event) {
	select_marker=null;
	$("#map-getfeatures-markers").empty();
}

// Traitement du select d'un élement de la couche des datas
function map_on_select_feature_data(event) {
	if (select_data != null) selectControl.unselect(select_data);
	if (select_data != null) {
		selectControl.unselect(select_data);
	}
	select_data = event.feature;
	if (mode_action=='Information') {
		map_clic(event);
		map_affiche_titre();

	}
}

// Traitement du unselect d'un élement de la couche des datas
function map_on_unselect_feature_data(event) {
}

// Traitement du select d'un élement de la couche des datas
function map_on_select_feature_data_edit(event) {
	//map_affiche_info(": "+event.feature.attributes['idx']);
}

// Traitement du unselect d'un élement de la couche des datas
function map_on_unselect_feature_data_edit(event) {
}

// récupère la valeur d'un attribut (traitement GetFeatureInfo)
function traiteGetFeatureInfoRecAttribut(attributes,name) {
	res="";
	for (var k=0; k < attributes.length; k++) {
		var att=attributes[k];
		if ( att.getAttribute('name')==name) {
			res=att.getAttribute('value');
			break;
		}
	}
	return res;
}

// récupère la valeur d'un attribut et formate la restitution (traitement GetFeatureInfo)
function traiteGetFeatureInfoRecAttributFormat(attributes,name,title, formatage) {
	res=traiteGetFeatureInfoRecAttribut(attributes,name);
	if (res!="")
	{
			res=formatage.replace('[TITLE]', title).replace('[VALUE]',res);
	}
	return res;
}

// traitement pour chaque GetFeatureInfo
function traiteGetFeatureInfo(i,rText,res) {
// jlb ajout css pour affichage des flux dans infos
/*
	var xmlf = new OpenLayers.Format.XML();
	var data = xmlf.read(rText).documentElement;
	featureInfo = {};
	var layerInfo = xmlf.getElementsByTagNameNS(data,'*','Layer');
	for (var i=0; i < layerInfo.length;i++) {
		var layer = layerInfo[i];
		var layerName = layer.getAttribute('name');
		featureInfo[layerName] = {};
		var features = xmlf.getElementsByTagNameNS(layer,'*','Feature');
		if (features.length>0) {
			res+="<LI>"+layerName+"";
			for( var j=0; j<features.length;j++) {
				var feature=features[j];
				var featureId=feature.getAttribute('id');
				featureInfo[layerName][featureId] = {};
				var attributes = xmlf.getElementsByTagNameNS(feature, '*','Attribute');
				for (var k=0; k < attributes.length; k++) {
					var att=attributes[k];
					res+="<BR>"+att.getAttribute('name')+': '+att.getAttribute('value')+"";
				}
			}
			res+="</LI>";
		}
	}
	return res;

*/
	var xmlf = new OpenLayers.Format.XML();
	var data = xmlf.read(rText).documentElement;
	featureInfo = {};
	var layerInfo = xmlf.getElementsByTagNameNS(data,'*','Layer');
	for (var i=0; i < layerInfo.length;i++) {
		var layer = layerInfo[i];
		var layerName = layer.getAttribute('name');
		featureInfo[layerName] = {};
		var features = xmlf.getElementsByTagNameNS(layer,'*','Feature');
		if (features.length>0) {
			res+="<center><table id='flux-table' border='0px' cellspacing='0' cellpadding='0'><tr><td id='flux-td-titre'><center>"+layerName.toUpperCase()+"</center></td></tr>";
                        var arch_layername = layerName.toUpperCase();
			for( var j=0; j<features.length;j++) {
				var feature=features[j];
				var featureId=feature.getAttribute('id');
				featureInfo[layerName][featureId] = {};
				var attributes = xmlf.getElementsByTagNameNS(feature, '*','Attribute');
				for (var k=0; k < attributes.length; k++) {
					var att=attributes[k];
                                           if(att.getAttribute('value')) {
                                                var temp=att.getAttribute('name').toUpperCase();
                                                if (k==0){
                                                   var arch_champs_un=att.getAttribute('name').toUpperCase();
                                                }
                                                var rechgeom =att.getAttribute('value').search("SRID");
                                                if(rechgeom == -1){

                                                        if (att.getAttribute('name').toUpperCase()==arch_layername || att.getAttribute('name').toUpperCase()==arch_champs_un ){
                                                             res+="<tr><td id='flux-td-soustitre'><center>"+att.getAttribute('name')+"&nbsp;"+att.getAttribute('value')+"</center></td></tr>";
                                                        }else{
                                                             res+="<tr><td> <font id='flux-td-lib-champs'>&nbsp;"+att.getAttribute('name').toUpperCase()+"&nbsp;:</font><font id='flux-td-value-champs'>&nbsp;"+att.getAttribute('value')+"</font></td></tr>";
                                                        }
                                                 }

                                            }
                                }
			}
			res+="</table></center><br>";
		}
	}
	return res;
}

// Traitement du clic sur la carte
function map_clic(e) {
        // jlb ajout css interface infos
	var contenu_markers="";
	if (mode_action=='Géolocalisation') {
	}
	else if (mode_action=='Information') {
		if (select_marker != null) {
			$("#map-getfeatures-markers").empty();
			//$("#map-getfeatures-markers").append("<center><strong>Marqueur(s)</strong></Center>");
                        $("#map-getfeatures-markers").append("<center><div id='markers-div-titre'><br><strong>Marqueur(s)</strong></div></Center>");
			var lst_attributes = new Array();
			var lst_optional_attributes = new Array();
			var ioa = 0;
			for(var key in select_marker.attributes) {
				lst_attributes[key]= select_marker.attributes[key].split('²');
				if (key != 'id' && key != 'titre' && key != 'description') {
					lst_optional_attributes[ioa]=key;
					ioa=ioa+1;
				}
			}
			for(var i=0; i<lst_attributes['id'].length; i++) {
				//lst_attributes['id'][i]='<li>'+lst_attributes['titre'][i]+' ('+lst_attributes['id'][i]+')<BR>'+lst_attributes['description'][i];
                                lst_attributes['id'][i]='<li>'+lst_attributes['titre'][i]+' ('+lst_attributes['id'][i]+')<BR><span id="markers-lib-champs">'+lst_attributes['description'][i]+'</span><span id="markers-value-champs">';
				for(var j=0; j<lst_optional_attributes.length; j++) {
					//lst_attributes['id'][i]=lst_attributes['id'][i]+'<BR>'+lst_optional_attributes[j]+': '+lst_attributes[lst_optional_attributes[j]][i];
                                        lst_attributes['id'][i]=lst_attributes['id'][i]+'</span><BR><span id="markers-lib-champs">'+lst_optional_attributes[j]+'</span> : <span id="markers-value-champs">'+lst_attributes[lst_optional_attributes[j]][i]+'</span>';
				}
				lst_attributes['id'][i]=lst_attributes['id'][i]+'</li>';
				$("#map-getfeatures-markers").append(lst_attributes['id'][i]);
			}
		}
		if (select_data != null) {
			idx_sel = select_data.attributes['idx'];

			map_copyDataToEditLayers();
			$("#map-getfeatures-datas").empty();
			//$("#map-getfeatures-datas").append("<center><strong>Donnée</strong></Center>");
			//$("#map-getfeatures-datas").append('<li>'+select_data.attributes['obj']+': '+select_data.attributes['idx']+'</LI>');
                        $("#map-getfeatures-datas").append("<center><div id='datas-div-titre'><br><strong >Données</strong></div></Center>");
			$("#map-getfeatures-datas").append("<span id='datas-lib-champs'>"+select_data.attributes['obj']+"&nbsp;:&nbsp;<span id='datas-value-champs'> "+select_data.attributes['idx']+"</span>");
		}
		$("#map-getfeatures-flux").empty();
		var xy;
		if (typeof e.xy !== "undefined") {
			xy=e.xy;
		}else {
			xy=mouseControl.lastXy;
		}
		for(var i=0; i<lst_idx_flux.length; i++) {
			if (fl_m_panier[i] != 't' && (fl_w_cache_type[i]=='' || fl_w_cache_type[i]=='TCF' || fl_w_cache_type[i]=='SMT')) {
				var layer;
				if (fl_m_baselayer[i] == 't') {
					layer = lst_base_layers[lst_idx_flux[i]];
				} else {
					layer = lst_overlays[lst_idx_flux[i]];
				}
				if (typeof layer !== "undefined") {
					if (layer.getVisibility() == true) {
						var chemin;
						var couches;
						if (fl_w_cache_type[i]=='') {
							chemin = fl_w_chemin[i];
							couches = layer.params.LAYERS;
						}else {
							chemin = fl_w_cache_gfi_chemin[i];
							couches = fl_w_cache_gfi_couches[i];
						}
						if (chemin != '' && couches != '') {
							var params = {
								REQUEST: "GetFeatureInfo",
								BBOX: map.getExtent().toBBOX(),
								SERVICE: "WMS",
								VERSION: "1.3.0",
								X: Math.round(xy.x),
								Y: Math.round(xy.y),
								INFO_FORMAT: 'text/xml',
								QUERY_LAYERS: couches,
								FEATURE_COUNT: 50,
								Layers: couches,
								WIDTH: map.size.w,
								HEIGHT: map.size.h,
								styles: layer.params.STYLES,
								srs: "EPSG:"+defBaseProjection
							};
							if(layer.params.CQL_FILTER != null) {
								params.cql_filter = layer.params.CQL_FILTER;
							}
							if(layer.params.FILTER != null) {
								params.filter = layer.params.FILTER;
							}
							if(layer.params.FEATUREID) {
								params.featureid = layer.params.FEATUREID;
							}
							var request = OpenLayers.Request.GET({
								url: chemin,
								params: params,
								success: function (req) {
										var res='';
										res=traiteGetFeatureInfo(i,req.responseText,res);
										if ($("#map-getfeatures-flux").text() == '' && res != '') {
											//$("#map-getfeatures-flux").append("<center><strong>flux(s)</strong></Center>");
                                                                                        $("#map-getfeatures-flux").append("<center><div id='flux-div-titre'><br><strong>flux(s)</strong></div></Center>");
										}
										$("#map-getfeatures-flux").append(res);
									}
							});
						}
					}
				}
			}
		}
	}
	else if (mode_action=='Navigation') {
	}
	else if (mode_action=='Edition') {
		if (action_edit_champ=='modify') {
			lstEditControls['modify'].vertices=lstEditControls['modify'].vertices;
		}
		if (action_edit_champ=='cart') {
			i=$('#map-edit-cart-lst-id').val();
			if (i != "") {
				var xy;
				if (typeof e.xy !== "undefined") {
					xy=e.xy;
				}else {
					xy=mouseControl.lastXy;
				}
				var chemin;
				var couches;
				if (fl_w_cache_type[i]=='') {
					chemin = fl_w_chemin[i];
					couches = selectCartLayer.params.LAYERS;
				}else {
					chemin = fl_w_cache_gfi_chemin[i];
					couches = fl_w_cache_gfi_couches[i];
				}
				if (chemin != '' && couches != '') {
					var params = {
						REQUEST: "GetFeatureInfo",
						BBOX: map.getExtent().toBBOX(),
						SERVICE: "WMS",
						VERSION: "1.3.0",
						X: Math.round(xy.x),
						Y: Math.round(xy.y),
						INFO_FORMAT: 'text/xml',
						QUERY_LAYERS: couches,
						FEATURE_COUNT: 50,
						Layers: couches,
						WIDTH: map.size.w,
						HEIGHT: map.size.h,
						styles: selectCartLayer.params.STYLES,
						srs: "EPSG:"+defBaseProjection
					};
					if(selectCartLayer.params.CQL_FILTER != null) {
						params.cql_filter = selectCartLayer.params.CQL_FILTER;
					}
					if(selectCartLayer.params.FILTER != null) {
						params.filter = selectCartLayer.params.FILTER;
					}
					if(selectCartLayer.params.FEATUREID) {
						params.featureid = selectCartLayer.params.FEATUREID;
					}
					var request = OpenLayers.Request.GET({
						url: chemin,
						params: params,
						success: function (req) {
								var res = '';
								var cart_id=$('#map-edit-cart-lst-id').val();
								var xmlf = new OpenLayers.Format.XML();
								var data = xmlf.read(req.responseText).documentElement;
								featureInfo = {};
								var layerInfo = xmlf.getElementsByTagNameNS(data,'*','Layer');
								for (var i=0; i < layerInfo.length;i++) {
									var layer = layerInfo[i];
									var layerName = layer.getAttribute('name');
									if (layerName==fl_m_pa_layer[cart_id]) {
										featureInfo[layerName] = {};
										var features = xmlf.getElementsByTagNameNS(layer,'*','Feature');
										if (features.length>0) {
											for( var j=0; j<features.length;j++) {
												var feature=features[j];
												var featureId=feature.getAttribute('id');
												featureInfo[layerName][featureId] = {};
												var attributes = xmlf.getElementsByTagNameNS(feature, '*','Attribute');
												for (var k=0; k < attributes.length; k++) {
													var att=attributes[k];
													if (fl_m_pa_attribut[cart_id] == att.getAttribute('name')) {
														res = fl_m_pa_encaps[cart_id]+att.getAttribute('value')+fl_m_pa_encaps[cart_id];
														var index = $.inArray(att.getAttribute('value'), cart_val);
														if (index == -1) cart_val.push(att.getAttribute('value'));
														else cart_val.splice(index, 1);
													}
												}
											}
										}
									}
								}
								if (typeof cartLayer !== "undefined") {
									cartLayer.setVisibility(false);
									cartLayer.removeAllFeatures();
									map.removeLayer(cartLayer);
									cartLayer = undefined;
								}
								fichier_jsons = base_url_map_get_geojson_cart+'&obj='+obj+'&idx='+idx+'&etendue='+etendue+'&reqmo='+reqmo+
									'&premier='+premier+'&tricol='+tricol+'&advs_id='+advs_id+
									'&valide='+valide+'&style='+style+'&onglet='+onglet+'&idx_sel='+idx_sel+'&cart='+cart_id+'&lst='+cart_val.join(',');
								cartLayer = new OpenLayers.Layer.Vector(
									"panier",
									{
										projection: displayProjection,
										strategies: [new OpenLayers.Strategy.Fixed()],
										protocol: new OpenLayers.Protocol.HTTP(
											{
												url: fichier_jsons,
												format: new OpenLayers.Format.GeoJSON()
											}
										)
									}
								);
								map.addLayer(cartLayer);
							}
					});
				}
			}
		}
	}
	else if (mode_action=='Formulaire') {
	}
	else if (mode_action=='Mesure distance') {
	}
	else if (mode_action=='Mesure surface') {
	}
}

// vide les couches vecteurs d'édition
function map_emptyEditLayers() {
	for(var i=0; i<lst_idx_data_layers_edit.length; i++) {
		lst_idx_data_layers_edit[i].removeAllFeatures();
	}
}

// copie les données geoson correspondant à l'idx sélectionné dans les couches d'édition correspondantes
function map_copyDataToEditLayers() {
	map_emptyEditLayers();
	for(var i=0; i<lst_idx_data_layers.length; i++) {
		vector=lst_idx_data_layers[i];
		var lstFeatures= vector.getFeaturesByAttribute('idx',idx_sel);
		for(var j=0; j<lstFeatures.length; j++) {
			lst_idx_data_layers_edit[i].addFeatures(lstFeatures[j].clone());
		}
		lst_idx_data_layers_edit[i].redraw();
	}
}

// Traitement clic Edit Draw Point
function map_clicEditDrawPoint() {
	action_edit_champ='draw-point';
	map_deactivate_all_edit_control('point');
	currentEditControl = lstEditControls['point'];
	map_RefreshEditBar();
	map_affiche_info(': dessiner un point');
}

// Traitement clic Edit Draw Line
function map_clicEditDrawLine() {
	action_edit_champ='draw-line';
	map_deactivate_all_edit_control('line');
	currentEditControl = lstEditControls['line'];
	map_RefreshEditBar();
	map_affiche_info(': dessiner une ligne');
}

// Traitement clic Edit Draw Polygon
function map_clicEditDrawPolygon() {
	action_edit_champ='draw-polygon';
	map_deactivate_all_edit_control('polygon');
	currentEditControl = lstEditControls['polygon'];
	map_RefreshEditBar();
	map_affiche_info(': dessiner un polygone');
}

//traitement du onChange sur le nombre de côté du polygone régulier
function map_EditDrawRegularChange() {
	var nb_side = 4;
	if (map_is_int($('#map-edit-draw-regular-nb').val()) != false)
		nb_side = map_is_int($('#map-edit-draw-regular-nb').val());
	else
		$('#map-edit-draw-regular-nb').val(nb_side);
	lstEditControls['regular'].handler.sides = nb_side;
}

// Traitement clic Edit Draw Polygon
function map_clicEditDrawRegular() {
	action_edit_champ='draw-regular';
	map_deactivate_all_edit_control('regular');
	currentEditControl = lstEditControls['regular'];
	map_RefreshEditBar();
	map_affiche_info(': dessiner un polygone régulier');
}

// Traitement clic Edit modify
function map_clicEditDrawModify() {
	action_edit_champ='modify';
	map_deactivate_all_edit_control('modify');
	//lstEditControls['select'].activate();
	if (typeof currentEditControl !== "undefined")
		currentEditControl.deactivate();
	map_RefreshEditBar();
	// *** jlb ajout type geometrie dans fenetre message
	// map_affiche_info(': modifier une géométrie');
	map_affiche_info(': modifier une géométrie'+' '+cg_geometrie[select_edit_champ]);
}

// Traitement clic Edit select
function map_clicEditSelect() {
	action_edit_champ='select';
	map_deactivate_all_edit_control('select');
	if (typeof currentEditControl !== "undefined") currentEditControl.deactivate();
	map_RefreshEditBar();
	//map_affiche_info(': sélection');
	// *** jlb ajout type geometrie dans fenetre message
	map_affiche_info(': sélection ou annuler sélection '+' '+cg_geometrie[select_edit_champ]);
}

// Traitement clic Edit navigate
function map_clicEditNavigate() {
	action_edit_champ='navigate';
	map_deactivate_all_edit_control('');
	map_RefreshEditBar();
	map_affiche_info(': naviguer');
}

// Traitement clic Edit Erase
function map_clicEditErase() {
	// *** jlb ajout type geometrie dans fenetre message
	//map_affiche_info(': effacer la sélection');
	map_affiche_info(': effacer la sélection'+' '+cg_geometrie[select_edit_champ]);
	map_deactivate_all_edit_control('select');
	lst_idx_data_layers_edit[select_edit_champ].removeFeatures(lst_idx_data_layers_edit[select_edit_champ].selectedFeatures);
}

// test si une valeur est un entier
function map_is_int(value){
  if((parseFloat(value) == parseInt(value)) && !isNaN(value)){
      return parseInt(value);
  } else {
      return false;
  }
}

// désactive tous les controles d'édition sauf sauf
function map_deactivate_all_edit_control(sauf) {
	for(var key in lstEditControls) {
		if (key==sauf)
			lstEditControls[key].activate();
		else
			lstEditControls[key].deactivate();
    }
	if (action_edit_champ!='cart') {
		$('#map-edit-cart-lst-id').val('');
		$('#map-edit-cart-lst-id').change();
	}
}

// affiche/cache les éléments de la barre d'outils d'édition
function map_RefreshEditBar() {
	for(var key in edit_toolbar) {
		edit_toolbar[key]=true;
	}
	if (cg_geometrie[select_edit_champ] == 'point' || cg_geometrie[select_edit_champ] == 'multipoint') {
		edit_toolbar["#map-edit-draw-line"]=false;
		edit_toolbar["#map-edit-draw-polygon"]=false;
		edit_toolbar["#map-edit-draw-regular"]=false;
		edit_toolbar["#map-edit-draw-regular-nb"]=false;
		edit_toolbar['#map-edit-cart-lst-id']=cart_type['point'];
	}else if (cg_geometrie[select_edit_champ] == 'linestring' || cg_geometrie[select_edit_champ] == 'multilinestring') {
		edit_toolbar["#map-edit-draw-point"]=false;
		edit_toolbar["#map-edit-draw-polygon"]=false;
		edit_toolbar["#map-edit-draw-regular"]=false;
		edit_toolbar["#map-edit-draw-regular-nb"]=false;
		edit_toolbar['#map-edit-cart-lst-id']=cart_type['linestring'];
	}else if (cg_geometrie[select_edit_champ] == 'polygon' || cg_geometrie[select_edit_champ] == 'multipolygon') {
		edit_toolbar["#map-edit-draw-point"]=false;
		edit_toolbar["#map-edit-draw-line"]=false;
		edit_toolbar["#map-edit-draw-regular-nb"]=false;
		edit_toolbar['#map-edit-cart-lst-id']=cart_type['polygon'];
	}
	if (action_edit_champ=='navigate') {
		edit_toolbar["#map-edit-draw-regular-nb"]=false;
	} else if (action_edit_champ=='select') {
		edit_toolbar["#map-edit-draw-regular-nb"]=false;
	} else if (action_edit_champ=='modify') {
		edit_toolbar["#map-edit-draw-regular-nb"]=false;
	} else if (action_edit_champ=='draw-point') {
	} else if (action_edit_champ=='draw-line') {
	} else if (action_edit_champ=='draw-polygon') {
	} else if (action_edit_champ=='draw-regular') {
		edit_toolbar["#map-edit-draw-regular-nb"]=true;
	}
	for(var key in edit_toolbar) {
		if (edit_toolbar[key]==true) {
			$(key).show();
		} else {
			$(key).hide();
		}
	}
}

//  calcule et valide les champs géométriques dont les composants sont sélectionnés
function map_computeGeom(sel, enreg) {
	var min = 0;
	var max = 0;
	if (sel=='') {
		min = 0;
		max = lst_idx_data_layers_edit.length-1;
	} else {
		min = sel;
		max = sel;
	}
	var data = new Array();
	var geojson_format = new OpenLayers.Format.GeoJSON();
	var sGeom = '';
	var sep ='';
	var sepEnr ='';
	var geojson='';
	for (c=min; c<=max; c++) {
		sGeom = '';
		if (cg_maj[c] == 't') {
			sep ='';
			if (lst_idx_data_layers_edit[c].selectedFeatures.length > 0) {
				for(var i=0; i< lst_idx_data_layers_edit[c].selectedFeatures.length; i++) {
					g=geojson_format.write(lst_idx_data_layers_edit[c].selectedFeatures[i].geometry);
					var geom = lst_idx_data_layers_edit[c].selectedFeatures[i].geometry.clone()
					sGeom=sGeom+sep+"(SELECT ST_GeomFromText('"+geom+"') as g)";
					sep=' UNION ';
				}
			}
		}
		geojson=geojson+sepEnr+sGeom;
		sepEnr ='#';

	}
	fichier_calc = base_url_map_compute_geom+'&obj='+obj+'&idx='+idx+'&etendue='+etendue+'&reqmo='+reqmo+
		'&premier='+premier+'&tricol='+tricol+'&advs_id='+advs_id+
		'&valide='+valide+'&style='+style+'&onglet='+onglet+'&idx_sel='+idx_sel+'&min='+min+'&max='+max;
	$.ajax(
			{
				url: fichier_calc,
				type: 'POST',
				data: { geojson: geojson },
				success: function(sGeom) {
					geojson=sGeom.split('#');
					sGeomsForm="";
					sep = '';
					err='';
					i=0;
					for (c=min; c<=max; c++) {
						if (cg_maj[c] == 't') {
							if (geojson[i].substring(0, 4) == 'Err:') {
								if (err=='') {
									err=geojson[i].replace('Err:',cg_lib_geometrie[c]+':');
								}
								else {
									err=err+', '+geojson[i].replace('Err:',cg_lib_geometrie[c]+':');
								}
							}
							else {
								lst_idx_data_layers_edit[c].removeAllFeatures();
								sGeomForm='';
								if (geojson[i] != '') {
									var o = geojson_format.read( geojson[i]);
									sGeomForm=o.geometry;
									lst_idx_data_layers_edit[c].addFeatures(o);
									lstEditControls['select'].select(lst_idx_data_layers_edit[c].features[0]);
								}
								if (recordMode=='1') {
									if (geojson[i] != '') {
										var geom = lst_idx_data_layers_edit[c].selectedFeatures[0].geometry.clone()
										sGeomsForm = sGeomsForm+sep+geom;
									}
									else
										sGeomsForm = sGeomsForm+sep;
									sep='#';
								}
								else {
								}

							}
						}
						i=i+1;
					}
					if (err=='') {
						if (enreg==true) {
							map_affiche_info(': enregistrement');
							link = base_url_map_form_sig+'&obj='+obj+'&idx='+idx_sel+'&etendue='+etendue+'&reqmo='+reqmo+
								'&premier='+premier+'&tricol='+tricol+'&advs_id='+advs_id+
								'&valide='+valide+'&style='+style+'&onglet='+onglet+'&idx_sel='+idx_sel+'&min='+min+'&max='+max+'&validation=0';
							width = 'auto';
							height= 'auto';
							callback = '';
							callbackParams='';
							var dialog = $('<div id=\"sousform-'+obj+'\"></div>').insertAfter('#map-id');
							$('<input type=\"text\" name=\"recherchedyn\" id=\"recherchedyn\" value=\"\" class=\"champFormulaire\" style=\"display:none\" />').insertAfter('#sousform-'+obj);
							$.ajax({
								type: 'POST',
								data: { geojson: sGeomsForm },
								url: link,
								cache: false,
								success: function(html){
									dialog.empty();
									dialog.append(html);
									om_initialize_content();
									$(dialog).dialog({
									close: function(ev, ui) {
										if (typeof(callback) === "function") {
											callback(callbackParams);
										}
										$(this).remove();
									},
									resizable: true,
									modal: true,
									width: width,
									height: height,
									position: 'center center'
								  });
								},
								async : false
							});
							$('#sousform-'+obj).on(
								"click",
								'a.retour',
								function() {
									ret=$('#form_sig_retour').val();
									$(dialog).dialog('close').remove();
									if (ret=='t') {
										idx_sel_sav = idx_sel;
										map_load_geojson_datas(true);
										map_load_carts();
										idx_sel = idx_sel_sav;
										map_copyDataToEditLayers();
										map_getFilters();
										map_load_geojson_markers();
										map_load_overlays();
										map_clicEditClose();
										return true
									}
									else
										return false;
								}
							);
						} else {
							map_affiche_info(': vérification terminée avec succès');
						}
					}
					else {
						map_affiche_info(': Données invalides! '+err );
					}
				},
				async:false,
				timeout:1000
			}
		);
}

// traitement du clic sur le bouton de validation
function map_clicEditValid() {
	map_computeGeom(select_edit_champ, false);
}

// traitement du clic sur le bouton d'enregistrement
function map_clicEditRecord() {
	if (recordMultiComp==true)  {
		map_computeGeom('', true);
	}
	else {
		map_computeGeom(select_edit_champ, true);
	}
}

// traitement du clic sur le bouton de récupération du panier
function map_clicEditGetCart() {
	if (typeof cartLayer !== "undefined") {
		for(var i=0; i<cartLayer.features.length; i++) {
			lst_idx_data_layers_edit[select_edit_champ].addFeatures(cartLayer.features[i].clone());
		}
		cartLayer.removeAllFeatures();
		cart_val = new Array();
		lst_idx_data_layers_edit[select_edit_champ].redraw();
	}
}

// Selection d'un panier
function map_select_cart(n) {
	cart_val = new Array();
	if (typeof selectCartLayer !== "undefined") {
		selectCartLayer.setVisibility(false);
	}
	if (typeof cartLayer !== "undefined") {
		cartLayer.setVisibility(false);
		cartLayer.removeAllFeatures();
		map.removeLayer(cartLayer);
		cartLayer = undefined;
	}
	$('#map-edit-cart-get').hide();
	if (n !='') {
		$('#map-edit-cart-get').show();
		selectCartLayer=lst_carts[lst_idx_flux[n]];
		selectCartLayer.setVisibility(true);
		action_edit_champ='cart';
		map_deactivate_all_edit_control('');
		map_RefreshEditBar();
		map_affiche_info(": choix d'un panier");
	}
}

// Selection d'un champ d'édition
function map_select_edit_champ(n) {
	map_affiche_info(': choix du champ '+cg_lib_geometrie[n]);
	select_edit_champ = n;
	selectControl_layers = [];
	map_add_SelectControl();
	for(var key in lstEditControls) {
		lstEditControls[key].deactivate();
		map.removeControl(lstEditControls[key]);
    }
	lstEditControls = [];
	var cselect = new OpenLayers.Control.SelectFeature(
				lst_idx_data_layers_edit[select_edit_champ],{
				clickout: true,
				toggle: true,
				multiple: true,
				hover: false,
                box: false
			}
		);
	;
	var cmodify = new OpenLayers.Control.ModifyFeature(
		lst_idx_data_layers_edit[select_edit_champ]
	);
	cmodify.standalone=false;
	cmodify.mode |= OpenLayers.Control.ModifyFeature.DRAG;
	if (cg_geometrie[select_edit_champ] == 'point' || cg_geometrie[select_edit_champ] == 'multipoint') {
		lstEditControls = {
			point: new OpenLayers.Control.DrawFeature(lst_idx_data_layers_edit[select_edit_champ],OpenLayers.Handler.Point),
            modify: cmodify,
			select: cselect
        };
	}else if (cg_geometrie[select_edit_champ] == 'linestring' || cg_geometrie[select_edit_champ] == 'multilinestring') {
		lstEditControls = {
			line: new OpenLayers.Control.DrawFeature(lst_idx_data_layers_edit[select_edit_champ],OpenLayers.Handler.Path),
            modify: cmodify,
			select: cselect
        };
	}else if (cg_geometrie[select_edit_champ] == 'polygon' || cg_geometrie[select_edit_champ] == 'multipolygon') {
		var nb_side = 4;
		if (map_is_int($('#map-edit-draw-regular-nb').val()) != false) nb_side = map_is_int($('#map-edit-draw-regular-nb').val());
		lstEditControls = {
			polygon: new OpenLayers.Control.DrawFeature(lst_idx_data_layers_edit[select_edit_champ],OpenLayers.Handler.Polygon),
            regular: new OpenLayers.Control.DrawFeature(lst_idx_data_layers_edit[select_edit_champ],OpenLayers.Handler.RegularPolygon,{handlerOptions: {sides: nb_side}}),
            modify: cmodify,
			select: cselect
		};
	}
	$("#map-edit-cart-lst-id").empty();
	var select = '<option value="" selected="selected">Choisir le panier</option>';
	var type_champ;
	var type_panier;
	for(var i=0; i<fl_om_sig_map_flux.length; i++) {
		if (fl_m_panier[i] == 't') {
			type_champ = cg_geometrie[select_edit_champ].replace('multi','');
			type_panier = fl_m_pa_type_geometrie[i].replace('multi','');
			if (type_champ == type_panier) select = select +'<option value="'+i+'">'+fl_m_pa_nom[i]+'</option>';
		}
	}
	$("#map-edit-cart-lst-id").append(select);
	$('#map-edit-cart-lst-id').on(
				'change',
				function() {
					map_select_cart( this.value );
				}
			);

	for(var key in lstEditControls) {
		map.addControl(lstEditControls[key]);
    }

	map_clicEditNavigate();
}

// Traitement géolocalisation
function map_clicGeolocate() {
	map_set_mode_action('Géolocalisation');
	map_affiche_info(": en cours");
	var control = map.getControlsBy("id", "locate-control")[0];
	if (control.active) {
		control.getCurrentLocation();
	} else {
		control.activate();
	}
}

// traitement bouton Info
function map_clicInfo() {
	map_set_mode_action('Information');
	selectControl.activate();
}

// traitement bouton Formulaire
function map_clicForm() {
	map_set_mode_action('Formulaire');
	if (idx_sel != '-1') {
		map_popupIt(obj, base_url_map_redirection_onglet+'&idx='+idx_sel+"&obj="+obj, 'auto','auto','','');
		map_set_mode_action('Information');
	} else {
		map_affiche_info(': aucun enregistrement n\'est sélectionné')
	}
}

// Traitement clic naviguer
function map_clicNavigate() {
	map_set_mode_action('Navigation');
}

// Traitement clic mesure distance
function map_clicMeasureDistance () {
	map_set_mode_action('Mesure distance');
}

// Traitement clic mesure aire
function map_clicMeasureAera () {
	map_set_mode_action('Mesure surface');
}

// traitement bouton Edit
function map_clicEdit() {
	if (idx_sel != '-1') {
		map_getFilters();
		map_copyDataToEditLayers();$("#map-edit-sel-comp-id").empty();
		var select = '';
		var premier = true;
		for(var i=0; i<cg_maj.length; i++) {
			if (cg_maj[i] == 't') {
				if (premier == true) {
					select = select +'<option value="'+i+'" selected="selected">'+cg_lib_geometrie[i]+'</option>';
					premier = false;
				} else {
					select = select +'<option value="'+i+'">'+cg_lib_geometrie[i]+'</option>';
				}
			}
		}
		if (premier == true) {
			map_affiche_info(": il n'y aucun champ géographique éditable")
		} else {
			map_set_mode_action('Edition');
			$("#map-edit-sel-comp-id").append(select);
			$('#map-edit-sel-comp-id').on(
				'change',
				function() {
					map_select_edit_champ( this.value );
				}
			);
			$('[id^="map-tools"]').hide();
			$('[id^="map-edit"]').show();
			map_select_edit_champ($('#map-edit-sel-comp-id').val());

			for(var i=0; i<cg_maj.length; i++) {
				if (cg_maj[i] == 't') {
                    if (lst_idx_data_layers_edit[i].features.length > 0)
                        lstEditControls['select'].select(lst_idx_data_layers_edit[i].features[0]);
				}
			}
		}
	} else {
		map_affiche_info(': aucun enregistrement n\'est sélectionné')
	}
}

// traitement bouton Edit Close
function map_clicEditClose() {
	map_clicEditNavigate();
	$('[id^="map-edit"]').hide();
	$('[id^="map-tools"]').show();
	map_emptyEditLayers();
	mode_action='';
	map_set_mode_action('Information');
	if (idx_sel != '-1') map_copyDataToEditLayers();
}

// Actualisation des filtres de flux
function map_getFilters() {
	fichier_get_filters = base_url_map_get_filters+'&obj='+obj+'&idx='+idx+'&idx_sel='+idx_sel+'&etendue='+etendue+'&reqmo='+reqmo+
			'&premier='+premier+'&tricol='+tricol+'&advs_id='+advs_id+
			'&valide='+valide+'&style='+style+'&onglet='+onglet;
	OpenLayers.Request.GET(
			{
				url: fichier_get_filters,
				success: function (req) {
					lst=req.responseText.split("\n");
					for(var i=0; i<fl_om_sig_map_flux.length-1; i++) {
						if (fl_m_panier[i] != 't' && fl_m_sql_filter[i] != '' && fl_w_cache_type[i] == '' && fl_m_filter[i] != lst[i]) {
							fl_m_filter[i] = lst[i];
							if (fl_m_baselayer[i] == 't'  ) {
								lst_base_layers[lst_idx_flux[i]].params['FILTER'] = fl_m_filter[i].replace(new RegExp('²', 'g'), '"');
								lst_base_layers[lst_idx_flux[i]].redraw(true);
							} else {
								lst_overlays[lst_idx_flux[i]].params['FILTER'] = fl_m_filter[i].replace(new RegExp('²', 'g'), '"');
								lst_overlays[lst_idx_flux[i]].redraw(true);
							}
						}

					}
				}
			}
		);
}

// Traitement de l'application des SLD pour les données geojson
function map_successGetSld_geojson_datas(req) {
	var format = new OpenLayers.Format.SLD();
	var sld_menu = format.read(req.responseXML || req.responseText);
	for(var i=0; i<lst_idx_data_layers.length; i++) {
		lst_idx_data_layers[i].styleMap.styles["default"] = sld_menu.namedLayers["defaut"].userStyles[0];
		lst_idx_data_layers[i].redraw();
	}
}

// Chargement des données aux formats geojson
// *** jlb changement couleur opacite pour presentation
function map_load_geojson_datas(bRefreshFilters) {
	map_empty_layer_list('datas');
	centerLayer.removeAllFeatures();
	idx_max_load_geojson_datas=cg_maj.length-1;
	idx_cou_load_geojson_datas=0;
	for(var i=0; i<lst_idx_data_layers.length; i++) {
		map.removeLayer(lst_idx_data_layers[i]);
		map.removeLayer(lst_idx_data_layers_edit[i]);
	}
	lst_idx_data_layers = [];
	for(var i=(cg_maj.length-1); i>=0; i--) {
		fichier_jsons = base_url_map_get_geojson_datas+'&obj='+obj+'&idx='+idx+'&seli='+i+'&etendue='+etendue+'&reqmo='+reqmo+
			'&premier='+premier+'&tricol='+tricol+'&advs_id='+advs_id+
			'&valide='+valide+'&style='+style+'&onglet='+onglet;
		geojson_layer = new OpenLayers.Layer.Vector(
			"GeoJSON_datas_"+i+"-"+cg_lib_geometrie[i],
			{
                    projection: displayProjection,
                    strategies: [new OpenLayers.Strategy.Fixed()],
                    protocol: new OpenLayers.Protocol.HTTP(
						{
							url: fichier_jsons,
							format: new OpenLayers.Format.GeoJSON()
						}
					)
               }
		);
		geojson_layer.events.on({
			"featureselected": map_on_select_feature_data,
			"featureunselected": map_on_unselect_feature_data
		});
		map.addLayer(geojson_layer);
		selectControl_layers.push(geojson_layer);
		geojson_layer.events.on(
			{
				"loadend":
					function(e){
						for(var i=0; i<e.object.features.length; i++) {
							centerLayer.addFeatures( e.object.features[i].clone());
						}
						if (idx_max_load_geojson_datas==idx_cou_load_geojson_datas) {
							if (centerLayer.features.length > 0) {
								map.zoomToExtent(centerLayer.getDataExtent());
								centerLayer.removeAllFeatures();
							} else {
                                if (Number(sm_point_centrage_x) != 0 && Number(sm_point_centrage_y) != 0) {
                                    var lonlat = new OpenLayers.LonLat(Number(sm_point_centrage_x), Number(sm_point_centrage_y));
                                    map.setCenter(lonlat, sm_zoom);
                                }
							}
						}
						idx_cou_load_geojson_datas=idx_cou_load_geojson_datas+1;

					}
			}
		)
		geojson_layer.redraw();
		lst_idx_data_layers[i] =  geojson_layer;
	}
	if (sm_sld_data!='') {
		OpenLayers.Request.GET(
			{
				url: url_sld_data,
				success: map_successGetSld_geojson_datas
			}
		);
	} else  {
		for(var i=0; i<lst_idx_data_layers.length; i++) {
			lst_idx_data_layers[i].styleMap =
				new OpenLayers.StyleMap(
					{	"default": 	{strokeColor: "black",strokeWidth:4,strokeOpacity: 0.9,fillColor : "blue", fillOpacity: 0.4, pointRadius : 5},
						"select": {strokeColor: "red",strokeWidth:3,strokeOpacity: 0.9,fillColor : "red", pointRadius : 5}
					}
				);
			lst_idx_data_layers[i].redraw();
		}
	}
	for(var i=0; i<lst_idx_data_layers.length; i++) {
		map_add_layer_to_list(lst_idx_data_layers[i], 'datas', i);
	}
	for(var i=(cg_maj.length-1); i>=0; i--) {
		lst_idx_data_layers_edit[i] = new OpenLayers.Layer.Vector("GeoJSON_datas_edit_"+i+"-"+cg_lib_geometrie[i], {projection: displayProjection});
		lst_idx_data_layers_edit[i].styleMap =
				new OpenLayers.StyleMap(
					{	"default": 	{strokeColor: "black",strokeWidth:3,strokeOpacity: 0.9,fillColor : "blue", fillOpacity: 0.4, pointRadius : 5},
						"select": {strokeColor: "red",strokeWidth:3,strokeOpacity: 0.9,fillColor : "red", pointRadius : 5}
					}
				);
		lst_idx_data_layers_edit[i].events.on({
			"featureselected": map_on_select_feature_data_edit,
			"featureunselected": map_on_unselect_feature_data_edit
		});
		map.addLayer(lst_idx_data_layers_edit[i]);
	}
}

// Traitement de l'application des SLD pour les marqueurs geojson
function map_successGetSld_geojson_markers(req) {
	var format = new OpenLayers.Format.SLD();
	var sld_menu = format.read(req.responseXML || req.responseText);
	markersLayer.styleMap.styles["default"] = sld_menu.namedLayers["defaut"].userStyles[0];
	markersLayer.redraw();
}

// Chargement des marqueurs aux formats geojson
function map_load_geojson_markers() {
	map.removeLayer(markersLayer);
	fichier_jsons = base_url_map_get_geojson_markers+'&obj='+obj+'&idx='+idx+'&etendue='+etendue+'&reqmo='+reqmo+
		'&premier='+premier+'&tricol='+tricol+'&advs_id='+advs_id+
		'&valide='+valide+'&style='+style+'&onglet='+onglet;
	markersLayer = new OpenLayers.Layer.Vector(
		"Marqueurs",
		{
			projection: displayProjection,
			strategies: [new OpenLayers.Strategy.Fixed()],
			protocol: new OpenLayers.Protocol.HTTP(
				{
					url: fichier_jsons,
					format: new OpenLayers.Format.GeoJSON()
				}
			)
		}
	);
	map.addLayer(markersLayer);
	if (sm_sld_marqueur!='') {
		OpenLayers.Request.GET(
			{
				url: url_sld_marqueur,
				success: map_successGetSld_geojson_markers
			}
		);
	} else  {
		markersLayer.styleMap = new OpenLayers.StyleMap(
			{
				"default": {
					externalGraphic: img_maj,
					graphicWidth:img_w,
					graphicHeight: img_h,
					graphicYOffset: -img_h
				},
				"select": {
					externalGraphic: img_maj_hover,
					graphicWidth:  img_w,
					graphicHeight: img_h,
					graphicYOffset: -img_h
				}
			}
		);
		markersLayer.redraw();
	}
	map_empty_layer_list('markers');
	map_add_layer_to_list(markersLayer, 'markers', 100);
	markersLayer.events.on({
		"featureselected": map_on_select_feature_marker,
		"featureunselected": map_on_unselect_feature_marker
	});
	selectControl_layers.push(markersLayer);
}

// fonction d'identification des tuiles pour les flux de type SMT
function map_flux_SMT(bounds) {
	var res = this.map.getResolution();
	var x = Math.round ((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
	var y = Math.round ((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
	var z = this.map.getZoom();
	return this.url+"/"+this.layer+"/"+z+"/"+x+"/"+y+"."+this.type;
}

// Initialisation des flux (om_sig_map_flux)
function map_load_flux(i) {
	var flux;
	var flux_name;
	if (fl_m_panier[i] != 't')
		flux_name="flux_"+fl_om_sig_map_flux[i]+"_"+i+"-"+fl_m_ol_map[i];
	else
		flux_name="flux_"+fl_om_sig_map_flux[i]+"_"+i+"-"+fl_m_pa_nom[i];
	paramsWms = {};
	paramsWms.layers=fl_w_couches[i];
	paramsWms.transparent=true;
	paramsWms.srs="EPSG:"+defDisplayProjection;
	if (fl_m_filter[i] != "") paramsWms.filter=fl_m_filter[i].replace(new RegExp('²', 'g'), '"');
	optionsWms = {};
	if (fl_m_baselayer[i]=="t") {
		optionsWms.isBaseLayer=true;
		optionsWms.maxZoomLevel=Number(fl_m_maxzoomlevel[i]);
	}else{
		optionsWms.isBaseLayer=false;
	}
	if (fl_m_visibility[i] == 't' && fl_m_panier[i] != 't') {
		optionsWms.visibility=true;
	}else{
		optionsWms.visibility=false;
	}
	if (fl_m_singletile[i] == 't' && fl_w_cache_type[i] != "TCF") {
		optionsWms.singleTile= true;
		optionsWms.ratio=1;
	}
	if (fl_w_cache_type[i] == "SMT") {
		paramsWms = {};
		paramsWms.layer=fl_w_couches[i];
		paramsWms.attribution=fl_w_attribution[i];
		paramsWms.type='png';
		paramsWms.getURL= map_flux_SMT;
		if (fl_m_baselayer[i]=="t") {
			paramsWms.isBaseLayer=true;
		}else{
			paramsWms.isBaseLayer=false;
			paramsWms.opacity=0.3;
		}
		if (fl_m_visibility[i] == 't') {
			paramsWms.visibility=true;
		}else{
			paramsWms.visibility=false;
		}
		paramsWms.numZoomLevels=Number(fl_m_maxzoomlevel[i]);
	}
	if (fl_w_cache_type[i] == "TCF") {
		optionsWms.attribution= fl_w_attribution[i];
		flux = new OpenLayers.Layer.WMS( flux_name, fl_w_chemin[i], paramsWms, optionsWms);
	} else if (fl_w_cache_type[i] == "SMT") {
		flux = new OpenLayers.Layer.TMS( flux_name, fl_w_chemin[i], paramsWms);
	} else {
		optionsWms.attribution= fl_w_attribution[i];
		flux = new OpenLayers.Layer.WMS( flux_name, fl_w_chemin[i], paramsWms, optionsWms);
	}
	return flux;
}

// Chargement des flux de type base
function map_load_bases_layers() {
	for(var i=0; i<lst_base_layers.length; i++) {
		map.removeLayer(lst_base_layers[i]);
	}
	lst_base_layers = [];
	for(var i=(fl_w_id.length-1); i>=0; i--) {
		if (fl_m_baselayer[i] == 't') {
			lst_base_layers.push(map_load_flux(i));
			map.addLayer(lst_base_layers[lst_base_layers.length-1]);
			lst_idx_flux[i] = lst_base_layers.length-1;
		}
	}
	for(var i=(lst_base_layers.length-1); i>=0; i--) {
		map_add_layer_to_list(lst_base_layers[i],'baselayers',i);
	}
	if (sm_fond_osm=='t') {
		osm = new OpenLayers.Layer.OSM( "Simple OSM Map");
		map.addLayer(osm);
		map_add_layer_to_list(osm,'baselayers','osm');
	}
	if (sm_fond_bing=='t') {
		bingRoad = new OpenLayers.Layer.Bing({ key: pebl_cle_bing, type: "Road", metadataParams: { mapVersion: "v1"}, name: "Bing Road", transitionEffect: 'resize'});
		map.addLayer(bingRoad);
		map_add_layer_to_list(bingRoad, 'baselayers', 'bingRoad');
	    bingAerial = new OpenLayers.Layer.Bing({key: pebl_cle_bing, type: "Aerial", name: "Bing Aerial", transitionEffect: 'resize'});
		map.addLayer(bingAerial);
		map_add_layer_to_list(bingAerial, 'baselayers', 'bing');
	    bingHybrid = new OpenLayers.Layer.Bing({ key: pebl_cle_bing, type: "AerialWithLabels", name: "Bing Aerial + Labels", transitionEffect: 'resize'});
		map.addLayer(bingHybrid);
		map_add_layer_to_list(bingHybrid, 'baselayers', 'bingHybrid');
	}
	if (sm_fond_sat=='t') {
		sat = new OpenLayers.Layer.Google(
				"Google Hybrid",
				{type: google.maps.MapTypeId.HYBRID, sphericalbaseProjection: true, numZoomLevels: 30, useTiltImages: true}
			);

		map.addLayer(sat);
		map_add_layer_to_list(sat, 'baselayers','sat');
		gSat = new OpenLayers.Layer.Google(
					"Google Satellite",
					{type: google.maps.MapTypeId.SATELLITE, sphericalbaseProjection: true, numZoomLevels: 30}
				);
		map.addLayer(gSat);
		map_add_layer_to_list(gSat, 'baselayers','gSat');
		gStreets = new OpenLayers.Layer.Google(
				"Google RoadMap",
				{type: google.maps.MapTypeId.ROADMAP, sphericalbaseProjection: true, numZoomLevels: 30}
			);
		map.addLayer(gStreets);
		map_add_layer_to_list(gStreets, 'baselayers','gStreets');
	}
}

// vide une des listes du selecteur (div map-layers)  (dest: baselayers, overlays, datas, markers)
function map_empty_layer_list(dest) {
	if (dest == 'baselayers') divSel='#map-layers-bases';
	else if (dest == 'overlays') divSel='#map-layers-overlays';
	else if (dest == 'datas') divSel='#map-layers-datas';
	else if (dest == 'markers') divSel='#map-layers-markers';
	else divSel ='';
	if (divSel != '') $(divSel).empty();
}

// Ajoute la couche dans le selecteur(div map-layers) (dest: baselayers, overlays, datas, markers)
// *** jlb : <p id = ...<input....  remplace <BR><input.... / Bouton radio ou check box dans css
function map_add_layer_to_list(layer, dest, i) {
	layer_name = layer.name;
	if (layer_name.substring(0, 5)=='flux_' || layer_name.substring(0, 14)=='GeoJSON_datas_') layer_name = layer_name.substring(layer_name.indexOf('-')+1,layer_name.length);
	if (layer.visibility) checked = ' checked '; else checked = '';
	if (dest == 'baselayers') {
		taglayers='#map-layers-bases';
		$('#map-layers-bases').append('<p id="layers"><input class="radio" type="radio" name = "map_rb_baselayer" id="map_baselayer-'+i+'"'+checked+'><label for="map_baselayer-'+i+'"'+checked+'>'+layer_name+'</label></p>');
		$('#map_baselayer-'+i).click(function() {
                if (layer.isBaseLayer) {
                    layer.map.setBaseLayer(layer);
                } else {
                    layer.setVisibility(!layer.getVisibility());
                }
            });
	}
	else if (dest == 'overlays') {
		$('#map-layers-overlays').append('<p id="h-oui-non"><input type="checkbox" id="map_overlays-'+i+'"'+checked+'><label for="map_overlays-'+i+'"'+checked+'><span class="x99"></span>'+layer_name+'</label></p>');
		$('#map_overlays-'+i).click(function() {
                if (layer.isBaseLayer) {
                    layer.map.setBaseLayer(layer);
                } else {
                    layer.setVisibility(!layer.getVisibility());
                }
            });
	}
	else if (dest == 'datas') {
		$('#map-layers-datas').append('<p id="h-oui-non"><input type="checkbox" id="map_datas-'+i+'"'+checked+'><label for="map_datas-'+i+'"'+checked+'><span class="x99"></span>'+layer_name+'</label></p>');
		$('#map_datas-'+i).click(function() {
                if (layer.isBaseLayer) {
                    layer.map.setBaseLayer(layer);
                } else {
                    layer.setVisibility(!layer.getVisibility());
                }
            });
	}
	else if (dest == 'markers') {
		$('#map-layers-markers').append('<p id="h-oui-non"><input type="checkbox" id="map_markers-'+i+'"'+checked+'><label for="map_markers-'+i+'"'+checked+'><span class="x99"></span>'+layer_name+'</label></p>');
		$('#map_markers-'+i).click(function() {
                if (layer.isBaseLayer) {
                    layer.map.setBaseLayer(layer);
                } else {
                    layer.setVisibility(!layer.getVisibility());

                }
            });
	}
}

// Chargement des flux de type overlays
function map_load_overlays() {
	map_empty_layer_list('overlays');
	for(var i=0; i<lst_overlays.length; i++) {
		map.removeLayer(lst_overlays[i]);
	}
	lst_overlays = [];
	for(var i=0; i<fl_w_id.length; i++) {
		if (fl_m_baselayer[i] != 't' && fl_m_panier[i] != 't') {
			if (s_visibility !== null && typeof(s_visibility)=='object') {
				if (s_visibility[lst_overlays.length]=='Oui')
					fl_m_visibility[i] ='t'
				else
					fl_m_visibility[i]='f';

			}
			var layer = map_load_flux(i);
			lst_overlays.push(layer);

			map.addLayer(lst_overlays[lst_overlays.length-1]);
			lst_idx_flux[i] = lst_overlays.length-1;
			map.setLayerIndex(lst_overlays[lst_overlays.length-1],0);
		}
	}
	for(var i=0; i<lst_overlays.length; i++) {
		map_add_layer_to_list(lst_overlays[i], 'overlays', i);
		if (lst_overlays[i].visibility)
			lst_overlays_visibility.push('Oui');
		else
			lst_overlays_visibility.push('Non');
	}
}

// Chargement des flux de type cart
function map_load_carts() {
	for(var i=0; i<lst_carts.length; i++) {
		map.removeLayer(lst_carts[i]);
	}
	lst_carts = [];
	for(var i=0; i<fl_w_id.length; i++) {
		if (fl_m_panier[i] == 't') {
			var layer = map_load_flux(i);
			lst_carts.push(layer);

			map.addLayer(lst_carts[lst_carts.length-1]);
			lst_idx_flux[i] = lst_carts.length-1;
			map.setLayerIndex(lst_carts[lst_carts.length-1],0);
		}
	}
}

// Affichage de la couche de base sélectionnée
function map_display_base_layer() {
	if (s_base == 'osm' || s_base == encodeURIComponent("Simple OSM Map")) {
		map.setBaseLayer(osm);
		$('#map_baselayer-osm').attr('checked', true);
	} else if ( s_base == 'bing' || s_base == encodeURIComponent("Bing Aerial")) {
		map.setBaseLayer(bingAerial);
		$('#map_baselayer-bing').attr('checked', true);
	} else if ( s_base == encodeURIComponent("Bing Road")) {
		map.setBaseLayer(bingAerial);
		$('#map_baselayer-bingRoad').attr('checked', true);
	} else if ( s_base == encodeURIComponent("Bing Aerial + Labels")) {
		map.setBaseLayer(bingHybrid);
		$('#map_baselayer-bingHybrid').attr('checked', true);
	} else if (s_base == 'sat' || s_base == encodeURIComponent("Google Hybrid")) {
		map.setBaseLayer(sat);
		$('#map_baselayer-sat').attr('checked', true);
	} else if (s_base == encodeURIComponent("Google Satellite")) {
		map.setBaseLayer(gSat);
		$('#map_baselayer-gSat').attr('checked', true);
	} else if (s_base == encodeURIComponent("Google RoadMap")) {
		map.setBaseLayer(gStreets);
		$('#map_baselayer-gStreets').attr('checked', true);
	} else {
		for(var i=(lst_base_layers.length-1); i>=0; i--) {
			if (s_base == encodeURIComponent(lst_base_layers[i].name)) {
				map.setBaseLayer(lst_base_layers[i]);
				$('#map_baselayer-'+i).attr('checked', true);
			}
		}
	}
	baseLayerSelected=map.baseLayer.name;

}

// Ajoute du control openlayers de géolocalisation
function map_addGeolocateControl() {
	var vector = new OpenLayers.Layer.Vector("Geolocate", {});
	map.addLayer(vector);
	geolocate = new OpenLayers.Control.Geolocate({
        id: 'locate-control',
        geolocationOptions: {
            enableHighAccuracy: false,
            maximumAge: 0,
            timeout: 7000
        }
    });
	map.addControl(geolocate);
	var style = {
        fillOpacity: 0.1,
        fillColor: '#000',
        strokeColor: '#f00',
        strokeOpacity: 0.6
    };
    geolocate.events.register(
		"locationupdated",
		this,
		function(e) {
			vector = map.getLayersByName("Geolocate")[0];
			vector.removeAllFeatures();
			vector.addFeatures([
				new OpenLayers.Feature.Vector(e.point, {},{graphicName: 'cross',strokeColor: '#f00',strokeWidth: 2,fillOpacity: 0,pointRadius: 10 }            ),
				new OpenLayers.Feature.Vector(
					OpenLayers.Geometry.Polygon.createRegularPolygon(
						new OpenLayers.Geometry.Point(e.point.x, e.point.y),
						e.position.coords.accuracy / 2,
						50,
						0
					),
					{},
					style
				)
			]);
			map.zoomToExtent(vector.getDataExtent());
			map_affiche_info(": terminée");
		}
	);

}

// Ajoute le controle openLayers de sélection
function map_add_SelectControl() {
	if (typeof selectControl !== "undefined") {
		selectControl.deactivate();
		map.removeControl(selectControl);
	}
	if (selectControl_layers.length > 0) {
		selectControl=new OpenLayers.Control.SelectFeature(
				selectControl_layers,{
					clickout: true,
					toggle: false,
					multiple: true,
					hover: false,
					box: false
				}
			);
		controls = {
			select: selectControl
		};
		for(var key in controls) {
			map.addControl(controls[key]);
			controls[key].activate();
		}
	} else {
		selectControl = undefined;
	}
}

// Traitement des controles de mesure
function map_handleMeasurements(event) {
            var geometry = event.geometry;
            var units = event.units;
            var order = event.order;
            var measure = event.measure;
            var out = "";
            if(order == 1) {
                out += "measure: " + measure.toFixed(3) + " " + units;
            } else {
                out += "measure: " + measure.toFixed(3) + " " + units + "2";
            }
            map_affiche_info(": "+out);
        }

// Ajoute des controles de mesure
function map_add_MeasureControls() {
	var sketchSymbolizers = {
		"Point": {
			pointRadius: 4,
			graphicName: "square",
			fillColor: "white",
			fillOpacity: 1,
			strokeWidth: 1,
			strokeOpacity: 1,
			strokeColor: "#333333"
		},
		"Line": {
			strokeWidth: 3,
			strokeOpacity: 1,
			strokeColor: "#666666",
			strokeDashstyle: "dash"
		},
		"Polygon": {
			strokeWidth: 2,
			strokeOpacity: 1,
			strokeColor: "#666666",
			fillColor: "white",
			fillOpacity: 0.3
		}
	};
	var style = new OpenLayers.Style();
	style.addRules([ new OpenLayers.Rule({symbolizer: sketchSymbolizers}) ]);
	var styleMap = new OpenLayers.StyleMap({"default": style});
    measureControls = {
		line: new OpenLayers.Control.Measure(
			OpenLayers.Handler.Path, {
				persist: true,
				handlerOptions: {
					layerOptions: {
						styleMap: styleMap
					}
				}
			}
		),
		polygon: new OpenLayers.Control.Measure(
			OpenLayers.Handler.Polygon, {
				persist: true,
				handlerOptions: {
					layerOptions: {
						styleMap: styleMap
					}
				}
			}
		)
	};
	var control;
	for(var key in measureControls) {
		control = measureControls[key];
		control.events.on({
			"measure": map_handleMeasurements,
			"measurepartial": map_handleMeasurements
		});
		control.geodesic = true;
		control.setImmediate(true);
		control.deactivate();
		map.addControl(control);
	}
}

// Ajoute les contrôles openLayers à la carte
function map_add_controls() {
	var controls = new Array();
	controls= [
				new OpenLayers.Control.ScaleLine({'bottomOutUnits':''})
				,new OpenLayers.Control.PanZoomBar()
				,new OpenLayers.Control.Navigation()
				,new OpenLayers.Control.OverviewMap({maximized: false})
				,new OpenLayers.Control.KeyboardDefaults()
				,new OpenLayers.Control.ZoomIn()
				//,new OpenLayers.Control.LayerSwitcher({'ascending':false})
			];
	for(var key in controls) {
		map.addControl(controls[key]);
	}
	mouseControl=new OpenLayers.Control.MousePosition();
	map.addControl(mouseControl);
	map_addGeolocateControl();
	map_add_SelectControl();
	map_add_MeasureControls();
}

// Initialisation de la carte
function map_init() {
        //jlb supp controls (supp superposition boutons zoom )
	//map_close_getfeatures();
	displayProjection = new OpenLayers.Projection("EPSG:"+defDisplayProjection);
	var projection_externe = new OpenLayers.Projection(sm_projection_externe);
	baseProjection = new OpenLayers.Projection("EPSG:"+defBaseProjection);
	var vetendue = new OpenLayers.Bounds.fromString(etendue).transform(displayProjection, baseProjection);
	map = new OpenLayers.Map( 'map-id',
		{
			projection: baseProjection,
            displayProjection: displayProjection,
			units: "m",
			maxZoomLevel:"auto", controls: []
		}
	);
	centerLayer = new OpenLayers.Layer.Vector("center", {projection: displayProjection});
	map.addLayer(centerLayer);
	markersLayer = new OpenLayers.Layer.Vector("Marqueurs", {projection: displayProjection});
	map.addLayer(markersLayer);
		if (sm_restrict_extent=='t') {
		map.setOptions({restrictedExtent: vetendue});
	}
	map_load_bases_layers();
	map_display_base_layer();
	map.setCenter(vetendue.getCenterLonLat(), zoomSelected);
	map_load_geojson_datas(false);
	map_load_geojson_markers();
	map_load_overlays();
	map_load_carts();
	map_add_controls();
	map_set_mode_action('Information');

	map.events.register('click', map, map_clic);
	map.events.register('changelayer', map, mapEventLayerChanged);
	map.events.register('moveend', map, mapChangeZoom);
}
//
function mapEventLayerChanged(event) {
	if(event.property === "visibility") {
		 mapChangeSession();
	}
}
function mapChangeZoom(evt) {
	if (zoomSelected!=map.zoom) {
		zoomSelected=map.zoom;
		mapChangeSession();
	}
}
function mapChangeSession() {
	for(var i=0; i<lst_overlays.length; i++) {
		if (lst_overlays[i].visibility)
			lst_overlays_visibility[i]='Oui';
		else
			lst_overlays_visibility[i]='Non';
	}
	if (map.baseLayer.getVisibility() == true)
		baseLayerSelected=map.baseLayer.name;
	fichier_calc = base_url_map_session;
	$.ajax(
		{
			url: fichier_calc,
			type: 'POST',
			data: {
				obj: obj,
				zoom: zoomSelected,
				base: baseLayerSelected,
				visibility: lst_overlays_visibility
				// seli: seli
			},
			success: function(sResult) {
				if (sResult!='ok')
					alert(sResult);
			},
			timeout:1000
		}
	);
}

// *** jlb Nouvelles fonctions d affichage visible ou pas suivant onglet selectionne

function affiche_aide()
{
	document.getElementById("map-layers").style.visibility='visible';
	document.getElementById("map-layers-bases").style.visibility='hidden';
	document.getElementById("map-layers-datas").style.visibility='hidden';
	document.getElementById("map-layers-markers").style.visibility='hidden';
	document.getElementById("map-layers-overlays").style.visibility='hidden';
	document.getElementById("map-tools").style.visibility='hidden';
	document.getElementById("map-edit").style.visibility='hidden';
	document.getElementById("map-legende").style.visibility='visible';
	document.getElementById("map-getfeatures").style.visibility='hidden';
	document.getElementById("map-getfeatures-datas").style.visibility='hidden';
	document.getElementById("map-getfeatures-markers").style.visibility='hidden';
	document.getElementById("map-getfeatures-flux").style.visibility='hidden';
	document.getElementById("map-infos").style.visibility='hidden';
}

function affiche_layers()
{
	document.getElementById("map-layers").style.visibility='visible';
	document.getElementById("map-layers-bases").style.visibility='hidden';
	document.getElementById("map-layers-datas").style.visibility='visible';
	document.getElementById("map-layers-markers").style.visibility='visible';
	document.getElementById("map-layers-overlays").style.visibility='visible';
	document.getElementById("map-tools").style.visibility='hidden';
	document.getElementById("map-edit").style.visibility='hidden';
	document.getElementById("map-legende").style.visibility='hidden';
	document.getElementById("map-getfeatures").style.visibility='hidden';
	document.getElementById("map-getfeatures-datas").style.visibility='hidden';
	document.getElementById("map-getfeatures-markers").style.visibility='hidden';
	document.getElementById("map-getfeatures-flux").style.visibility='hidden';
	document.getElementById("map-infos").style.visibility='hidden';
}

function affiche_tools()
{
    document.getElementById("map-layers").style.visibility='visible';
    document.getElementById("map-tools").style.visibility='visible';
    document.getElementById("map-edit").style.visibility='visible';
    document.getElementById("map-legende").style.visibility='hidden';
    document.getElementById("map-infos").style.visibility='visible';
    document.getElementById("map-layers-bases").style.visibility='hidden';
    document.getElementById("map-layers-datas").style.visibility='hidden';
    document.getElementById("map-layers-markers").style.visibility='hidden';
    document.getElementById("map-layers-overlays").style.visibility='hidden';
    document.getElementById("map-getfeatures").style.visibility='hidden';
    document.getElementById("map-getfeatures-datas").style.visibility='hidden';
    document.getElementById("map-getfeatures-markers").style.visibility='hidden';
    document.getElementById("map-getfeatures-flux").style.visibility='hidden';
}


function affiche_baselayers()
{
    document.getElementById("map-layers").style.visibility='visible';
    document.getElementById("map-layers-bases").style.visibility='visible';
    document.getElementById("map-infos").style.visibility='hidden';
    document.getElementById("map-tools").style.visibility='hidden';
    document.getElementById("map-edit").style.visibility='hidden';
    document.getElementById("map-legende").style.visibility='hidden';
    document.getElementById("map-layers-datas").style.visibility='hidden';
    document.getElementById("map-layers-markers").style.visibility='hidden';
    document.getElementById("map-layers-overlays").style.visibility='hidden';
    document.getElementById("map-getfeatures").style.visibility='hidden';
    document.getElementById("map-getfeatures-datas").style.visibility='hidden';
    document.getElementById("map-getfeatures-markers").style.visibility='hidden';
    document.getElementById("map-getfeatures-flux").style.visibility='hidden';

}

function affiche_getfeatures()
{
    map_set_mode_action('Information');
    document.getElementById("map-infos").style.visibility='hidden';
    document.getElementById("map-getfeatures").style.visibility='visible';
    document.getElementById("map-getfeatures-datas").style.visibility='visible';
    document.getElementById("map-getfeatures-markers").style.visibility='visible';
    document.getElementById("map-getfeatures-flux").style.visibility='visible';
    document.getElementById("map-layers").style.visibility='hidden';
    document.getElementById("map-layers-bases").style.visibility='hidden';
    document.getElementById("map-layers-datas").style.visibility='hidden';
    document.getElementById("map-layers-markers").style.visibility='hidden';
}

