var sbb_station;
var sbb_limit;
var sbb_amount;
var sbb_time_to_station;

var sbb_lines_raw;
var sbb_categories_raw;
var sbb_cols_raw;
var sbb_cols_width_raw;

var sbb_lines;
var sbb_categories;
var sbb_cols;
var sbb_cols_width;

var sbb_init = true;

$(document).ready(function () {
	getSBBParameter();
});

function getSBBParameter() {

	$.getJSON( "../modules/sbb/assets/getSBBConfig.php", function( data ) {
		//console.log(data);

		sbb_station = data["station"];
		sbb_limit = data["limit"];
		sbb_time_to_station = data["time_to_station"];
		sbb_cols_raw = data["cols"];
		sbb_cols_width_raw = data["cols_width"];
		sbb_lines_raw = data["lines"];
		sbb_categories_raw = data["categories"];

		if (typeof sbb_lines != "undefined") {
			sbb_lines = sbb_lines_raw.split(",");
		} else {
			sbb_lines = [];
		}

		if (typeof sbb_categories != "undefined") {
			sbb_categories = sbb_categories_raw.split(",");
		} else {
			sbb_categories = [];
		}

		sbb_cols = sbb_cols_raw.split(",");
		sbb_cols_width = sbb_cols_width_raw.split(",");
		sbb();
	});

	// alle 5 Sekunden aktualiseren
	window.setTimeout(function() {
		sbb();
	}, 5000);

}

