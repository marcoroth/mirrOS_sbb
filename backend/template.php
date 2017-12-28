<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<?php

  $sbb_station = getConfigValue('sbb_station');
  $sbb_limit = getConfigValue('sbb_limit');
  $sbb_time_to_station = getConfigValue('sbb_time_to_station');
  $sbb_cols = getConfigValue('sbb_cols');
  $sbb_cols_width = getConfigValue('sbb_cols_width');

  if($sbb_station == 'GLANCR_DEFAULT' || $sbb_station == "" || $sbb_station == "") {	$sbb_station = 'Basel SBB'; }
  if($sbb_limit == 'GLANCR_DEFAULT' || $sbb_limit == "") {	$sbb_limit = 5; }
  if($sbb_time_to_station == 'GLANCR_DEFAULT' || $sbb_time_to_station == "") {	$sbb_time_to_station = 0; }
  if($sbb_cols == 'GLANCR_DEFAULT' || $sbb_cols == "") {	$sbb_cols = 'type,final_station,departure'; }
  if($sbb_cols_width == 'GLANCR_DEFAULT' || $sbb_cols_width == "") {	$sbb_cols_width = '20,50,10'; }

  $sbb_cols = explode(",", trim($sbb_cols));
  $sbb_cols_width = explode(",", trim($sbb_cols_width));

  $sbb_available_cols = ["type", "final_station", "departure", "departure_in", "platform", "delay"];
  $sbb_all_cols = $sbb_cols;

  foreach ($sbb_available_cols as $sbb_key => $sbb_col) {
    if (!in_array($sbb_col,$sbb_all_cols)){
      array_push($sbb_all_cols, $sbb_col);
    }
  }

?>

<h5><?php echo _('sbb_title_stationname');?></h5>
<input type="text" id="sbb_station" placeholder="<?php echo _('sbb_title_stationname');?>" value="<?php echo $sbb_station; ?>"/>

<h5><?php echo _('sbb_title_limit');?></h5>
<input type="number" id="sbb_limit" placeholder="<?php echo _('sbb_title_limit');?>" value="<?php echo $sbb_limit; ?>"/>

<h5><?php echo _('sbb_title_minute_to_walk');?></h5>
<input type="number" id="sbb_time_to_station" placeholder="<?php echo _('sbb_title_minute_to_walk');?>" value="<?php echo $sbb_time_to_station; ?>"/>

<h5><?php echo _('sbb_title_cols');?></h5>


<ul id="sbb_sortable">

 <?php

		$sbb_not_in_list_count = 0;

		foreach ($sbb_all_cols as $sbb_key => $sbb_col) {
			$sbb_checked = "";
			$sbb_width = $sbb_cols_width[$sbb_key-$sbb_not_in_list_count];

			if (in_array($sbb_col, $sbb_cols)){
				$sbb_checked = "checked";
			} else {
				$sbb_width = "10";
				$sbb_not_in_list_count++;
			}

			echo '<li class="ui-state-default" name="cols" value="'.$sbb_col.'"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" name="cols[]" value="'.$sbb_col.'" '.$sbb_checked.'/> '._("sbb_".$sbb_col).' <input type="text" name="cols_width[]" value="'.$sbb_width.'" placeholder="'._("sbb_width_placeholder").'"/></li>';
		}

 ?>
</ul><br>


<!-- <h5><?php echo _("sbb_advanced_options") ?></h5>
<a href="/modules/sbb/backend/lines.php" target="_blank">Erweiterte Einstellungen</a><br /><br /> -->

<a href="/modules/sbb/assets/reset.php"><?php echo _("sbb_reset_config"); ?></a><br /><br />

<div class="block__add" id="sbb__edit">
	<button class="sbb__edit--button" href="#">
		<span><?php echo _('sbb_save'); ?></span>
	</button>
</div>
