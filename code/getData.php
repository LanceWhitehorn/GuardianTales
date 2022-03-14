<?php

include ('functions.php'); // CSRF and userIP functions
checkCSRF();

// Connect to the database
require_once ('mysql_connect.php');

$id = $_GET['id'];
$filter = $_GET['filter'];

function checkRows($result) {
  $error = "<p>No data</p>";
  if (mysqli_num_rows($result) == 0) {
    echo $error;
    die();
  }
}

if ($filter == "raid") {
/*
  $sql = "SELECT curr.rank, curr.name, curr.damage, (CAST(prev.rank AS SIGNED)-CAST(curr.rank AS SIGNED)) AS diff, (curr.damage/prev.damage-1)*100 AS growth FROM
            (SELECT gr.guild_id, ROW_NUMBER() OVER(PARTITION BY raid_id ORDER BY damage DESC) AS `rank`, name, damage
                  FROM guild_rankings gr, guilds g
                  WHERE gr.guild_id = g.guild_id AND raid_id = $id) AS curr,
            (SELECT gr.guild_id, ROW_NUMBER() OVER(PARTITION BY raid_id ORDER BY damage DESC) AS `rank`, name, damage
                  FROM guild_rankings gr, guilds g
                  WHERE gr.guild_id = g.guild_id AND raid_id = $id-1) AS prev
            WHERE curr.guild_id = prev.guild_id
            ORDER BY curr.damage DESC";
*/
  $sql = "SELECT curr.rank, curr.name, curr.damage, IFNULL(CAST(prev.rank AS SIGNED)-CAST(curr.rank AS SIGNED), '-') AS diff, IFNULL((curr.damage/prev.damage-1)*100, '-') AS growth FROM
            (SELECT gr.guild_id, ROW_NUMBER() OVER(PARTITION BY raid_id ORDER BY damage DESC) AS `rank`, name, damage
                  FROM guild_rankings gr, guilds g
                  WHERE gr.guild_id = g.guild_id AND raid_id = $id) AS curr
            LEFT JOIN
                  (SELECT gr.guild_id, ROW_NUMBER() OVER(PARTITION BY raid_id ORDER BY damage DESC) AS `rank`, name, damage
                        FROM guild_rankings gr, guilds g
                        WHERE gr.guild_id = g.guild_id AND raid_id = $id-1) AS prev
                  ON curr.guild_id = prev.guild_id
            ORDER BY curr.damage DESC";
  $result = mysqli_query($dbc, $sql);
  checkRows($result);

  $sql = "SELECT * FROM raids WHERE raid_id = $id";
  $run = mysqli_query($dbc, $sql);
  $raid = mysqli_fetch_array($run);
  $end_day = date('d', strtotime($raid['end']));
  if ($end_day>=10) {
    $month = date('F Y', strtotime($raid['end']));
  }
  if ($end_day<10) {
    $month = date('F Y', strtotime($raid['start']));
    $month = "End-$month";
  }
  echo "<p><b>$month</b></p>";
  # echo "<p><b>$month:</b> {$raid['name']}</p>";

  echo "<center>";
  echo "<table class='styled-table' width='95%' id='myTable'>
    <thead>
      <tr>
        <th><a href='#' onclick=\"SortTable(0, 'N'); return false;\">Rank</a></th>
        <th style='white-space:nowrap'><a href='#' onclick=\"SortTable(1, 'T'); return false;\">Guild</a></th>
        <th><a href='#' onclick=\"SortTable(2, 'N'); return false;\">Damage</th>
        <th><a href='#' onclick=\"SortTable(3, 'N'); return false;\">+/-</a></th>
        <th style='white-space:nowrap'><a href='#' onclick=\"SortTable(4, 'N'); return false;\">Growth (%)</a></th>
      </tr>
    </thead>";
    echo "<tbody>";
      while($row = mysqli_fetch_array($result)) {
        echo "<tr>";
    	    echo "<td>" . $row['rank'] . "</td>";
    	    echo "<td>" . $row['name'] . "</td>";
    	    echo "<td>" . number_format($row['damage']) . "</td>";
          $rank_diff = $row['diff'];
          if ($rank_diff >= 0 && is_numeric($rank_diff)) {
            echo "<td>" . $rank_diff . "</td>";
          } elseif ($rank_diff < 0 && is_numeric($rank_diff)) {
            echo "<td style='color:#D51B1B'>" . $rank_diff . "</td>";
          } else {
            echo "<td>-</td>";
          }
          $growth = $row['growth'];
          if ($growth > 0 && is_numeric($growth)) {
            echo "<td>" . number_format($growth, 2) . "</td>";
          } elseif ($growth < 0 && is_numeric($growth)) {
            echo "<td style='color:#D51B1B'>" . number_format($growth, 2) . "</td>";
          } else {
            echo "<td>-</td>";
          }
        echo "</tr>";
      }
    echo "</tbody>";
  echo "</table>";
  echo "</center>";
  mysqli_free_result($result);
}

