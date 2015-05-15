<?php
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);
require_once 'config.php';

if(!isset($_SESSION)){
    session_start();
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Real Estate Search</title>
		<link rel="stylesheet" href="real_estate_575/templatemo_style.css" type="text/css">
		
		<script type="text/javascript">
			<!--
			function toggle(id)
			{
				var block = document.getElementById(id);
				if(block.style.display == 'none')
				{
					block.style.display = 'block';
				}
				else
				{
					block.style.display = 'none';
				}
			}
			function addFilter(select)
			{
				var filter = document.getElementById(select).value;
				if(filter == "")
				{
					return
				}
				var filterInput = document.getElementById('filterInput');
				var filterDisplay = document.getElementById('filterDisplay');
				filterInput.value += filter + '~';
				var filterSpan = '<span onclick="removeFilter(\'' + filter + '\')">' + filter + '</span><br>';
				filterDisplay.innerHTML += filterSpan;
			}
			function removeFilter(filter)
			{
				var filterInput = document.getElementById('filterInput');
				var filterDisplay = document.getElementById('filterDisplay');
				filterInput.value = filterInput.value.replace(filter + "~", '');
				var filterDisplayHTML = filterDisplay.innerHTML.replace('<span onclick="removeFilter(\'' + filter + '\')">' + filter + '</span><br>', '');
				filterDisplay.innerHTML = filterDisplayHTML;
			}
			-->
		</script>
		<style>
			table {
				border-collapse: collapse;
			}
			td, th {
				border: 1px solid #999;
  			padding: 0.5rem;
  			text-align: left;
				vertical-align: top;
			}
			td {
				color: #555;
			}
			table.innerTable {
				border: 0;
			}
			table.innerTable td, table.innerTable th {
				border: 0;
  			padding: 0.2rem;
			}
			table.innerTable th {
				color: #000;
				font-weight: normal;
			}
			td.listNumber {
				vertical-align: middle;
			}
			.error {
				color: red;
				display: block;
			}
		</style>
	</head>
	<body>
		<h1>Search Real Estate Data</h1>
		<form action="filter.php" method="POST">
			<input type="hidden" name="filterInput" id="filterInput" value="<?php echo $_POST['filterInput'] ?>"/>
			<p>
				<?php
				$error = 0; //flag to track form errors
				if($_POST['list'] != "")
				{
					if(!is_numeric($_POST['list']))
					{
						echo '<span class="error">List Number must be a number</span>';
						$error = 1;
					}
				}
				?>
				<label for="list">List #</label>
				<input type="text" name="list" id="list" value="<?php echo @$_POST['list'] ?>"/>
			</p>
			<p>
				<label for="address">Address</label>
				<input type="text" name="address" id="address" value="<?php echo $_POST['address']?>"/>
			</p>
			<p>
				<label for="contactSearch">Contact (Name)</label>
				<input type="text" name="contactSearch" id="contactSearch" value="<?php echo $_POST['contactSearch']?>"/>
			</p>
			<p>
				<?php
				if($_POST['sellingAgentIDSearch'] != "")
				{
					if(!is_numeric($_POST['sellingAgentIDSearch']))
					{
						echo '<span class="error">Selling Agent ID must be a number</span>';
						$error = 1;
					}
				}
				?>
				<label for="sellingAgentIDSearch">Selling Agent ID</label>
				<input type="text" name="sellingAgentIDSearch" id="sellingAgentIDSearch" value="<?php echo $_POST['sellingAgentIDSearch']?>"/>
			</p>
			<p>
				<?php
				if($_POST['officeSearch'] != "")
				{
					if(!is_numeric($_POST['officeSearch']))
					{
						echo '<span class="error">Office must be a number</span>';
						$error = 1;
					}
				}
				?>
				<label for="officeSearch">Office</label>
				<input type="text" name="officeSearch" id="officeSearch" value="<?php echo $_POST['officeSearch'] ?>"/>
			</p>
			<h2>Filters</h2>
			<p>	<b>Status</b><br>
				<label for="sActive">Active</label>
				<input type="checkbox" name="sActive" id="sActive" value="1" <?php echo (isset($_POST['sActive'])) ? 'checked' : '' ?>/>
				<label for="sActive">Contingent</label>
				<input type="checkbox" name="sContingent" id="sContingent" value="1" <?php echo (isset($_POST['sContingent'])) ? 'checked' : '' ?>/>
				<label for="sSold">Sold</label>
				<input type="checkbox" name="sSold" id="sSold" value="1" <?php echo (isset($_POST['sSold'])) ? 'checked' : '' ?>/>
			</p>
			<p><b>Price</b><br>
				<?php
				if($_POST['minPrice'] != "")
				{
					if(!is_numeric($_POST['minPrice']))
					{
						echo '<span class="error">Min price must be a number</span>';
						$error = 1;
					}
				}
				if($_POST['maxPrice'] != "")
				{
					if(!is_numeric($_POST['maxPrice']))
					{
						echo '<span class="error">Max price must be a number</span>';
						$error = 1;
					}
				}
				?>
				<label for="minPrice">Min</label>
				<input type="text" name="minPrice" id="minPrice" value="<?php echo $_POST['minPrice'] ?>"/>
				<label for="maxPrice">Max</label>
				<input type="text" name="maxPrice" id="maxPrice" value="<?php echo $_POST['maxPrice'] ?>"/>
			</p>
			<p><b>Sold Price</b><br>
				<?php
				if($_POST['minSoldPrice'] != "")
				{
					if(!is_numeric($_POST['minSoldPrice']))
					{
						echo '<span class="error">Min sold price must be a number</span>';
						$error = 1;
					}
				}
				if($_POST['maxSoldPrice'] != "")
				{
					if(!is_numeric($_POST['maxSoldPrice']))
					{
						echo '<span class="error">Max sold price must be a number</span>';
						$error = 1;
					}
				}
				?>
				<label for="minSoldPrice">Min</label>
				<input type="text" name="minSoldPrice" id="minSoldPrice" value="<?php echo $_POST['minSoldPrice'] ?>"/>
				<label for="maxSoldPrice">Max</label>
				<input type="text" name="maxSoldPrice" id="maxSoldPrice" value="<?php echo $_POST['maxSoldPrice'] ?>"/>
			</p>
			<p><b>Age</b><br>
				<?php
				if($_POST['minAge'] != "")
				{
					if(!is_numeric($_POST['minAge']))
					{
						echo '<span class="error">Min age must be a number</span>';
						$error = 1;
					}
				}
				if($_POST['maxAge'] != "")
				{
					if(!is_numeric($_POST['maxAge']))
					{
						echo '<span class="error">Max age must be a number</span>';
						$error = 1;
					}
				}
				?>
				<label for="minAge">Min</label>
				<input type="text" name="minAge" id="minAge" value="<?php echo $_POST['minAge'] ?>"/>
				<label for="maxAge">Max</label>
				<input type="text" name="maxAge" id="maxAge" value="<?php echo $_POST['maxAge'] ?>"/>
			</p>
			<p><b>Closing Date (format YYYY-MM-DD)</b><br>
				<?php
				if($_POST['minClosing'] != "")
				{
					if(preg_match('/\d\d\d\d-\d\d-\d\d/', $_POST['minClosing']) === 0)
					{
						echo '<span class="error">Min Closing Date must be in the format YYYY-MM-DD</span>';
						$error = 1;
					}
				}
				if($_POST['maxClosing'] != "")
				{
					if(preg_match('/\d\d\d\d-\d\d-\d\d/', $_POST['maxClosing']) === 0)
					{
						echo '<span class="error">Max Closing Date must be in the format YYYY-MM-DD</span>';
						$error = 1;
					}
				}
				?>
				<label for="minClosing">Min</label>
				<input type="text" name="minClosing" id="minClosing" value="<?php echo $_POST['minClosing'] ?>"/>
				<label for="maxClosing">Max</label>
				<input type="text" name="maxClosing" id="maxClosing" value="<?php echo $_POST['maxClosing'] ?>"/>
			</p>
			<?php
			// Create connection
			$conn = new mysqli($config['hostname'], $config['user'], $config['pass'], $config['database']);
			// Check connection
			if ($conn->connect_error) {
				die("Connection failed: " . $conn->connect_error);
			}
			?>
			<p id="filterDisplay">
				<b>Options (Click options to remove):</b><br>
				<?php
				if(isset($_POST['filterInput']))
				{
					$filters = explode('~', $_POST['filterInput']);
					foreach($filters as $filter)
					{
						if($filter == '')
						{
							continue;
						}
						echo '<span onclick="removeFilter(\'' . $filter . '\')">' . $filter . '</span><br>';
					}
				}
				?>
			</p>
			<p>
				<label for="countyToggle">County<label>
				<input type="checkbox" id="countyToggle" onclick="toggle('countyBox')"/>
			</p>
			<div id="countyBox" style="display:none">
				<select id="county" onchange="addFilter('county')">
					<option></option>
					<?php
					$sql = "SELECT DISTINCT County FROM House_Location ORDER BY County";
					$result = $conn->query($sql);
				 	if($result === FALSE)
					{
						die("Failed to get Counties: " . $conn->error);
					}
					else
					{
						while($row = $result->fetch_array())
						{
							echo "<option value='hlo.County||", $row[0], "'>", $row[0], "</option>";
						}
					}
					?>
				</select>
			</div>
			<p>
				<label for="areaToggle">Area<label>
				<input type="checkbox" id="areaToggle" onclick="toggle('areaBox')"/>
			</p>
			<div id="areaBox" style="display:none">
				<select id="area" onchange="addFilter('area')">
					<option></option>
					<?php
					$sql = "SELECT DISTINCT Area FROM House_Location ORDER BY Area";
					$result = $conn->query($sql);
				 	if($result === FALSE)
					{
						die("Failed to get Areas: " . $conn->error);
					}
					else
					{
						while($row = $result->fetch_array())
						{
							echo "<option value='hlo.Area||", $row[0], "'>", $row[0], "</option>";
						}
					}
					?>
				</select>
			</div>
			<p>
				<label for="areaNameToggle">Area Name<label>
				<input type="checkbox" id="areaNameToggle" onclick="toggle('areaNameBox')"/>
			</p>
			<div id="areaNameBox" style="display:none">
				<select id="areaName" onchange="addFilter('areaName')">
					<option></option>
					<?php
					$sql = "SELECT DISTINCT Area_Name FROM House_Location ORDER BY Area_Name";
					$result = $conn->query($sql);
				 	if($result === FALSE)
					{
						die("Failed to get Area Names: " . $conn->error);
					}
					else
					{
						while($row = $result->fetch_array())
						{
							echo "<option value='hlo.Area_Name||", $row[0], "'>", $row[0], "</option>";
						}
					}
					?>
				</select>
			</div>
			<p>
				<label for="agentIDToggle">Agent ID<label>
				<input type="checkbox" id="agentIDToggle" onclick="toggle('agentIDBox')"/>
			</p>
			<div id="agentIDBox" style="display:none">
				<select id="agentID" onchange="addFilter('agentID')">
					<option></option>
					<?php
					$sql = "SELECT Agent_ID FROM Agents_Directory ORDER BY Agent_ID";
					$result = $conn->query($sql);
				 	if($result === FALSE)
					{
						die("Failed to get Agent IDs: " . $conn->error);
					}
					else
					{
						while($row = $result->fetch_array())
						{
							echo "<option value='ad.Agent_ID||", $row[0], "'>", $row[0], "</option>";
						}
					}
					?>
				</select>
			</div>
			<p>
				<label for="contactToggle">Contact<label>
				<input type="checkbox" id="contactToggle" onclick="toggle('contactBox')"/>
			</p>
			<div id="contactBox" style="display:none">
				<select id="contact" onchange="addFilter('contact')">
					<option></option>
					<?php
					$sql = "SELECT DISTINCT Contact FROM Agents_Directory ORDER BY Contact";
					$result = $conn->query($sql);
				 	if($result === FALSE)
					{
						die("Failed to get Contacts: " . $conn->error);
					}
					else
					{
						while($row = $result->fetch_array())
						{
							echo "<option value='ad.Contact||", $row[0], "'>", $row[0], "</option>";
						}
					}
					?>
				</select>
			</div>
			<p>
				<label for="sellingAgentIDToggle">Selling Agent ID<label>
				<input type="checkbox" id="sellingAgentIDToggle" onclick="toggle('sellingAgentIDBox')"/>
			</p>
			<div id="sellingAgentIDBox" style="display:none">
				<select id="sellingAgentID" onchange="addFilter('sellingAgentID')">
					<option></option>
					<?php
					$sql = "SELECT Agent_ID FROM Agents_Directory ORDER BY Agent_ID";
					$result = $conn->query($sql);
				 	if($result === FALSE)
					{
						die("Failed to get Selling Agent IDs: " . $conn->error);
					}
					else
					{
						while($row = $result->fetch_array())
						{
							echo "<option value='si.Selling_Agent_ID||", $row[0], "'>", $row[0], "</option>";
						}
					}
					?>
				</select>
			</div>
			<p>
				<label for="officeToggle">Office<label>
				<input type="checkbox" id="officeToggle" onclick="toggle('officeBox')"/>
			</p>
			<div id="officeBox" style="display:none">
				<select id="office" onchange="addFilter('office')">
					<option></option>
					<?php
					$sql = "SELECT DISTINCT Office FROM Agents_Directory ORDER BY Office";
					$result = $conn->query($sql);
				 	if($result === FALSE)
					{
						die("Failed to get Offices: " . $conn->error);
					}
					else
					{
						while($row = $result->fetch_array())
						{
							echo "<option value='ad.Office||", $row[0], "'>", $row[0], "</option>";
						}
					}
					?>
				</select>
			</div>
			<p>
				<label for="sellingOfficeToggle">Selling Office<label>
				<input type="checkbox" id="sellingOfficeToggle" onclick="toggle('sellingOfficeBox')"/>
			</p>
			<div id="sellingOfficeBox" style="display:none">
				<select id="sellingOffice" onchange="addFilter('sellingOffice')">
					<option></option>
					<?php
					$sql = "SELECT DISTINCT Office FROM Agents_Directory ORDER BY Office";
					$result = $conn->query($sql);
				 	if($result === FALSE)
					{
						die("Failed to get Selling Office: " . $conn->error);
					}
					else
					{
						while($row = $result->fetch_array())
						{
							echo "<option value='si.Selling_Office_ID||", $row[0], "'>", $row[0], "</option>";
						}
					}
					?>
				</select>
			</div>
			<p>
				<label for="styleToggle">Style<label>
				<input type="checkbox" id="styleToggle" onclick="toggle('styleBox')"/>
			</p>
			<div id="styleBox" style="display:none">
				<select id="style" onchange="addFilter('style')">
					<option></option>
					<?php
					$sql = "SELECT DISTINCT Style FROM House_Details ORDER BY Style";
					$result = $conn->query($sql);
				 	if($result === FALSE)
					{
						die("Failed to get Styles: " . $conn->error);
					}
					else
					{
						while($row = $result->fetch_array())
						{
							echo "<option value='hd.Style||", $row[0], "'>", $row[0], "</option>";
						}
					}
					mysqli_close($conn);
					?>
				</select>
			</div>
			<p>
				<label for="beds">Beds<label>
				<select name="beds" id="beds">
					<option></option>
					<option value="0" <?php echo ($_POST['beds'] === 0) ? 'selected' : '' ?>>0</option>
					<option value="1" <?php echo ($_POST['beds'] == 1) ? 'selected' : '' ?>>1+</option>
					<option value="2" <?php echo ($_POST['beds'] == 2) ? 'selected' : '' ?>>2+</option>
					<option value="3" <?php echo ($_POST['beds'] == 3) ? 'selected' : '' ?>>3+</option>
				</select>
			</p>
			<p>
				<label for="fBaths">Full Baths<label>
				<select name="fBaths" id="fBaths">
					<option></option>
					<option value="0" <?php echo ($_POST['fBaths'] === 0) ? 'selected' : '' ?>>0</option>
					<option value="1" <?php echo ($_POST['fBaths'] == 1) ? 'selected' : '' ?>>1+</option>
					<option value="2" <?php echo ($_POST['fBaths'] == 2) ? 'selected' : '' ?>>2+</option>
					<option value="3" <?php echo ($_POST['fBaths'] == 3) ? 'selected' : '' ?>>3+</option>
				</select>
			</p>
			<p>
				<label for="pBaths">Partial Baths<label>
				<select name="pBaths" id="pBaths">
					<option></option>
					<option value="0" <?php echo ($_POST['pBaths'] === 0) ? 'selected' : '' ?>>0</option>
					<option value="1" <?php echo ($_POST['pBaths'] == 1) ? 'selected' : '' ?>>1+</option>
					<option value="2" <?php echo ($_POST['pBaths'] == 2) ? 'selected' : '' ?>>2+</option>
					<option value="3" <?php echo ($_POST['pBaths'] == 3) ? 'selected' : '' ?>>3+</option>
				</select>
			</p>
			<p>
				<input type="submit" name="search" value="Search"/>
			</p>
		</form>
		<?php
		if(isset($_POST['search']) && !$error)
		{
			ob_start();
			include "results.php";
		}
		
		?>
		
	</body>
</html>