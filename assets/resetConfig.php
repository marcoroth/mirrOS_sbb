<?php

	include('../../../config/glancrConfig.php');

	setConfigValue("sbb_station", "");
	setConfigValue("sbb_lines", "");
	setConfigValue("sbb_categories", "");
	setConfigValue("sbb_time_to_station", "");
	setConfigValue("sbb_limit", "");
	setConfigValue("sbb_cols", "");
	setConfigValue("sbb_cols_width", "");
	setConfigValue("reload", "1");

	header("location: /config/");

?>
