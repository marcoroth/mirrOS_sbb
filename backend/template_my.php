<?php

  $domain = 'sbb';
  bindtextdomain($domain, "/var/www/html/locale");
  bind_textdomain_codeset($domain, 'UTF-8');

  $sbb = file('/var/www/html/config/module_configs/sbb');

  $station = trim($sbb[0]);
  $limit = trim($sbb[1]);
  $amount = trim($sbb[2]);
  $lines_raw = trim($sbb[3]);
  $categories_raw = trim($sbb[4]);
  $time_to_station = trim($sbb[5]);
  $cols = explode(",", trim($sbb[6]));
  $cols_width = explode(",", trim($sbb[7]));

  $available_cols = ["type", "final_station", "departure", "departure_in", "platform", "delay"];
  $all_cols = $cols;

  foreach ($available_cols as $key => $value) {
    if (!in_array($value,$all_cols)){
      array_push($all_cols, $value);
    }
  }

?>
<script src="//code.jquery.com/jquery-1.10.2.js"></script>

<link rel="stylesheet" type="text/css" href="bower_components/jquery/dist/jquery-ui.min.css">
<script type="text/javascript" src="bower_components/jquery/dist/jquery-ui.min.js"></script>
<script type="text/javascript" src="bower_components/jquery/dist/jquery.ui.autocomplete.html.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css" />
<script src="https://raw.githubusercontent.com/OpendataCH/Transport/master/web/media/js/moment.min.js" charset="utf-8"></script>

<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<style>
  #sbb_sortable { list-style-type: none; margin: 0; padding: 0;}
  #sbb_sortable li {  padding-top: 0.4em; padding-left: 1.5em; font-size: 1em; height: 36px; margin-bottom: 5px; }
  #sbb_sortable li span { position: absolute; margin-left: -1.3em; margin-top: 0.2em}


  #sbb_sortable input[type='text'] {
    display: inline-block;
    box-sizing: inherit;
    width: 200px;
    height: 1.4rem;
    padding: 0.0rem;
    float: right;
    margin-right: 15px;
    padding-left: 10px;
  }
</style>


<div class="large reveal" data-reveal id="gr-modal-sbb" data-animation-in="fade-in" data-animation-out="fade-out" tabindex="1" role="dialog">
  <button class="close-button" data-close aria-label="Close modal" type="button">
    <span aria-hidden="true">&times;</span>
  </button>

  <p>
    <h5><?php echo dgettext($domain, "sbb_title_stationname") ?></h5>
    <input type="text" name="station" class="station" id="station" value="<?php echo $station;?>" placeholder="Haltestelle">

    <h5><?php echo dgettext($domain, "sbb_title_limit") ?></h5>
    <input type="number" name="limit" class="limit" id="limit" value="<?php echo $limit;?>" placeholder="Wieviele Abfahrten sollen angeziegt werden">

    <!-- <h5><?php echo dgettext($domain, "sbb_title_lines") ?></h5>
    <input type="text" name="lines" class="lines" id="lines" value="<?php echo $lines_raw;?>" placeholder="Weleche Linien sollen angezeigt werden">

    <h5><?php echo dgettext($domain, "sbb_title_category") ?></h5>
    <input type="text" name="categories" class="categories" id="categories" value="<?php echo $categories_raw;?>" placeholder="Wieviele Kategorien sollen angeziegt werden"> -->

    <h5><?php echo dgettext($domain, "sbb_title_minute_to_walk") ?></h5>
    <input type="text" name="time_to_station" class="time_to_station" id="time_to_station" value="<?php echo $time_to_station;?>" placeholder="Es werden keine Abfahrten kleiner als die angegebene Minutenzahl angezeigt">

    <h5><?php echo dgettext($domain, "sbb_title_cols") ?></h5>
    <script>
     $(function() {
       $( "#sbb_sortable" ).sortable();
       $( "#sbb_sortable" ).disableSelection();
     });
     </script>

   <ul id="sbb_sortable">

     <?php

        $not_in_list_count = 0;

        foreach ($all_cols as $key => $value) {
          $checked = "";
          $width = $cols_width[$key-$not_in_list_count];

          if (in_array($value, $cols)){
            $checked = "checked";
          } else {
            $width = "";
            $not_in_list_count++;
          }

          echo '<li class="ui-state-default" name="cols" value="'.$value.'"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><input type="checkbox" name="cols[]" value="'.$value.'" '.$checked.'/> '.dgettext($domain, "sbb_".$value).' <input type="text" name="cols_width[]" value="'.$width.'" placeholder="'.dgettext($domain, "sbb_width_placeholder").'"/></li>';
        }



     ?>
   </ul><br>


   <h5><?php echo dgettext($domain, "sbb_advanced_options") ?></h5>
   <a  href="#" onclick="openAdvancedOptions()">Erweiterte Einstellungen</a><br /><br />


    <br /><div class="block__add" id="sbb-save" style="height: 50px"><button href="#"><b>Speichern</b></button></div><br />

  </p>
</div>

<script>

  var cols = [];
  var cols_width = [];


//$('input[value=""]').addClass('error');
$('#sbb-save').click(function() {

  cols = [];
  cols_width = [];

  $.each($("[name='cols[]']:checked"), function(el){
    cols.push($(this).val());

    var width = $(this).nextAll('input').last().val();
    console.log(width);
    if (width == ""){ width = 10}
    cols_width.push(width);
    $(this).nextAll('input').last().val(width);
  });

  $.each($("[name='cols[]']:not(:checked)"), function(el){
    $(this).nextAll('input').last().val("");
  });

  $.post('writeSBB.php', {station: $("#station").val(), limit: $("#limit").val(), lines_raw: $("#lines").val(), categories_raw: $("#categories").val(), time_to_station: $("#time_to_station").val(), cols: cols.join(","), cols_width: cols_width.join(",")})
  .done(function() {
    $('#ok').show(30, function() {
      $(this).hide('slow');
    });
  });
});


function openAdvancedOptions() {
    width = 1000;
    height = 1200;

    popupwindow("/config/modals/sbbLines.php", "test", width, height);
    //window.open(, "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,top="+((window.innerHeight/2)-(height/4))+",left="+((window.innerWidth/2)-(width/4))+",width="+width+",height="+height+"");
}

function popupwindow(url, title, w, h) {
  var left = (screen.width/2)-(w/2);
  var top = (screen.height/2)-(h/2);
  return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
}

</script>
