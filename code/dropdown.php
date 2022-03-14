<?php

include ('functions.php'); // CSRF and userIP functions
checkCSRF();

// Connect to the database
require_once ('mysql_connect.php');

$filter = $_GET['filter'];

if ($filter=="raid") {
  $sql = "SELECT * FROM raids ORDER BY raid_id DESC";
  $result = mysqli_query($dbc, $sql);
  echo "<option value=''>- Select Raid -</option>";
  foreach($result as $row) {
    echo "<option value='{$row['raid_id']}'>{$row['name']}</option>";
  }
} elseif ($filter=="guild") {
  $sql = "SELECT DISTINCT guild_id, name FROM guilds ORDER BY name";
  $result = mysqli_query($dbc, $sql);
  echo "<option value=''>- Select Guild -</option>";
  foreach($result as $row) {
    echo "<option value='{$row['guild_id']}'>{$row['name']}</option>";
  }
} elseif ($filter=="members") {
  $sql = "SELECT DISTINCT user_id, name FROM members ORDER BY name";
  $result = mysqli_query($dbc, $sql);
  echo "<option value=''>- Select Member -</option>";
  foreach($result as $row) {
    echo "<option value='{$row['user_id']}'>{$row['name']}</option>";
  }
} elseif ($filter=="veg_raids") {
  $sql = "SELECT * FROM raids ORDER BY raid_id DESC";
  $result = mysqli_query($dbc, $sql);
  echo "<option value=''>- Select Raid -</option>";
  foreach($result as $row) {
    echo "<option value='{$row['raid_id']}'>{$row['name']}</option>";
  }
}

?>