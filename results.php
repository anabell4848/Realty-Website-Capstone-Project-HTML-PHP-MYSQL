<!DOCTYPE html>
<html>
<?php
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);
require_once "config.php";

if(!isset($_SESSION)){
    session_start();
}
$data = $_SESSION['data'];
	
// Create connection
$conn = new mysqli($config['hostname'], $config['user'], $config['pass'], $config['database']);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
$escapedPOST = array();
foreach($_POST as $key => $value):
	$escapedPOST[$key] = $conn->real_escape_string($value);
endforeach;
$sql = <<<EOD
SELECT *
FROM House_Listing AS hl
	LEFT OUTER JOIN Agents_Directory AS ad ON hl.Agent_ID = ad.Agent_ID
	LEFT OUTER JOIN House_Details AS hd ON hl.List_Number = hd.List_Number
	LEFT OUTER JOIN House_Location AS hlo ON hl.List_Number = hlo.List_Number
	LEFT OUTER JOIN Sold_Info AS si on hl.List_Number = si.List_Number
WHERE hl.List_Number LIKE '%{$escapedPOST['list']}%'
	AND hlo.address LIKE '%{$escapedPOST['address']}%'
	AND ad.Contact LIKE '%{$escapedPOST['contactSearch']}%'
	AND si.Selling_Agent_ID LIKE '%{$escapedPOST['sellingAgentIDSearch']}%'
	AND ad.Office LIKE '%{$escapedPOST['officeSearch']}%'
EOD;

$status = array();
if(isset($escapedPOST['sActive']))
{
	$status[] = "'Active'";
}
if(isset($escapedPOST['sContingent']))
{
	$status[] = "'Contingent'";
}
if(isset($escapedPOST['sSold']))
{
	$status[] = "'Sold'";
}
if(count($status) > 0)
{
	$sql .= ' AND hl.Status IN (' . implode(',', $status) . ')';
}
if($escapedPOST['minPrice'] != "")
{
	$sql .= ' AND hl.Price >= ' . $escapedPOST['minPrice'];
}
if($escapedPOST['maxPrice'] != "")
{
	$sql .= ' AND hl.Price <= ' . $escapedPOST['maxPrice'];
}
if($escapedPOST['minSoldPrice'] != "")
{
	$sql .= ' AND hl.Sold_Price >= ' . $escapedPOST['minSoldPrice'];
}
if($escapedPOST['maxSoldPrice'] != "")
{
	$sql .= ' AND hl.Sold_Price <= ' . $escapedPOST['maxSoldPrice'];
}
if($escapedPOST['minAge'] != "")
{
	$sql .= ' AND hd.Years_Old >= ' . $escapedPOST['minAge'];
}
if($escapedPOST['maxAge'] != "")
{
	$sql .= ' AND hd.Years_Old <= ' . $escapedPOST['maxAge'];
}
if($escapedPOST['minClosing'] != "")
{
	$sql .= " AND si.Closing_Date >= '" . $escapedPOST['minClosing'] . "'";
}
if($escapedPOST['maxClosing'] != "")
{
	$sql .= " AND si.Closing_Date <= '" . $escapedPOST['maxClosing'] . "'";
}
if($_POST['filterInput'] != "")
{
	$filters = explode('~', $_POST['filterInput']);
	$filtersArray = array();
	foreach($filters as $filter):
		if($filter == "")
		{
			continue;
		}
		list($column, $value) = explode("||", $filter);
		$filtersArray[$column][] = $conn->real_escape_string($value);
	endforeach;
	foreach($filtersArray as $column => $values):
		$valueString = "('" . implode("','", $values) . "')";
		$sql .= " AND $column IN $valueString";
	endforeach;
}
if($escapedPOST['beds'] != "")
{
	if($escapedPOST['beds'] == 0)
	{
		$sql .= ' AND hd.Beds = ' . $escapedPOST['beds'];
	}
	else
	{
		$sql .= ' AND hd.Beds >= ' . $escapedPOST['beds'];
	}
}
if($escapedPOST['fBaths'] != "")
{
	if($escapedPOST['fBaths'] == 0)
	{
		$sql .= ' AND hd.F_Baths = ' . $escapedPOST['fBaths'];
	}
	else
	{
		$sql .= ' AND hd.F_Baths >= ' . $escapedPOST['fBaths'];
	}
}
if($escapedPOST['pBaths'] != "")
{
	if($escapedPOST['pBaths'] == 0)
	{
		$sql .= ' AND hd.P_Baths = ' . $escapedPOST['pBaths'];
	}
	else
	{
		$sql .= ' AND hd.P_Baths >= ' . $escapedPOST['pBaths'];
	}
}
$result = $conn->query($sql);

