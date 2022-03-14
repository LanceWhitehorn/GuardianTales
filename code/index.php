<html>

<?php
	include ('header.php');
	include ('functions.php');
	userIP(1);
?>

<body onload="csrf();">
	<fieldset>
		<center>
			<b>Filter: </b>
			<label><input type="radio" id="filter" name="filter" value="raid" onchange="filter('raid')"  />Raid</label>
			<label><input type="radio" id="filter" name="filter" value="guild" onchange="filter('guild')" />Guild</label>
			<label><input type="radio" id="filter" name="filter" value="veg_raids" onchange="filter('veg_raids')" />Veg Raids</label>
			<label><input type="radio" id="filter" name="filter" value="members" onchange="filter('members')" />Veg Members</label>
			<br /><br />

			<select name="dropdown" id="dropdown" onchange="showTable(this.value)" disabled="true">
				<option>Select an option above</option>
			</select>

			<input type="hidden" id="csrf_token" />

			<input type="button" onclick="customReset();" name="reset" value="Reset" class="button" />
			<br /><br />

			<span class="highlight">Note:</span> I started recording top 100 since Forbidden Forest (Sep 2021)
		</center>
	</fieldset>

	<div id="data"></div>
</body>

</html>