else if ($filter == "guild") {
  // This was a very helpful idea to calculate the % change from a previous row value!!
  // https://stackoverflow.com/questions/30484373/calculate-percent-increase-decrease-from-previous-row-value
  $sql = "SELECT IF(DAY(r.end)>=10, DATE_FORMAT(r.end, '%b'), CONCAT('End-', DATE_FORMAT(r.start, '%b'))) AS month, r.name, temp.rank, base.damage,
            (damage / (SELECT damage FROM guild_rankings WHERE raid_id < base.raid_id AND guild_id=$id ORDER BY raid_id DESC LIMIT 1)-1)*100 AS growth
            FROM
                  (SELECT raid_id, guild_id, damage FROM guild_rankings WHERE guild_id=$id) AS base,
                  (SELECT raid_id, guild_id, ROW_NUMBER() OVER(PARTITION BY raid_id ORDER BY damage DESC) AS `rank` FROM guild_rankings) AS temp,
                  raids AS r
              WHERE base.raid_id = temp.raid_id AND base.guild_id = temp.guild_id
              AND base.raid_id = r.raid_id";
  $result = mysqli_query($dbc, $sql);
  checkRows($result);

  $sql = "SELECT name FROM guilds WHERE guild_id = $id";
  $run = mysqli_query($dbc, $sql);
  $guild = mysqli_fetch_array($run)['name'];
  # echo "<p><b>$guild</b></p>";

  echo "<center>";
  echo "<table class='styled-table' width='95%' id='myTable'>
    <thead>
      <tr>
        <th style='white-space:nowrap'><a href='#' onclick=\"SortTable(0, 'D', 'dmy'); return false;\">Month</a></th>
        <th>Raid</th>
        <th>Rank</th>
        <th>Damage</th>
        <th style='white-space:nowrap'>Growth (%)</th>
      </tr>
    </thead>";
    echo "<tbody>";
      while($row = mysqli_fetch_array($result)) {
        echo "<tr>";
          echo "<td>" . $row['month'] . "</td>";
          # echo "<td>" . date("d-m-y", strtotime($row['start'])) . "</td>";
          echo "<td>" . $row['name'] . "</td>";
          echo "<td>" . $row['rank'] . "</td>";
          echo "<td>" . number_format($row['damage']) . "</td>";
          $growth = $row['growth'];
          if ($growth > 0 && is_numeric($growth)) {
            echo "<td>" . number_format($growth, 2) . "</td>";
          } elseif ($growth < 0 && is_numeric($growth)) {
            echo "<td style='color:#D51B1B'>" . number_format($growth, 2) . "</td>";
          } else {
            echo "<td>-</td>";
          }
        echo "</tr>";
      }
    echo "</tbody>";
  echo "</table>";
  echo "</center>";
}