if($result === FALSE)
{
	die("Failed to search: " . $conn->error);
}
else
{
	
//----------------------graph and google maps display------------------
	?>
	

	<div id="googleMap" style="width:900px;height:600px;"></div>
	
	<!--box plot with vs year showing max/min/avg/etc for each year. I can also combine a line graph with that to connect the averages-->
	<img src="PricePlot.php" /><br>

	<!--number of listing for each of the 4 quartiles like a a histogram-->	
	<img src="NumberSoldHistogram.php" /><br>
	

	
	<p style="text-align: left;"><button onclick="self.location.href = '\excelOutput.php';">click here to download excel file</button></p>


<!---------------------- showing the data in a table--------------------->
	<table style="border:1px solid #000">
		<tr>
			<th>Listing</th>
			<th>Agent Info</th>
			<th>House Details</th>
			<th>Location</th>
			<th>Sale Info</th>
		</tr>
		<tr>
			<?php
			$data = array();
			$maxyear = 1970;
			$minyear = 2014;
			while($row = $result->fetch_assoc())
			{
				echo <<<EOD
<tr>
	<td class="listNumber">{$row['List_Number']}</td>
	<td>
		<table class="innerTable">
			<tr><th>Agent Name</th><td>{$row['Contact']}</td></tr>
			<tr><th>Agent ID</th><td>{$row['Agent_ID']}</td></tr>
			<tr><th>Office</th><td>{$row['Office']}</td></tr>
			<tr><th>Phone</th><td>{$row['Contact_Phone']}</td></tr>
		</table>
	</td>
	<td>
		<table class="innerTable">
			<tr><th>Beds</th><td>{$row['Beds']}</td></tr>
			<tr><th>F Baths</th><td>{$row['F_Baths']}</td></tr>
			<tr><th>P Baths</th><td>{$row['P_Baths']}</td></tr>
			<tr><th>Years Old</th><td>{$row['Years_Old']}</td></tr>
			<tr><th>Style</th><td>{$row['Style']}</td></tr>
		</table>
	</td>
	<td>
		<table class="innerTable">
			<tr><th>Address</th><td>{$row['Address']}</td></tr>
			<tr><th>County</th><td>{$row['County']}</td></tr>
			<tr><th>Area</th><td>{$row['Area']}</td></tr>
			<tr><th>Area Name</th><td>{$row['Area_Name']}</td></tr>
		</table>
	</td>
	<td>
		<table class="innerTable">
			<tr><th>Status</th><td>{$row['Status']}</td></tr>
			<tr><th>Asking Price</th><td>{$row['Price']}</td></tr>
			<tr><th>Sold Price</th><td>{$row['Sold_Price']}</td></tr>
			<tr><th>Closing Date</th><td>{$row['Closing_Date']}</td></tr>
			<tr><th>Selling Agent ID</th><td>{$row['Selling_Agent_ID']}</td></tr>
			<tr><th>Selling Office</th><td>{$row['Selling_Office_ID']}</td></tr>
		</table>
	</td>
</tr>
EOD;


//----------------------------------------------------------------------------------------------
//--------from here down is Annie's code for creating the downloadable excel and for google maps
		//array for ouputting csv/excel file
		$year = substr($row['Closing_Date'], 0, 4);  
		
		array_push($data, array("Address" => $row['Address'], "City" => $row['Area_Name'], "State" => "PA", 
		"ListNumber"=> $row['List_Number'], "Price"=> $row['Sold_Price'],"Year"=> $year ));
		
		
		if ($year>$maxyear){
			$maxyear = $year;
			//echo "maxyear=".$year;
		}
		if ($year<$minyear){
			$minyear = $year;		
			//echo "minyear=".$year;
		}
			}
			
			?>
		</tr>
	</table>
<?php
$_SESSION['data'] = $data;
}

?>

<body>

