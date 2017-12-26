<?php
	include('../../../config/glancrConfig.php');

	$station = getConfigValue('sbb_station');
	$limit = getConfigValue('sbb_limit');
	$time_to_station = getConfigValue('sbb_time_to_station');
	$cols = getConfigValue('sbb_cols');
	$cols_width = getConfigValue('sbb_cols_width');
	$lines = getConfigValue('sbb_lines');
	$categories = getConfigValue('sbb_categories');


	// wenn der parameter noch nicht gesetzt wurde
	if($station == 'GLANCR_DEFAULT') {	$station = 'Basel SBB'; }
	if($limit == 'GLANCR_DEFAULT') {	$limit = 5; }
	if($time_to_station == 'GLANCR_DEFAULT') {	$time_to_station = 0; }
	if($cols == 'GLANCR_DEFAULT') {	$cols = 'type,final_station,departure,departure_in,platform,delay'; }
	if($cols_width == 'GLANCR_DEFAULT') {	$cols_width = '20,50,20,10,10,10'; }
	if($lines == 'GLANCR_DEFAULT') {	$lines = ''; }
	if($categories == 'GLANCR_DEFAULT') {	$categories = ''; }

	$params = [
							"station" => $station,
							"limit" => $limit,
							"time_to_station" => $time_to_station,
							"cols" => $cols,
							"cols_width" => $cols_width,
							"lines" => $lines,
							"categories" => $categories
						];

	echo json_encode($params);

?>
