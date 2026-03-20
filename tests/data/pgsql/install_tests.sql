--------------------------------------------------------------------------------
-- Script d'installation des jeux de données pour les tests
--
-- ATTENTION ce script est prévu pour être appliqué après le install.sql
--
-- @package framework_openmairie
-- @version SVN : $Id$
--------------------------------------------------------------------------------
--
START TRANSACTION;
--
\set schema 'framework_openmairie'
--
SET search_path = :schema, public, pg_catalog;
--
INSERT INTO om_collectivite (om_collectivite, libelle, niveau) VALUES 
(nextval('om_collectivite_seq'), 'MULTI', '2');


--
--
-- 
--
INSERT INTO om_parametre VALUES (nextval('om_parametre_seq'), 'option_localisation', 'sig_interne', 1);
INSERT INTO om_sig_extent VALUES (nextval('om_sig_extent_seq'), 'LIBREVILLE', '5.3588,43.6203,5.7107,43.7661', true);
INSERT INTO om_sig_flux VALUES (nextval('om_sig_flux_seq'), 'Cadastre', 1, 'cadastre', NULL, 'http://inspire.cadastre.gouv.fr/scpc/84089.wms?service=WMS&request=GetMap&VERSION=1.3&CRS=EPSG:3857&', 'AMORCES_CAD,LIEUDIT,CP.CadastralParcel,SUBFISCAL,CLOTURE,DETAIL_TOPO,HYDRO,VOIE_COMMUNICATION,BU.Building,BORNE_REPERE', NULL, NULL, NULL);
INSERT INTO om_sig_flux VALUES (nextval('om_sig_flux_seq'), 'Cadastre - section', 1, 'cadastre_section', NULL, 'http://inspire.cadastre.gouv.fr/scpc/84089.wms?service=WMS&request=GetMap&VERSION=1.3&CRS=EPSG:3857&', 'AMORCES_CAD', NULL, NULL, NULL);
INSERT INTO om_sig_map VALUES (nextval('om_sig_map_seq'), 1, 'carte_flux_generique', 'Carte pour flux génériques', false, '0', true, false, false, false, 'EPSG:2154', '...', '...', '...', false, false, false, NULL, 'osm', (SELECT om_sig_extent FROM om_sig_extent WHERE nom='LIBREVILLE'), true, NULL, NULL, NULL);
INSERT INTO om_sig_map VALUES (nextval('om_sig_map_seq'), 1, 'ma_carte', 'Ma carte', true, '12', true, false, false, false, 'EPSG:2154', '...', 'SELECT ST_asText(''01010000206A080000C6DE4AFF7E552B412CF66CF750D35741'') as geom, ''2'' as titre, ''3'' as description, 4 as idx, ''5'' as plop', '...', true, false, true, (SELECT om_sig_map FROM om_sig_map WHERE id='carte_flux_generique'), 'osm', (SELECT om_sig_extent FROM om_sig_extent WHERE nom='LIBREVILLE'), true, NULL, NULL, NULL);
INSERT INTO om_sig_map_flux VALUES (nextval('om_sig_map_flux_seq'), (SELECT om_sig_flux FROM om_sig_flux WHERE id='cadastre'), (SELECT om_sig_map FROM om_sig_map WHERE id='carte_flux_generique'), 'Cadastre', 10, true, false, NULL, NULL, NULL, NULL, '', NULL, '', true, false, 20);
INSERT INTO om_sig_map_flux VALUES (nextval('om_sig_map_flux_seq'), (SELECT om_sig_flux FROM om_sig_flux WHERE id='cadastre_section'), (SELECT om_sig_map FROM om_sig_map WHERE id='carte_flux_generique'), 'Cadastre - Section', 15, true, false, NULL, NULL, NULL, NULL, '', NULL, '', false, false, NULL);


