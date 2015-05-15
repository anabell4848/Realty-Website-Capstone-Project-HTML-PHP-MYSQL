<?php
/*
Creates the database and tables

*/
?>

<!DOCTYPE html>
<html>
 <head>
  <title>Script to Initialize Real Estate Database</title>
 </head>
 <body>
 <?php
	// Create connection
	$con=mysqli_connect("localhost","hax12","123");       //USERNAME AND PASSWORD
	// Check connection
	if (mysqli_connect_errno()) 
	{
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	//drop database
	$sql="DROP DATABASE RealEstateDB";
	if (mysqli_query($con,$sql)) 
	{
	  echo "Database RealEstateDB dropped successfully</br>";
	} 
	else {
	  echo "Error dropping database: " . mysqli_error($con);
	}
	
	// Create database
	$sql="CREATE DATABASE RealEstateDB";
	if (mysqli_query($con,$sql)) 
	{
	  echo "Database RealEstateDB created successfully</br>";
	} 
	else 
	{
	  echo "Error creating database: " . mysqli_error($con);
	}
	mysqli_close($con);
	
	// Create connection  
	$con=mysqli_connect("localhost","hax12","123","RealEstateDB");	//USERNAME AND PASSWORD for Salim
	// Check connection
	if (mysqli_connect_errno()) 
	{
	  echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	
	//-----------create tables--------------//
	//Status,County,Area,Area_Name,List_Number,Address,Price,Years_Old,Agent_ID,Beds,F_Baths,P_Baths,Style,Contact,Contact_Phone,Office,Sold_Price,Closing_Date,Agent_ID,Office_ID
	
	// Create Agents_Directory table
	$sql = "CREATE TABLE Agents_Directory 
	(
		Agent_ID			INT NOT NULL, 
		Contact				VARCHAR(20) NOT NULL,
		Contact_Phone		VARCHAR(15) NOT NULL,
		Office				INT NOT NULL,
		
		PRIMARY KEY (Agent_ID)
	)ENGINE=INNODB";
	//check creation
	if (mysqli_query($con,$sql)) 
	{ 
		echo "Table Agents_Directory created successfully</br>"; 
	} 
	else 
	{  
		echo "Error creating Agents_Directory table: " . mysqli_error($con). "</br>";
	}
	
	
	// Create House_Listing table	//change mm/dd/yyyy to YYYY-MM-DD
	$sql = "CREATE TABLE House_Listing
	(
		List_Number			INT NOT NULL,
		Agent_ID			INT NOT NULL, 
		Status				VARCHAR(10) NOT NULL,
		Price				INT NOT NULL,
		Sold_Price			INT,
		Closing_Date		DATE, 			
		
		PRIMARY KEY (List_Number),
		INDEX (Agent_ID)
		
	)ENGINE=INNODB";
	//check creation
	if (mysqli_query($con,$sql)) 
	{  
		echo "Table House_Listing created successfully</br>";
	} 
	else 
	{  
		echo "Error creating House_Listing table: " . mysqli_error($con). "</br>";
	}
	
	/*//add House_Listing foreign keys 
	$sql = "ALTER TABLE House_Listing
		ADD CONSTRAINT 
		FOREIGN KEY(Agent_ID)
			REFERENCES Agents_Directory(Agent_ID)";
	if (mysqli_query($con,$sql)) 
	{  
		echo "Table House_Listing ALTERED successfully</br>";
	} 
	else 
	{  
		echo "Error ALTERING House_Listing table: " . mysqli_error($con). "</br>";
	}*/
	
	
	// Create House_Location table
	$sql = "CREATE TABLE House_Location 
	(
		List_Number			INT NOT NULL,
		Address				VARCHAR(20) NOT NULL,
		County				VARCHAR(5) NOT NULL,
		Area				INT NOT NULL, 
		Area_Name			VARCHAR(20) NOT NULL,
		
		PRIMARY KEY(List_Number)
	)ENGINE=INNODB";
	//check creation
	if (mysqli_query($con,$sql)) 
	{  
		echo "Table House_Location created successfully</br>";
		} 
	else 
	{  
		echo "Error creating House_Location table: " . mysqli_error($con). "</br>";
	}
	
	/*//add House_Location foreign keys 
	$sql = "ALTER TABLE House_Location
		ADD CONSTRAINT 
		FOREIGN KEY (List_Number)
			REFERENCES House_Listing(List_Number)";
	if (mysqli_query($con,$sql)) 
	{ 
		echo "Table House_Location ALTERED successfully</br>";
	} 
	else 
	{  
		echo "Error ALTERING House_Location table: " . mysqli_error($con). "</br>";
	}*/
	
	
	// Create House_Details table
	$sql = "CREATE TABLE House_Details 
	(
		List_Number			INT NOT NULL,
		Years_Old			INT,
		Beds				INT,	
		F_Baths				INT,
		P_Baths				INT,
		Style				VARCHAR(10),
		
		PRIMARY KEY (List_Number)
	)ENGINE=INNODB";
	// check creation
	if (mysqli_query($con,$sql)) 
	{  
		echo "Table House_Details created successfully</br>";
	} 
	else 
	{  
		echo "Error creating House_Details table: " . mysqli_error($con). "</br>";
	}
	
	/*//add House_Details foreign keys 
	$sql = "ALTER TABLE House_Details
		ADD CONSTRAINT 
		FOREIGN KEY (List_Number)
			REFERENCES House_Listing(List_Number)";
	if (mysqli_query($con,$sql)) 
	{  
		echo "Table House_Details ALTERED successfully</br>";
	} 
	else 
	{  
		echo "Error ALTERING House_Details table: " . mysqli_error($con). "</br>";
	}*/
	
	
	// Create Sold_Info table
	$sql = "CREATE TABLE Sold_Info 
	(
		List_Number			INT NOT NULL,
		Sold_Price			INT,
		Closing_Date		DATE,
		Selling_Agent_ID	INT,
		Selling_Office_ID	INT,
		
		PRIMARY KEY (List_Number)
	)ENGINE=INNODB";
	// check creation
	if (mysqli_query($con,$sql)) 
	{  
		echo "Table Sold_Info created successfully</br>";
	} 
	else 
	{  
		echo "Error creating Sold_Info table: " . mysqli_error($con). "</br>";
	}
	
	/*//add Sold_Info foreign keys 
	$sql = "ALTER TABLE Sold_Info
		ADD CONSTRAINT 
		FOREIGN KEY (List_Number, Sold_Price)
			REFERENCES House_Listing(List_Number, Sold_Price)";
	if (mysqli_query($con,$sql)) 
	{  
		echo "Table Sold_Info ALTERED successfully</br>";
	} 
	else 
	{  
		echo "Error ALTERING Sold_Info table: " . mysqli_error($con). "</br>";
	}	
	*/

?>
 </body>
</html>