else if ($filter == "members") {
  $sql = "SELECT IF(DAY(r.end)>=10, DATE_FORMAT(r.end, '%b'), CONCAT('End-', DATE_FORMAT(r.start, '%b'))) AS month, r.start, r.name, m1.damage,
            (m1.damage / (SELECT damage FROM member_rankings m2 WHERE m2.raid_id < m1.raid_id AND user_id = $id ORDER BY m2.raid_id DESC LIMIT 1) - 1) * 100 AS growth, temp.rank
            FROM member_rankings AS m1, raids AS r, (SELECT *, ROW_NUMBER() OVER(PARTITION BY raid_id ORDER BY damage DESC) AS 'rank' FROM member_rankings) AS temp
            WHERE m1.raid_id = r.raid_id AND m1.raid_id = temp.raid_id AND m1.user_id = temp.user_id AND m1.user_id = $id
            ORDER BY m1.raid_id";
  $result = mysqli_query($dbc, $sql);
  checkRows($result);

  echo "<center>";
  echo "<table class='styled-table' width='95%' id='myTable'>
    <thead>
      <tr>
        <th style='white-space:nowrap'><a href='#' onclick=\"SortTable(0, 'D', 'm'); return false;\">Month</a></th>
        <th>Raid</th>
        <th style='white-space:nowrap'>Damage</th>
        <th style='white-space:nowrap'>Growth (%)</th>
        <th style='white-space:nowrap'>Rank</th>
      </tr>
    </thead>";
    echo "<tbody>";
      while($row = mysqli_fetch_array($result)) {
        echo "<tr>";
          echo "<td>" . $row['month'] . "</td>";
          echo "<td>" . $row['name'] . "</td>";
          echo "<td>" . number_format($row['damage']) . "</td>";
          $growth = $row['growth'];
          if ($growth > 0 && is_numeric($growth)) {
            echo "<td>" . number_format($growth, 2) . "</td>";
          } elseif ($growth < 0 && is_numeric($growth)) {
            echo "<td style='color:#D51B1B'>" . number_format($growth, 2) . "</td>";
          } else {
            echo "<td>-</td>";
          }
          echo "<td>" . $row['rank'] . "</td>";
        echo "</tr>";
      }
    echo "</tbody>";
  echo "</table>";
  echo "</center>";
}

else if ($filter == "veg_raids") {

  // Display the total damage
  $sql = "SELECT  (SELECT SUM(mr1.damage) FROM member_rankings mr1 WHERE raid_id=$id) AS `curr_total`,
                  (SELECT SUM(damage) FROM member_rankings WHERE raid_id=$id-1) AS `prev_total`";
  $result = mysqli_query($dbc, $sql);
  $row = mysqli_fetch_array($result);
  if (!is_null($row['curr_total'])) {
    $curr_total = $row['curr_total'];
    $prev_total = $row['prev_total'];
    if (isset($curr_total) && isset($prev_total)) {
      $growth = number_format(($curr_total/$prev_total-1)*100, 2);
      if ($growth > 0) {
        $icon = "<i class='fa fa-angle-double-up' style='color:#51D51B'></i> $growth%";
      } else if ($growth < 0) {
        $icon = "<i class='fa fa-angle-double-down' style='color:#D51B1B'></i> $growth%";
      }
      echo "<p><b>Total:</b> " . number_format($curr_total) . " $icon</p>";
    } else {
      echo "<p><b>Total:</b> " . number_format($curr_total);
    }
  }

  // Get the table
  $sql = "SELECT ROW_NUMBER() OVER(PARTITION BY mr1.raid_id ORDER BY mr1.damage DESC) AS `rank`, m.name, mr1.damage, (mr1.damage/mr2.damage-1)*100 AS growth
            FROM member_rankings mr1
              LEFT JOIN (SELECT * FROM member_rankings WHERE raid_id=$id-1) AS mr2
                ON mr1.user_id = mr2.user_id
            JOIN members m
                ON mr1.user_id = m.user_id
            WHERE mr1.raid_id = $id
            ORDER BY mr1.damage DESC";
  $result = mysqli_query($dbc, $sql);
  checkRows($result);

  echo "<center>";
  echo "<table class='styled-table' width='95%' id='myTable'>
    <thead>
      <tr>
        <th style='white-space:nowrap'><a href='#' onclick=\"SortTable(0, 'N'); return false;\">Rank</a></th>
        <th>Name</th>
        <th style='white-space:nowrap'>Damage</th>
        <th style='white-space:nowrap'><a href='#' onclick=\"SortTable(3, 'N'); return false;\">Growth (%)</a></th>
      </tr>
    </thead>";
    echo "<tbody>";
      while($row = mysqli_fetch_array($result)) {
        echo "<tr>";
          echo "<td>" . $row['rank'] . "</td>";
          echo "<td>" . $row['name'] . "</td>";
          echo "<td>" . number_format($row['damage']) . "</td>";
          $growth = $row['growth'];
          if ($growth > 0 && is_numeric($growth)) {
            echo "<td>" . number_format($growth, 2) . "</td>";
          } elseif ($growth < 0 && is_numeric($growth)) {
            echo "<td style='color:#D51B1B'>" . number_format($growth, 2) . "</td>";
          } else {
            echo "<td>-</td>";
          }
        echo "</tr>";
      }
    echo "</tbody>";
  echo "</table>";
  echo "</center>";
}

?>