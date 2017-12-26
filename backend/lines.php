<?php
include('../../../config/glancrConfig.php');

$language = getConfigValue('language');

putenv("LANG=$language");
setlocale(LC_ALL, $language . '.utf8');

setGetTextDomain("/var/www/html/locale");

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
  <title><?php echo _('module overview');?></title>
  <link rel="stylesheet" type="text/css" href="../../../config/css/main.css">
  <link rel="stylesheet" href="../../../config/bower_components/foundation-icon-fonts/foundation-icons.css" media="screen" title="no title" charset="utf-8">

  <style type="text/css">
    .error {
      border: 2px solid red;
    }
    .reveal a {
      color: #14243C;
    }
    a {
      text-decoration: underline;
    }
    .validate {
      position: relative;
      top: -19px;
      font-size: small;
      color: #999;
      left: 2px;
      display: none;
    }
  </style>
</head>
<body>

  <header class="expanded row">
    <div class="small-12 columns site__title">
      <img src="../../../config/assets/glancr_logo.png" width="57" height="30" alt="GLANCR Logo" srcset="../../../config/assets/glancr_logo.png 57w, ../../../config/assets/glancr_logo@2x.png 114w, ../../../config/assets/glancr_logo@2x.png 171w">
    </div>
  </header>

  <main class="container">
    <section>
      <div class="row">
        <div class="small-12 columns">
          <p class="instruction__stepper">Filtern der Linien/Kategorien</p>
          <h3 class="instruction__title">Erweiterte Einstellungen SBB</h3>
          <p><a href="/" id="overview">Modulübersicht</a> > Erweiterte Einstellungen</p>

          <span class="zeitraum"></span><br /><br />

          <i class="fa fa-5x fa-spinner fa-spin"></i>
          <div class="lines multi" style="width: 100%;"></div><br /><br />

          <div class="block__add" id="sbb__edit">
            <button class="lines-save" href="#">
              <span>Speichern</span>
            </button>
          </div>

          <style>
            body {
              font-family: Arial;
            }

            .multi {
              display: inline-block;;
              width: 50%;
            }

            .multi div {
              display: inline-block;;
              width: 250px;
              overflow: hidden;
              text-overflow: ellipsis;
              height: 20px;
              white-space: nowrap;
            }

            .multi i {
              height: 100px !important;
              overflow: visible !important;
            }

            .lines h3 {
              border-top: 1px solid #ccc;
              padding-top: 15px;
            }

            label {
              color: white;
            }
          </style>

          <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
          <script src="https://code.jquery.com/jquery-2.2.3.min.js" charset="utf-8"></script>

          <script>
            var station;

            $.getJSON("../assets/getSBBConfig.php").done(function(data){
              station = data[0];

              var limit = 400;
              var fields =	"&fields[]=sbb/name" +	"&fields[]=sbb/category" + "&fields[]=sbb/stop/departureTimestamp"+ "&fields[]=sbb/to";
              var url = "http://transport.opendata.ch/v1/sbb?station="+station+"&limit="+limit+fields;
              var lines = [];
              var categories = [];
              var previous_category = "";
              var data;
              var selected_lines = [];
              var selected_categories = [];


              $(document).ready(function() {
                getSBB();
              });

              function getSBB() {
                $.ajax({ url: url }).done(function( content ) {
                  data = content;
                  $.each(content.sbb, function( index, value ) {

                    name = value.name;
                    category = value.category;
                    to = value.to;
                    obj = {};
                    obj.line = name;
                    obj.category = category;
                    obj.to = to;
                    push_line = true;

                    $.each(lines, function(l_index, el) {
                      if (el.line == name){ push_line = false}
                    });

                    if (push_line){ 	lines.push(obj); }
                    if (!($.inArray(category, categories) > -1)){	categories.push(category);	}
                  });

                  lines.sort(function(a,b) {return (a.line > b.line) ? 1 : ((b.line > a.line) ? -1 : 0);} );
                  $(".fa-spin").remove();
                  $.each(lines, function(index, value ) {

                    if (previous_category != value.category){
                      $(".lines").append("<br /><h3><input type='checkbox' value='"+value.category+"' id='"+value.category+"' class='category' name='category'> <label for='"+value.category+"'>" + value.category + "</label></h3>");
                    }

                    $(".lines").append("<div><input type='checkbox' id='"+value.line+"' class='"+value.category+" line' name='line'> <label for='"+value.line+"'>"+value.line+" ("+value.to+")</label></div>");
                    previous_category = value.category;
                  });

                  $(".category").change(function(){
                    category = $(this).val();
                    $("."+category).prop('disabled', function(i, v) { return !v; });
                    $("."+category).parent().toggle();
                    $("."+category).prop("checked", false);
                  });

                  $(".zeitraum").html("Die in den Klammern angebene Endstation, bezieht sicht nicht auf die Fahrtrichtung, sondern dient nur als Anhaltspunkt zum Identifizieren der Linie.<br />Alle Linen, welche in den n&auml;chsten <b>"+ limit +"</b> Verbindungen vorkommen werden angezeigt.<br />Im Zeitraum von: <b>" + toDate(content.sbb[0].stop.departureTimestamp) + "</b> bis: <b>"+ toDate(content.sbb[content.sbb.length-1].stop.departureTimestamp))+"</b><br />";
                });
              }

              $(".lines-save").click(function(e){
                e.preventDefault();
                var categories_raw = $("input:checked[name='category']");
                var lines_raw = $("input:checked[name='line']");

                selected_lines = [];
                selected_categories = [];

                $.each(lines_raw, function(index, el) {
                  selected_lines.push(el.id);
                });

                $.each(categories_raw, function(index, el) {
                  selected_categories.push(el.id);
                });

                $.post('/config/setConfigValueAjax.php', {'key': 'sbb_lines', 'value': selected_lines.join(",")});
                $.post('/config/setConfigValueAjax.php', {'key': 'sbb_categories', 'value': selected_categories.join(",")});

                window.close();
              });

              function toDate(timestamp){
                var date = new Date(timestamp*1000);

                var day = date.getDate();
                var month = date.getMonth()+1;
                var year = date.getFullYear();

                var hour = date.getHours();
                var min = date.getMinutes();

                if (hour <= 9) {hour = "0"+hour}
                if (min <= 9) {min = "0"+min}
                if (day <= 9) {day = "0"+day}
                if (month <= 9) {month = "0"+month}

                return day + "." + month + "." + year + " " +hour + ':' + min;
              }
            });

          </script>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
