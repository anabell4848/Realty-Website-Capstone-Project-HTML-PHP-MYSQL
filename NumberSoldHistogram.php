<?php // content="text/plain; charset=utf-8"
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_bar.php');

	if(!isset($_SESSION)){
		session_start();
	}
	$data = $_SESSION['data'];
	//print_r($data);
	$allfivenumbers = $_SESSION['allfivenumbers'];
	$histo = $_SESSION['histo'];
	
	$datay=$histo;
	$datax=array();
	foreach($allfivenumbers as $num){
		array_push($datax, $num["Min"]."-".$num["Q1"], $num["Q1"]."-".$num["Median"], $num["Median"]."-".$num["Q3"],$num["Q3"]."-".$num["Max"]);
	}
	//$datax=$allfivenumbers;
 
//$datay=array(12,8,19,3,10);
//$datax = array(2,3,4,5,6,7);
 
// Create the graph. These two calls are always required
$graph = new Graph(600,450);
$graph->SetScale('textlin');
 
// Add a drop shadow
$graph->SetShadow();
 
// Adjust the margin 
$graph->SetMargin(50,30,30,50);
 
// Create a bar pot
$bplot = new BarPlot($datay);
 
// Adjust fill color
$bplot->SetFillColor('orange');
$graph->Add($bplot);
 
// Setup the titles
$graph->title->Set('Number of Listings Sold vs Price Histogram');
$graph->yaxis->title->Set('Number of Listings Sold');
$graph->xaxis->title->Set('Price');
$graph->xaxis->SetTickLabels($datax);
 
 
// Display the graph
$graph->Stroke();
?>