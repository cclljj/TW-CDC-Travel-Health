<html>
<head>
</head>
<?php

function removeBOM($data) {
    if (0 === strpos(bin2hex($data), 'efbbbf')) {
       return substr($data, 3);
    }
    return $data;
}

$items = file_get_contents("CDC_CountryEpidLevel.json");
//$items = file_get_contents("https://www.cdc.gov.tw/CountryEpidLevel/ExportJSON");
$json = json_decode(removeBOM($items),true);

$all = array();
$msgs = ["Watch","Alert","Warning"];
//$colors = ["FFA500","FF0000","FF00FF"];
$colors = ["FFFF00","FFA500","FF0000"];
$levels = [[],[],[]];

foreach ($json as $item){
	$alert_disease = $item["alert_disease"];
	
	$tmp = array();
	if (isset($all[$alert_disease])){
	} else {
		$all[$item["alert_disease"]] = array();
	}
	foreach ($item as $key => $value){
		$tmp[$key] = $value;
	}
	$all[$item["alert_disease"]][$item["ISO3166"]] = $tmp;

	for ($i=count($msgs)-1;$i>=0;$i--){
		if (strpos($tmp["severity_level"], $msgs[$i])!==false){
			array_push($levels[$i], $tmp["ISO3166"]);
		}
	}
}

//var_dump($all);
//var_dump($levels);

?>

<body>
<div id="page_header" align="center">
<h2>國際間旅遊疫情建議</h2>
資料來源：<a href="https://www.cdc.gov.tw/CountryEpidLevel/" alt="data source">台灣衛生福利部疾病管制署</a>
</div>
<div id="mapcontainer" align="center"> <!-- You can place the container wherever you want, but it has to be before loading the code-->
</div>
<link rel="stylesheet" href="css/bootstrap.min.css">

<script src="js/jquery.min.js"></script> 
<script src="js/topojson.v1.min.js"></script>
<!-- jQuery library -->
<!-- Latest compiled JavaScript -->
<script src="js/bootstrap.min.js"></script>
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.min.css">
<!-- datatables -->
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/d3.v3.min.js"></script>
<script src="js/d3.geo.projection.v0.min.js"></script>
<script src="js/queue.v1.min.js"></script>

<link rel="stylesheet" href="css/worldmap.v1.css"> 
<script src="js/worldmap.v1.js"></script>

<script>
var countries = {};
<?php
	$js_str = array();
	foreach ($all as $disease => $value){
		for ($i=0;$i<count($msgs);$i++){
			foreach ($levels[$i] as $country){
				if (isset($all[$disease][$country])){
					$tmp = $all[$disease][$country];
					$ss = "<br>".$tmp["alert_disease"].":&nbsp;".$tmp["severity_level"];
					if (isset($js_str[$country])){
						$js_str[$country] = $js_str[$country].$ss;
					} else {
						$js_str[$country] = $ss;
					}
				}
			}
		}
	}
	foreach ($js_str as $key => $value){
		echo "countries[\"$key\"] = \"$value\";\n";
	}
?>



   // The following codes is based on the example provided on the web page: https://worldmapjs.org/cloropleth.html
   // 2020.2.17

   var createmap = new Worldmap({  
	elementid: "#mapcontainer",
     	mapstyle: {   // Change the map style
       		//ocean: "#4A5B62",
        	//region: "#F3F3F3",
        	//border : "#ffffff"
       		ocean: "#CBEEFB",
        	region: "#c0e0c0",
        	border : "#000000"
    	},
    	project: {  
        	name: "Mercator",
        	//zoomlevel: 6 // If you want it to zoom into an area
        	//zoomarea:[-122.417, 37.775] 
    	},
   	showtable:false, // Hide Table
   	editpanel:false,  // Hide Edit
   	//dataType: 'csv',
   	//dataurl: 'countriesdata.csv', // location of the file
   	//defaultfill: "steelblue", // default fill color
   	//defaultsize: 30,
   	player: false // show player
   }); 
 
// Adds the button an 
 
$(document).ready(function(){
//	$("#playeranim").click(function() {

   setTimeout(function(){
	createmap.update([
			//{"location":"BR","color":"#0E5FF6"},
<?php
	foreach ($all as $disease => $value){
		for ($i=0;$i<count($msgs);$i++){
			foreach ($levels[$i] as $country){
				if (isset($all[$disease][$country])){
					$tmp = $all[$disease][$country];
					echo "\t\t\t{\"location\":\"".$country."\",\"color\":\"#".$colors[$i]."\"},\n";
				}
			}
		}
	}
?>
			],
			"cloropleth")}, 500);

 
//	});
});
</script>

<div class="divider"></div>

<div class="country_names" align="center">
<table border=1>
<?php
	foreach ($all as $disease => $value){
		echo "<tr><td colspan=3 align=\"center\"><font size=\"+2\" color=\"0000FF\">";
		echo $disease;
		echo "</font></td></tr>";
		echo "<tr>";
		for ($i=0;$i<count($msgs);$i++){
			echo "<td bgcolor=\"".$colors[$i]."\" align=\"center\"><font size=\"+2\" color=\"#000000\">".$msgs[$i]."</font></td>";
		}
		echo "</tr>";
		echo "<tr valign=\"top\">";
		for ($i=0;$i<count($msgs);$i++){
			echo "<td align=\"center\">";
			foreach ($levels[$i] as $country){
				if (isset($all[$disease][$country])){
					$tmp = $all[$disease][$country];
					echo $tmp["areaDesc"]." (".$tmp["areaDesc_EN"].")<br>";
				}
			}
			echo "</td>";
		}
		echo "</tr>";
	}
?>
</table>
</div>
<hr>
<div class="notes" align="center">
GitHub | <a href="https://worldmapjs.org/cloropleth.html">WorldMap.js</a>
</div>


</body>
</html>