<?php
	//make minyear to maxyear individual years array
	$years =  array();
	for($i=$minyear; $i<=$maxyear; $i++){
		array_push($years, $i);
	
	}
	//print_r($years);
	

//calculate the histogram summary intervals
	$allprices = array();
	$allfivenumbers = array();
	foreach($data as $da){
		array_push($allprices, $da["Price"]);
	}
	$pusharr=fivenums($allprices, "");
	$allmin = $pusharr["Min"];
	$allmax = $pusharr["Max"];
	array_push($allfivenumbers,$pusharr);
	//print_r($allfivenumbers);
	
		
	rsort($allprices);	
	$allcount = count($allprices);
	$_SESSION['allfivenumbers']=$allfivenumbers;
	
	$zone1=0;
	$zone2=0;
	$zone3=0;
	$zone4=0;
	
//calculate for each year the 5 number summary
	$tempdata = array();	//for keeping track of values for each year
	$fivenumbers= array();
	
	$histogramcounter=1;
	foreach($years as $yr){
		//echo "</br>".$yr. "</br>";
		foreach($data as $dat){
			if ($dat["Year"]==$yr){
				//echo $dat["Price"]." ";
				array_push($tempdata, $dat["Price"]);
				//print_r($tempdata);
			}
			
			//for histogram
			if ($histogramcounter==1){
				$q=($allmax-$allmin)/4;
				if ($dat["Price"]<=$q){
					$zone1++;
				}
				else if ($dat["Price"]>$q & $dat["Price"]<=$q*2){
					$zone2++;
				}
				else if ($dat["Price"]>$q*2 & $dat["Price"]<=$q*3){
					$zone3++;
				}
				else if ($dat["Price"]>$q*3 & $dat["Price"]<=$allmax){
					$zone4++;
				}
			}
			
		}
		$histogramcounter=0;
		
		//for box an whisker
		$pusharray=fivenums($tempdata, $yr);
		//print_r($pusharray);
		array_push($fivenumbers, $pusharray);
		
		//reuse array for next year's set of values/prices
		unset($tempdata);
		$tempdata = array();
	}
	
	//for histogram counts
	echo $zone1." ".$zone2." ".$zone3." ".$zone4;
	$histo = array($zone1, $zone2,$zone3,$zone4);
	$_SESSION['histo'] = $histo;

			
	//for box plot 
	$_SESSION['fivenumbers'] = $fivenumbers;

	
function fivenums($tempdata, $yr){
	
		rsort($tempdata);
		$tempcount = count($tempdata);
		//for($x = 0; $x <  $tempcount; $x++) {
			//echo $tempdata[$x];
			//echo "<br>";
		//}
		
		if ($tempcount>=5){
			$min = min($tempdata);
			$max = max($tempdata);
			if ($tempcount%2==0){
				// even number of values, median is the 2 middle numbers / 2
				//echo " even ";
				$medianindex=floor($tempcount/2);
				$median = 0.5*($tempdata[$medianindex-1]+$tempdata[$medianindex]);
				if ($medianindex%2==0){
					$q3index=floor($medianindex/2);
					$q3 = $tempdata[$q3index-1];
					$q1index=$q3index+$medianindex;
					$q1 = $tempdata[$q1index-1];
				}
				else {
					$q3index=ceil($medianindex/2);
					$q3 = $tempdata[$q3index-1];
					$q1index=$q3index+$medianindex;
					$q1 = $tempdata[$q1index-1];
				}
			}
			else if ($tempcount%2==1){
				// odd number of values, median is just the middle number
				//echo "odd ";
				$medianindex=ceil($tempcount/2);
				$median = $tempdata[$medianindex-1];
				if ($medianindex%2==0){
					$q3index=floor($medianindex/2);
					$q3 = $tempdata[$q3index-1];
					$q1index=$q3index+$medianindex;
					$q1 = $tempdata[$q1index-1];
				}
				else {
					$q3index=ceil($medianindex/2);
					$q3 = $tempdata[$q3index-1];
					$q1index=$q3index+$medianindex;
					$q1 = $tempdata[$q1index-1];
				}
			}
			else {
				//echo "this shouldn't happen";
			}
		}
		else{
			if ($tempcount==0) {
				//echo "No values ";
				$min=0;
				$max=0;
				$q1=0;
				$q3=0;
				$median=0;
			}
			else if ($tempcount==1) {
				//echo "1 values ";
				$min=$tempdata[0];
				$max=$tempdata[0];
				$q1=$tempdata[0];
				$q3=$tempdata[0];
				$median=$tempdata[0];
			}
			else if ($tempcount==2) {
				//echo "2 values ";
				$min=$tempdata[1];
				$max=$tempdata[0];
				$q1=$tempdata[0];
				$q3=$tempdata[0];
				$median=$tempdata[0];
			}
			else if ($tempcount==3) {
				//echo "3 values ";
				$min=$tempdata[2];
				$max=$tempdata[0];
				$q1=$tempdata[1];
				$q3=$tempdata[1];
				$median=$tempdata[1];
			}
			else if ($tempcount==4) {
				//echo "4 values ";
				$min=$tempdata[3];
				$max=$tempdata[0];
				$q1=$tempdata[2];
				$q3=$tempdata[1];
				$median=0.5*($q1+$q1);
			}
		}
		//echo "min=".$min." max=".$max." q1=".$q1." q3=".$q3." median=".$median;
		
		$pusharray =array("Year" => $yr, "Min"=>$min, "Max"=>$max, "Q1"=>$q1, "Q3"=>$q3, "Median"=>$median);
		return $pusharray;
}
	