function sbb() {

	var sbb_width_all = 0;
	 $.each(sbb_cols_width, function(index, el) {
	   sbb_width_all += parseFloat(sbb_cols_width[index]);
	});

	if (sbb_width_all != 100) {
	  sbb_width_wo_last = 100 - sbb_width_all + parseFloat(sbb_cols_width[sbb_cols_width.length-1]);
	  sbb_cols_width[sbb_cols_width.length-1] = sbb_width_wo_last;
	}

	var sbb = [];
	var getCount = 0;
	var filtering = true;

	var data;
	var connection_count;

	// Falls es nichts zu Filtern gibt
	if (sbb_lines.length == 0 && sbb_categories.length == 0){
	  filtering = false;
	  amount = sbb_limit;
	  lines_text = "Alle";
	  categories_text = "Alle";
	} else {
	  lines_text = lines;
	  categories_text = categories;
	}

	var fields =
	"&fields[]=stationboard/to" +
	"&fields[]=stationboard/name" +
	"&fields[]=stationboard/category" +
	"&fields[]=stationboard/stop/platform" +
	"&fields[]=stationboard/stop/delay" +
	"&fields[]=stationboard/stop/departureTimestamp";

	var url = "http://transport.opendata.ch/v1/stationboard?station=" + sbb_station + "&limit=" + sbb_amount + fields;

	$("#sbb_station").text(sbb_station);
	if (sbb_init){
		$(".sbb tr:last").append("<tr><td>Anzahl Abfahrten:</td><td> " + sbb_limit + " Stk. </td></tr><tr><td>Folgende Linen:</td><td> " + lines_text + "</td></tr><tr><td>Folgende Kategorien:</td><td> " + categories_text + "</td></tr><tr><td>Anzahl Gehminuten:</td><td> " + sbb_time_to_station + " min</td></tr></td>");
		sbb_init = false;
	}
	$(document).ready(function() {
	  window.setTimeout(function() {
	    updateSBB();
	  }, 1000);
	});

	function updateSBB() {

	  connection_count = 0;
	  getCount = 0;
	  var date_now = Date.now() + (sbb_time_to_station*1000*60);

	  getSBB(date_now);

	  window.setTimeout(function() {
	    updateSBB();
	  }, 10 * 1000);
	}

	function getSBB(datetime) {

	  getCount++;
	  // console.log("Begin search: " + getCount);

	  $.ajax({ url: url + "&datetime=" + toDate(datetime) }).done(function( content ) {

	    data = content;

	    if (filtering){
	      $.each(content.stationboard, function( index, value ) {
	        if (($.inArray(value.name, sbb_lines) > -1 && connection_count < sbb_limit) || (lines.length == 0 && categories.length == 0 && connection_count < sbb_limit) || ($.inArray(value.category, sbb_categories) > -1 && connection_count < sbb_limit)){
	          sbb.push(value);
	          connection_count++;
	          // console.log("Found Entry: " + connection_count + " - " + timeConverter(value.stop.departureTimestamp) + " - in Query NO: " + getCount);
	        }
	      });
	    } else {
	      sbb = content.stationboard.slice(0, sbb_limit);
	      connection_count = sbb_limit;
	    }

	    // Falls noch nicht genug Verbindungen geladen wurden
	    if (connection_count < sbb_limit) {

	      //console.log(content.stationboard[content.stationboard.length-1].stop.departureTimestamp*1000);
	      // Holt den letzten Wert aus der Liste und gibt diese Wert an, um ab diesem Zeitpunkt weiter zu suchen
	      getSBB(content.stationboard[content.stationboard.length-1].stop.departureTimestamp*1000);
	    }

	    if (connection_count >= sbb_limit){

	      // console.log("Finished search");
	      // console.log("--------");
	      $(".sbb tr").addClass("old-tr");

	      $.each(sbb, function( index, value ) {

	        $(".sbb .old-tr:first-of-type").remove();
	        name = value.name;

	        if ($.inArray(value.category, sbb_categories) > -1) {name = value.category}
	        if (name == "S") {name = value.name}
	        if (name == "BUS") {name = value.name}
	        if (name == "NFT") {name = value.name}
	        if (name == "NFB") {name = value.name}
	        if (name == "T") {name = value.name}
	        var type = this.name;

	        var final_station = this.to;
	        var departure = timeConverter(this.stop.departureTimestamp);
	        var platform = removeNull(this.stop.platform);
	        var timestamp_now = Math.ceil(Date.now()/1000);
	        var timestamp_departure = parseInt(this.stop.departureTimestamp);
	        var delay = removeNull(this.stop.delay);

	        var departure_in = parseInt((timestamp_departure-timestamp_now)/60)+1;

	        if (departure_in == 0){
	          departure_in = '<i class="fa fa-train blink_me" aria-hidden="true"></i>';
	        } else if (departure_in < 0) {
	          if (delay != "-") {
	            departure_in = '<i class="fa fa-clock-o" aria-hidden="true"></i>';
	            delay = delay + "'";
	          } else {
	            departure_in = "Morgen";
	          }
	        } else {
	          departure_in = departure_in+"'";
	        }

	          $(".sbb").append("<tr class='sbb-row'>");
	          $.each(sbb_cols, function(index, value){
	            $(".sbb tr:last").append("<td width='" + sbb_cols_width[index]+"%'>" + eval(sbb_cols[index]) + "</td>");
	          });
	          $(".sbb").append("</tr>");
	      });

	      sbb = [];
	    }

	    if (getCount > 8 && connection_count == 0 ){
	      $(".sbb tr:first-of-type").text("Keine Abfahrten gefunden an dieser Haltestelle mit den angegeben Linien: "+ lines.concat());
	    }
	  });
	}
}

function removeNull(string) { if (string == "null" || string == null) {  return "";  } else { return string; }  }

function timeConverter(timestamp){
  var date = new Date(timestamp * 1000);
  var hour = date.getHours();
  var min = date.getMinutes();

  if (hour <= 9) {hour = "0"+hour}
  if (min <= 9) {min = "0"+min}

  return hour + ':' + min;
}

function toDate(timestamp){
  var date = new Date(timestamp);

  var day = date.getDate();
  var month = date.getMonth()+1;
  var year = date.getFullYear();

  var hour = date.getHours();
  var min = date.getMinutes();

  if (hour <= 9) {hour = "0"+hour}
  if (min <= 9) {min = "0"+min}

  return year + "-" + month + "-" + day + " " + hour + ':' + min;
}
