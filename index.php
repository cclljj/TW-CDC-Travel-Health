<html>
<head>
<title>Taiwan CDC - 國際間旅遊疫情建議</title>
</head>
<?php

function removeBOM($data) {
    if (0 === strpos(bin2hex($data), 'efbbbf')) {
       return substr($data, 3);
    }
    return $data;
}

//$items = file_get_contents("CDC_CountryEpidLevel.json");
$items = file_get_contents("https://www.cdc.gov.tw/CountryEpidLevel/ExportJSON");
$json = json_decode(removeBOM($items),true);

$all = array();
$msgs = ["Watch","Alert","Warning"];
$colors = ["FFFF00","FFA500","FF0000"];
$levels = [[],[],[]];
$pandemic = -1;
foreach ($json as $item){
	if ($item["ISO3166"]=="") {
		$item["ISO3166"] = "ALL";
		$item["areaDesc_EN"] = "Others";
	}
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
			if ($tmp["ISO3166"]=="ALL") {
				$pandemic = $i;
			}
		}
	}
}
?>

<body>
<div id="page_header" align="center">
<h2>國際間旅遊疫情建議</h2>
資料來源：<a href="https://www.cdc.gov.tw/CountryEpidLevel/" alt="data source">台灣衛生福利部疾病管制署</a>
</div>

<table border=0>
<tr valign="top">
<td>
<div id="country_names" class="country_names">
<table border=1i width=300>
<?php
foreach ($all as $disease => $value){
	echo "<tr><td align=\"center\"><font size=\"+2\" color=\"0000FF\">";
	echo $disease;
	echo "</font></td></tr>";
	for ($i=count($msgs)-1;$i>=0;$i--){
		$num = 0;
		echo "<tr>";
		echo "<td bgcolor=\"".$colors[$i]."\" align=\"center\"><font size=\"+2\" color=\"#000000\">".$msgs[$i]."</font></td>";
		echo "</tr>";
		echo "<tr valign=\"top\">";
		echo "<td align=\"center\">";
		foreach ($levels[$i] as $country){
			if (isset($all[$disease][$country])){
				$tmp = $all[$disease][$country];
				if ($num==0){
					echo $tmp["areaDesc"]." (".$tmp["areaDesc_EN"].")";
				} else {
					echo ", ".$tmp["areaDesc"]." (".$tmp["areaDesc_EN"].")";
				}
				$num = $num + 1;
			}
		}
		echo "</td>";
		echo "</tr>";
	}
}
?>
</table>
</div>
</td>
<td>
<div id="mapcontainer" > <!-- You can place the container wherever you want, but it has to be before loading the code-->
</div>
</td>
</tr></table>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">

<script src="jquery-3.6.4.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script src="//d3js.org/d3.v3.min.js"></script>
<script src="//d3js.org/topojson.v1.min.js"></script>
<script src="//d3js.org/d3.geo.projection.v0.min.js"></script>
<script src="//d3js.org/queue.v1.min.js"></script> 

<link rel="stylesheet" href="css/worldmap-twcdc.v1.css"> 
<script src="js/worldmap-twcdc.v1.js"></script>

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
<?php
	if ($pandemic<0) {
?>
        	region: "#c0e0c0",
<?php
	} else {
?>
        	region: "#<?php echo $colors[$pandemic]; ?>",
<?php
	}
?>
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
				if ($country=="ALL") continue;
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


<hr>
<div class="notes" align="center">
	<a href="https://github.com/cclljj/TW-CDC-Travel-Health">GitHub</a> | <a href="https://worldmapjs.org/cloropleth.html">WorldMap.js</a>
</div>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-61532541-2"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-61532541-2');
</script>



</body>
</html>

