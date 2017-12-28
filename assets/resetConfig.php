<?php

	include('../../../config/glancrConfig.php');

	setConfigValue("sbb_station", "Basel SBB");
	setConfigValue("sbb_lines", "");
	setConfigValue("sbb_categories", "");
	setConfigValue("sbb_time_to_station", "0");
	setConfigValue("sbb_limit", "5");
	setConfigValue("sbb_cols", "type,final_station,departure,departure_in,platform,delay");
	setConfigValue("sbb_cols_width", "20,50,20,10,10,10");
	setConfigValue("reload", "1");

	header("location: /config/");

?>