//-----------------------------next lines are for google maps, foreach loop and function need to be together like this to work-----------------
// get latitude, longitude and formatted address

	$geo = array();
	$listingsCount = 0;
	
	foreach($data as $point){
		
		$address = $point["Address"]. ", ". $point["City"]." ". $point["State"];
		$data_arr = geocode($address);
		//print_r($data_arr);
		
		// if able to geocode the address
		if($data_arr){
			 
			$latitude = $data_arr[0];
			$longitude = $data_arr[1];
			array_push($geo, array("lat" => $data_arr[0], "long" => $data_arr[1], "listingNum" => $point["ListNumber"]));
			$listingsCount++;
		}
	}
	
 
// function to geocode address, return false if unable to geocode address
function geocode($address){
 
    // url encode the address
    $address = urlencode($address);
     
    // google map geocode api url
    $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address={$address}";
 
    // get the json response
    $resp_json = file_get_contents($url);
     
    // decode the json
    $resp = json_decode($resp_json, true);
 
    // response status will be 'OK', if able to geocode given address 
    if($resp['status']='OK'){
 
        // get the important data
        $lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        $formatted_address = $resp['results'][0]['formatted_address'];
         
        // verify if data is complete
        if($lati && $longi && $formatted_address){
         
            // put the data in the array
            $data_arr = array();            
             
            array_push(
                $data_arr, 
                    $lati, 
                    $longi, 
                    $formatted_address
                );
             
            return $data_arr;
             
        }else{
            return false;
        }
         
    }else{
        return false;
    }
}
?>

<script
src="http://maps.googleapis.com/maps/api/js">
</script>


<script type="text/javascript">
	
var myCenter=new google.maps.LatLng(40.441069, -79.955874);

function initialize()
{
	var map;
    var bounds = new google.maps.LatLngBounds();
    var mapOptions = {
        mapTypeId: 'roadmap'
    };
                    
		
	map=new google.maps.Map(document.getElementById("googleMap"), mapOptions);
	
	// Markers arrays
	var geo = <?php echo json_encode($geo); ?>;
	var count = <?php echo json_encode($listingsCount); ?>;
        
    // Display multiple markers on a map
    var infoWindow = new google.maps.InfoWindow(), marker, i;
    
    // Loop through our array of markers & place each one on the map  
    for( i = 0; i < geo.length; i++ ) {
	
		//alert(geo[i]["lat"]);
        var position = new google.maps.LatLng(geo[i]["lat"], geo[i]["long"]);
        bounds.extend(position);
        marker = new google.maps.Marker({
            position: position,
            map: map
        });
        
        // Allow each marker to have an info window    
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infoWindow.setContent("Listing#: " + geo[i]["listingNum"]);
                infoWindow.open(map, marker);
            }
        })(marker, i)); 

        // Automatically center the map fitting all markers on the screen
        map.fitBounds(bounds);
    }

    // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
    var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
        this.setZoom(14);
        google.maps.event.removeListener(boundsListener);
    });
	
	
}
google.maps.event.addDomListener(window, 'load', initialize);


</script>
</body>

</html>