--
--
--
INSERT INTO om_widget VALUES (nextval('om_widget_seq'), 'Ma carte', '../app/index.php?module=map&mode=tab_sig&obj=ma_carte', '<iframe style=''width:100%;border: 0 none; height: 500px;'' src=''../app/index.php?module=map&mode=tab_sig&obj=ma_carte&popup=1''></iframe>', 'web', '', '');
INSERT INTO om_widget VALUES (nextval('om_widget_seq'), 'Lorem ipsum', '', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. In eget urna a ante hendrerit vestibulum a at felis. Sed sit amet neque a mi fermentum scelerisque eu et ipsum. Nunc sit amet erat magna. Fusce mollis pellentesque magna, quis imperdiet mi interdum sed. Suspendisse ac elementum magna, sodales efficitur eros. Nunc ullamcorper sodales varius. Sed maximus, odio sed porta dictum, enim tortor porta massa, a euismod dui odio vel turpis. Morbi interdum sem in scelerisque vulputate. Donec sit amet tortor ante. Fusce ullamcorper libero dui, non luctus lectus sagittis eget. Proin fringilla placerat diam, at vehicula nunc placerat a.
Fusce quis ex pretium, tempus elit vitae, tristique ligula. Nam tincidunt ipsum ut scelerisque sodales. In et nisl at nisl congue scelerisque non vel nunc. Maecenas faucibus, sem sit amet scelerisque sodales, diam nisi sollicitudin elit, gravida iaculis lorem mi sit amet ipsum. Phasellus ac cursus dolor, eu placerat diam. Integer laoreet vestibulum urna, sit amet tristique erat aliquam quis. Fusce purus massa, rutrum ut consectetur sed, vehicula elementum augue. Mauris massa odio, tempor a scelerisque in, scelerisque quis justo. Nunc non sem eu dolor aliquam rhoncus vitae eget leo. Nullam semper dolor sed lectus semper, et interdum enim condimentum. Nullam eget odio metus.', 'web', '', '');
INSERT INTO om_widget VALUES (nextval('om_widget_seq'), 'Statistiques', '', '	<script src=''https://www.chartjs.org/dist/2.7.2/Chart.bundle.js''></script>
	<style>
	canvas {
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}
	</style>
	<div style=''width: 95%;''>
		<canvas id=''canvas''></canvas>
	</div>
        <div id=''canvas-holder-doughnut'' style=''width:95%''>
		<canvas id=''chart-area-doughnut''></canvas>
	</div>
	<script>
window.chartColors = {
	red: ''rgb(255, 99, 132)'',
	orange: ''rgb(255, 159, 64)'',
	yellow: ''rgb(255, 205, 86)'',
	green: ''rgb(75, 192, 192)'',
	blue: ''rgb(54, 162, 235)'',
	purple: ''rgb(153, 102, 255)'',
	grey: ''rgb(201, 203, 207)''
};
	window.randomScalingFactor = function() {
		 return Math.round(Math.random() * 100);
	};
		var barChartData = {
			labels: [''January'', ''February'', ''March'', ''April'', ''May'', ''June'', ''July''],
			datasets: [{
				label: ''Dataset 1'',
				backgroundColor: [
					window.chartColors.red,
					window.chartColors.orange,
					window.chartColors.yellow,
					window.chartColors.green,
					window.chartColors.blue,
					window.chartColors.purple,
					window.chartColors.red
				],
				yAxisID: ''y-axis-1'',
				data: [
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor()
				]
			}, {
				label: ''Dataset 2'',
				backgroundColor: window.chartColors.grey,
				yAxisID: ''y-axis-2'',
				data: [
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor()
				]
			}]
		};
		window.onload = function() {
			var ctx2 = document.getElementById(''chart-area-doughnut'').getContext(''2d'');
			window.myDoughnut = new Chart(ctx2, config);
			var ctx1 = document.getElementById(''canvas'').getContext(''2d'');
			window.myBar = new Chart(ctx1, {
				type: ''bar'',
				data: barChartData,
				options: {
					responsive: true,
					title: {
						display: true,
						text: ''Chart.js Bar Chart - Multi Axis''
					},
					tooltips: {
						mode: ''index'',
						intersect: true
					},
					scales: {
						yAxes: [{
							type: ''linear'', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							display: true,
							position: ''left'',
							id: ''y-axis-1'',
						}, {
							type: ''linear'', // only linear but allow scale type registration. This allows extensions to exist solely for log scale for instance
							display: true,
							position: ''right'',
							id: ''y-axis-2'',
							gridLines: {
								drawOnChartArea: false
							}
						}],
					}
				}
			});
		};
		var config = {
			type: ''doughnut'',
			data: {
				datasets: [{
					data: [
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
						randomScalingFactor(),
					],
					backgroundColor: [
						window.chartColors.red,
						window.chartColors.orange,
						window.chartColors.yellow,
						window.chartColors.green,
						window.chartColors.blue,
					],
					label: ''Dataset 1''
				}],
				labels: [
					''Red'',
					''Orange'',
					''Yellow'',
					''Green'',
					''Blue''
				]
			},
			options: {
				responsive: true,
				legend: {
					position: ''top'',
				},
				title: {
					display: true,
					text: ''Chart.js Doughnut Chart''
				},
				animation: {
					animateScale: true,
					animateRotate: true
				}
			}
		};
	</script>', 'web', '', '');

INSERT INTO om_dashboard VALUES (nextval('om_dashboard_seq'), 1, 'C1', 1, (SELECT om_widget FROM om_widget WHERE libelle='Ma carte'));
INSERT INTO om_dashboard VALUES (nextval('om_dashboard_seq'), 1, 'C2', 1, (SELECT om_widget FROM om_widget WHERE libelle='Lorem ipsum'));
INSERT INTO om_dashboard VALUES (nextval('om_dashboard_seq'), 1, 'C3', 1, (SELECT om_widget FROM om_widget WHERE libelle='Statistiques'));

--
COMMIT;

