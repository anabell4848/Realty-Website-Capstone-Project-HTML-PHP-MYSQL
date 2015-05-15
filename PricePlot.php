<?php // content="text/plain; charset=utf-8"
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_stock.php');
 
	if(!isset($_SESSION)){
		session_start();
	}
	$data = $_SESSION['data'];
	//print_r($data);
	$fivenumbers = $_SESSION['fivenumbers'];
	
	$datay = array();
	$datax = array();
	foreach($fivenumbers as $num){
		array_push($datay, $num["Q1"], $num["Q3"], $num["Min"], $num["Max"], $num["Median"]);
		array_push($datax, $num["Year"]);
	}
// Data must be in the format : q1,q3,min,max,median
/*$datay = array(
	0,0,0,0,0,
	1,1,1,1,1,
	4,4,4,7,4,
	5,5,4,6,5,
    27,45,27,45,36,
    55,25,14,59,40,
    15,40,12,47,23,
    62,38,25,65,57,
    38,49,32,64,45);
$datax = array(-5,-4,-3,-2,-1,0,1,2,3);*/
 
// Setup a simple graph
$graph = new Graph(600,450);
$graph->SetScale('textlin');
$graph->SetMarginColor('lightblue');
$graph->title->Set('Box Plot for Prices vs Years');
$graph->yaxis->title->Set('Price');
$graph->xaxis->title->Set('Years');
$graph->xaxis->SetTickLabels($datax);
 
// Adjust the margin 
$graph->SetMargin(50,30,30,50);

// Create a new stock plot
$p1 = new BoxPlot($datay);
 
// Width of the bars (in pixels)
$p1->SetWidth(13);
 
// Uncomment the following line to hide the horizontal end lines
//$p1->HideEndLines();
 
// Add the plot to the graph and send it back to the browser
$graph->Add($p1);
$graph->Stroke();
 
?>