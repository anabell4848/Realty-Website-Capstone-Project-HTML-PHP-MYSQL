<?php
/*
inserts real estate data from text files into database tables

*/
?>
<!DOCTYPE html>
<html>
 <head>
  <title>Script to Initialize Real Estate Tables</title>
 </head>
 <body>
 <?php
	
	$servername = "localhost";
	$username = "hax12"; 
	$password = "123";
	$dbname = "RealEstateDB";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) 
	{
		die("Connection failed: " . $conn->connect_error);
	} 


	//insert files into table
	$readLine = file("listings__2014_08_26_07_35_13.txt");
	insert_file($readLine, $conn);
	
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) 
	{
		die("Connection failed: " . $conn->connect_error);
	} 
	$readLine = file("listings__2014_09_09_14_02_01.txt");
	insert_file($readLine, $conn);
	

function insert_file($readLine, $conn){
	$readLine = array_slice($readLine, 0);
	
	foreach ($readLine as $line):
		$line = rtrim($line);
		//remove commas in quotes
		$pattern='/"(\d+),(\d+)"/';
		$replacement='${1}$2';
		$line = preg_replace($pattern, $replacement, $line );

		//remove all non-alphanumeric symbols except the slash from the date
		$line = preg_replace("/[^A-Za-z0-9,\/ ]/",'',$line);
		echo $line, "<br>";
		$chunks = explode(",", $line);
		//change mm/dd/yyyy to YYYY-MM-DD
		$time=strtotime($chunks[17]);
		$chunks[17] = date('Y-m-d',$time);
		
		//make all empty fields equal to NULL
		foreach ($chunks as $achunk):
			echo $achunk, " ";
			if (ctype_space($achunk))
			{
				$achunk = null;
				echo $achunk, " ";
			}
		endforeach;
		echo "<br>";
		
		//insert into tables
		$sql = "INSERT INTO House_Listing (List_Number, Agent_ID, Status, Price, Sold_Price, Closing_Date)
		VALUES ('$chunks[4]','$chunks[8]','$chunks[0]','$chunks[6]', '$chunks[16]','$chunks[17]')";
		if ($conn->query($sql) === TRUE) 
		{
			echo "New row House_Listing created<br>";
		} 
		else 
		{
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		
		$sql = "INSERT INTO House_Location (List_Number, Address, County, Area, Area_Name)
		VALUES ('$chunks[4]','$chunks[5]','$chunks[1]', '$chunks[2]','$chunks[3]')";
		if ($conn->query($sql) === TRUE) 
		{
			echo "New row House_Location created<br>";
		} 
		else 
		{
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		
		$sql = "INSERT INTO House_Details (List_Number, Years_Old, Beds, F_Baths, P_Baths, Style)
		VALUES ('$chunks[4]','$chunks[7]','$chunks[9]', '$chunks[10]','$chunks[11]','$chunks[12]')";
		if ($conn->query($sql) === TRUE) 
		{
			echo "New row House_Details created<br>";
		} 
		else 
		{
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		
		$sql = "INSERT INTO Agents_Directory (Agent_ID, Contact, Contact_Phone, Office)
		VALUES ('$chunks[8]','$chunks[13]','$chunks[14]', '$chunks[15]')";
		if ($conn->query($sql) === TRUE) 
		{
			echo "New row Agents_Directory created<br>";
		} 
		else 
		{
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		
		$sql = "INSERT INTO Sold_Info (List_Number, Sold_Price, Closing_Date, Selling_Agent_ID, Selling_Office_ID)
		VALUES ('$chunks[4]','$chunks[16]','$chunks[17]', '$chunks[18]','$chunks[19]')";
		if ($conn->query($sql) === TRUE) 
		{
			echo "New row Sold_Info created<br>";
		} 
		else 
		{
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
	endforeach;

}
	
?>
 </body>
</html>
